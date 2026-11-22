<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\BcryptPassword;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use function Illuminate\Support\defer;

class PasswordResetController extends Controller
{
    private const EXPIRY_MINUTES = 30;

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:254'],
        ], [
            'email.required' => 'Укажи адрес электронной почты.',
            'email.email' => 'Проверь формат адреса электронной почты.',
            'email.max' => 'Адрес электронной почты слишком длинный.',
        ]);

        $email = mb_strtolower(trim($data['email']));
        defer(fn () => $this->issueAndSend($email));

        return to_route('password.request')->with(
            'status',
            'Если этот адрес зарегистрирован, на него придёт ссылка для восстановления пароля.',
        );
    }

    public function open(Request $request, string $resetId, string $token): RedirectResponse
    {
        $digest = hash('sha256', $token);

        if (! $this->availableReset($resetId, $digest)->exists()) {
            return $this->expired();
        }

        $request->session()->put('password_reset.'.$resetId, $digest);

        return to_route('password.reset', ['resetId' => $resetId])
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Cache-Control', 'no-store');
    }

    public function show(Request $request, string $resetId): Response|RedirectResponse
    {
        $digest = $request->session()->get('password_reset.'.$resetId);

        if (! is_string($digest) || ! $this->availableReset($resetId, $digest)->exists()) {
            $request->session()->forget('password_reset.'.$resetId);

            return $this->expired();
        }

        return response()
            ->view('reset-password', ['resetId' => $resetId])
            ->header('Cache-Control', 'no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }

    public function update(Request $request, string $resetId): RedirectResponse
    {
        $digest = $request->session()->get('password_reset.'.$resetId);

        if (! is_string($digest)) {
            return $this->expired();
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', new BcryptPassword],
        ], [
            'password.required' => 'Укажи новый пароль.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        $updated = DB::transaction(function () use ($resetId, $digest, $data): bool {
            $reset = $this->availableReset($resetId, $digest)->first();

            if ($reset === null) {
                return false;
            }

            // Issuing and redeeming links both lock the user before changing reset records.
            $user = User::query()->whereKey($reset->user_id)->lockForUpdate()->first();

            if ($user === null || $this->availableReset($resetId, $digest)->update(['used_at' => now()]) !== 1) {
                return false;
            }

            $user->forceFill(['password_hash' => Hash::make($data['password'])])->save();

            return true;
        }, 3);

        $request->session()->forget('password_reset.'.$resetId);

        if (! $updated) {
            return $this->expired();
        }

        $request->session()->regenerate();

        return to_route('login')->with('status', 'Пароль изменён. Теперь можно войти.');
    }

    private function issueAndSend(string $email): void
    {
        $token = bin2hex(random_bytes(32));
        $issued = DB::transaction(function () use ($email, $token): ?array {
            $user = User::query()->where('email', $email)->lockForUpdate()->first();

            if ($user === null) {
                return null;
            }

            $now = now();
            $active = DB::table('password_reset_tokens')
                ->where('user_id', $user->getKey())
                ->whereNull('used_at')
                ->where('expires_at', '>', $now);

            if ((clone $active)->where('expires_at', '>', $now->copy()->addMinutes(self::EXPIRY_MINUTES - 1))->exists()) {
                return null;
            }

            $active->update(['expires_at' => $now]);

            $resetId = DB::table('password_reset_tokens')->insertGetId([
                'user_id' => $user->getKey(),
                'token_hash' => hash('sha256', $token),
                'expires_at' => $now->copy()->addMinutes(self::EXPIRY_MINUTES),
            ], 'reset_id');

            return ['id' => $resetId, 'email' => $user->email];
        }, 3);

        if ($issued === null) {
            return;
        }

        $url = rtrim(config('app.url'), '/').route('password.open', [
            'resetId' => $issued['id'],
            'token' => $token,
        ], false);

        $minutes = self::EXPIRY_MINUTES;
        $body = "Чтобы задать новый пароль в TryTravel, открой ссылку:\n\n{$url}\n\n"
            ."Ссылка действует {$minutes} минут и используется один раз. "
            .'Если ты не запрашивал сброс пароля, письмо можно проигнорировать.';

        Mail::raw($body, fn ($message) => $message->to($issued['email'])->subject('Восстановление пароля TryTravel'));
    }

    private function availableReset(string $resetId, string $digest): Builder
    {
        return DB::table('password_reset_tokens')
            ->where('reset_id', $resetId)
            ->where('token_hash', $digest)
            ->whereNull('used_at')
            ->where('expires_at', '>', now());
    }

    private function expired(): RedirectResponse
    {
        return to_route('password.request')
            ->withErrors(['reset' => 'Ссылка недействительна или срок её действия истёк. Запроси новую.'])
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Cache-Control', 'no-store');
    }
}
