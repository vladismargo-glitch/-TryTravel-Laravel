<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $isAdmin = fn (User $user): bool => (int) $user->role_id === User::ADMIN_ROLE;
        Gate::define('manage-catalog', $isAdmin);
        Gate::define('manage-bookings', $isAdmin);
        Gate::define('manage-promotions', $isAdmin);
        Gate::define('manage-roles', $isAdmin);
        Gate::define('moderate-reviews', $isAdmin);
    }
}
