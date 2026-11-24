<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('manage-catalog', fn (User $user): bool => (int) $user->role_id === User::ADMIN_ROLE);
    }
}
