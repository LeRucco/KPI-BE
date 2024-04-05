<?php

namespace App\Providers;

use App\Enums\PermissionEnum;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Policies\AttendancePolicy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Gate::before(function (User $user, $ability) {
            // Log::info($ability);
            return $user->hasRole(RoleEnum::SUPER_ADMIN->value, 'web') ? true : null;
        });

        Gate::policy(Attendance::class, AttendancePolicy::class);
    }
}
