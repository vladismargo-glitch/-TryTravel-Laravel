<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $data = $request->validate([
            'lastName' => ['required', 'string', 'max:100'],
            'firstName' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^[+0-9][0-9 ().-]{5,31}$/'],
            'password' => ['required', 'string', 'min:8', function (string $attribute, mixed $value, Closure $fail): void {
                if (is_string($value) && strlen($value) > 72) {
                    $fail('Пароль слишком длинный.');
                }
                if (is_string($value) && str_contains($value, "\0")) {
                    $fail('Пароль содержит недопустимый символ.');
                }
            }],
            'agree' => ['accepted'],
        ], [
            'lastName.required' => 'Укажи фамилию.',
            'lastName.max' => 'Фамилия должна содержать не более 100 символов.',
            'firstName.required' => 'Укажи имя.',
            'firstName.max' => 'Имя должно содержать не более 100 символов.',
            'email.required' => 'Укажи адрес электронной почты.',
            'email.email' => 'Проверь формат адреса электронной почты.',
            'email.max' => 'Адрес электронной почты слишком длинный.',
            'email.unique' => 'Этот адрес электронной почты уже используется.',
            'phone.required' => 'Укажи номер телефона.',
            'phone.regex' => 'Проверь формат номера телефона.',
            'phone.max' => 'Номер телефона слишком длинный.',
            'password.required' => 'Укажи пароль.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'agree.accepted' => 'Подтверди согласие на обработку персональных данных.',
        ]);

        try {
            User::create([
                'role_id' => User::CUSTOMER_ROLE,
                'last_name' => $data['lastName'],
                'first_name' => $data['firstName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password_hash' => Hash::make($data['password']),
                'consent_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([
                'email' => 'Этот адрес электронной почты уже используется.',
            ]);
        }

        return to_route('login')->with('status', 'Учётная запись создана. Теперь можно войти.');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required', 'string', 'max:72'],
        ], [
            'email.required' => 'Укажи адрес электронной почты.',
            'email.email' => 'Проверь формат адреса электронной почты.',
            'email.max' => 'Адрес электронной почты слишком длинный.',
            'password.required' => 'Введи пароль.',
            'password.max' => 'Неверная электронная почта или пароль.',
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Неверная электронная почта или пароль.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('account'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function normalizeEmail(Request $request): void
    {
        $email = $request->input('email');

        if (is_string($email)) {
            $request->merge(['email' => mb_strtolower(trim($email))]);
        }
    }
}
