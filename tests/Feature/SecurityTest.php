<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\BcryptPassword;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_password_accepts_unicode_letters_and_decimal_digits(): void
    {
        $rule = ['password' => ['required', 'string', 'min:8', new BcryptPassword]];

        $this->assertTrue(Validator::make(['password' => 'Пароль123'], $rule)->passes());
        $this->assertFalse(Validator::make(['password' => '12345678'], $rule)->passes());
        $this->assertFalse(Validator::make(['password' => 'OnlyLetters'], $rule)->passes());
        $this->assertFalse(Validator::make(['password' => 'Short1'], $rule)->passes());
    }

    public function test_registration_shows_password_and_duplicate_email_errors(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->string('email')->unique();
        });

        $fields = [
            'firstName' => 'Иван',
            'lastName' => 'Проверка',
            'email' => 'security@example.test',
            'phone' => '+7 900 000-00-00',
            'password' => 'OnlyLetters',
            'agree' => '1',
        ];

        $response = $this->followingRedirects()->from('/register.html')->post('/register.html', $fields);
        $response->assertOk()->assertSee('Пароль должен содержать хотя бы одну букву и одну цифру.');
        $this->assertDatabaseCount('users', 0);

        DB::table('users')->insert(['email' => $fields['email']]);
        $fields['email'] = strtoupper($fields['email']);
        $fields['password'] = 'Пароль123';

        $this->followingRedirects()->from('/register.html')->post('/register.html', $fields)
            ->assertOk()->assertSee('Этот адрес электронной почты уже используется.');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_password_reset_rejects_a_password_without_letters(): void
    {
        $this->withSession(['password_reset.1' => 'test-digest'])
            ->from('/reset-password/1')
            ->post('/reset-password/1', [
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ])
            ->assertRedirect('/reset-password/1')
            ->assertSessionHasErrors(['password' => 'Пароль должен содержать хотя бы одну букву и одну цифру.']);
    }

    public function test_default_web_limit_returns_429_and_retry_after(): void
    {
        $this->get('/')->assertRedirect('/index.html')->assertHeader('X-RateLimit-Limit', '120');

        $key = md5('trytravel-web127.0.0.1');
        for ($attempt = 1; $attempt < 120; $attempt++) {
            RateLimiter::hit($key, 60);
        }

        $this->get('/')->assertTooManyRequests()->assertHeader('Retry-After');
    }

    public function test_customer_cannot_access_admin_bookings(): void
    {
        $customer = new User(['role_id' => User::CUSTOMER_ROLE]);
        $customer->user_id = 1;
        $customer->password_hash = 'test-hash';

        $this->actingAs($customer)->get('/admin/bookings')->assertForbidden();
    }
}
