<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\BcryptPassword;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return response()->view('profile', ['user' => $request->user()])
            ->header('Cache-Control', 'no-store');
    }

    public function update(Request $request): RedirectResponse
    {
        $userId = $request->user()->getKey();
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => mb_strtolower(trim($email))]);
        }

        $data = $request->validate([
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:254', Rule::unique('users')->ignore($userId, 'user_id')],
            'phone' => ['required', 'string', 'max:32', 'regex:/^[+0-9][0-9 ().-]{5,31}$/'],
            'current_password' => ['nullable', 'string', new BcryptPassword],
        ], [
            'firstName.required' => 'Укажи имя.',
            'firstName.max' => 'Имя должно содержать не более 100 символов.',
            'lastName.required' => 'Укажи фамилию.',
            'lastName.max' => 'Фамилия должна содержать не более 100 символов.',
            'email.required' => 'Укажи адрес электронной почты.',
            'email.email' => 'Проверь формат адреса электронной почты.',
            'email.max' => 'Адрес электронной почты слишком длинный.',
            'email.unique' => 'Этот адрес электронной почты уже используется.',
            'phone.required' => 'Укажи номер телефона.',
            'phone.max' => 'Номер телефона слишком длинный.',
            'phone.regex' => 'Проверь формат номера телефона.',
        ]);

        try {
            DB::transaction(function () use ($userId, $data): void {
                $user = User::query()->lockForUpdate()->findOrFail($userId);
                $emailChanged = $user->email !== $data['email'];

                if ($emailChanged && ! Hash::check($data['current_password'] ?? '', $user->getAuthPassword())) {
                    throw ValidationException::withMessages([
                        'current_password' => 'Для смены электронной почты введи текущий пароль.',
                    ]);
                }

                $user->update([
                    'first_name' => $data['firstName'],
                    'last_name' => $data['lastName'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ]);

                if ($emailChanged) {
                    $now = now();
                    DB::table('password_reset_tokens')->where('user_id', $userId)
                        ->whereNull('used_at')->where('expires_at', '>', $now)
                        ->update(['expires_at' => $now]);
                }
            }, 3);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'email' => 'Этот адрес электронной почты уже используется.',
            ]);
        }

        return to_route('profile.edit', [], 303)->with('status', 'Данные профиля сохранены.');
    }
}
