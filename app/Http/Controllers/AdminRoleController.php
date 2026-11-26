<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\BcryptPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminRoleController extends Controller
{
    private const ROLE_IDS = [User::CUSTOMER_ROLE, User::ADMIN_ROLE];

    public function index(Request $request): View|RedirectResponse
    {
        $users = DB::table('users')->join('roles', 'roles.role_id', '=', 'users.role_id')
            ->orderBy('users.last_name')->orderBy('users.first_name')->orderBy('users.user_id')
            ->paginate(12, ['users.user_id', 'users.first_name', 'users.last_name',
                'users.email', 'users.role_id', 'roles.name as role']);

        if ($users->currentPage() > $users->lastPage()) {
            return redirect()->to($users->url($users->lastPage()));
        }

        return view('admin.users.index', [
            'users' => $users,
            'adminId' => $request->user()->getKey(),
        ]);
    }

    public function edit(Request $request, string $userId): View
    {
        $user = User::query()->findOrFail($userId, ['user_id', 'first_name', 'last_name', 'email', 'role_id']);
        abort_if($user->is($request->user()), 403);

        return view('admin.users.role', [
            'user' => $user,
            'roles' => DB::table('roles')->whereIn('role_id', self::ROLE_IDS)
                ->orderBy('role_id')->get(['role_id', 'name']),
        ]);
    }

    public function update(Request $request, string $userId): RedirectResponse
    {
        $data = $request->validate([
            'role_id' => ['bail', 'required', 'integer', Rule::in(self::ROLE_IDS), 'exists:roles,role_id'],
            'current_password' => ['required', 'string', new BcryptPassword],
        ], [
            'role_id.required' => 'Выбери роль из списка.',
            'role_id.integer' => 'Выбери роль из списка.',
            'role_id.in' => 'Выбери роль из списка.',
            'role_id.exists' => 'Выбери роль из списка.',
            'current_password.required' => 'Введи свой текущий пароль.',
        ]);

        $adminId = $request->user()->getKey();

        $changed = DB::transaction(function () use ($data, $adminId, $userId): bool {
            // Serialize role edits so a demoted administrator cannot finish another role change.
            DB::table('roles')->where('role_id', User::ADMIN_ROLE)->lockForUpdate()->first();
            $admin = User::query()->lockForUpdate()->findOrFail($adminId);
            abort_unless((int) $admin->role_id === User::ADMIN_ROLE, 403);

            $user = User::query()->lockForUpdate()->findOrFail($userId);
            abort_if($user->is($admin), 403);

            if (! Hash::check($data['current_password'], $admin->getAuthPassword())) {
                throw ValidationException::withMessages(['current_password' => 'Неверный текущий пароль.']);
            }

            $roleId = (int) $data['role_id'];
            if ((int) $user->role_id === $roleId) {
                return false;
            }

            $user->update(['role_id' => $roleId]);

            return true;
        }, 3);

        return to_route('admin.users.index', [], 303)
            ->with('status', $changed ? 'Роль пользователя изменена.' : 'У пользователя уже назначена эта роль.');
    }
}
