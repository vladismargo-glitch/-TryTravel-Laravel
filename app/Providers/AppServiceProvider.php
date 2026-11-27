<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('trytravel-web', fn (Request $request): Limit => Limit::perMinute(120)->by($request->ip()));

        $isAdmin = fn (User $user): bool => (int) $user->role_id === User::ADMIN_ROLE;
        Gate::define('manage-catalog', $isAdmin);
        Gate::define('manage-bookings', $isAdmin);
        Gate::define('manage-promotions', $isAdmin);
        Gate::define('manage-roles', $isAdmin);
        Gate::define('moderate-reviews', $isAdmin);
    }
}
