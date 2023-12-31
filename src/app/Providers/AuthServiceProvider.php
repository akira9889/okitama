<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\DropoffHistory;
use App\Policies\DropoffHistoryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        DropoffHistory::class => DropoffHistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.url') . '/reset-password?email=' . $user->email . '&token=' . $token;
        });
    }
}
