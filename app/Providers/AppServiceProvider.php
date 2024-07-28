<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Work;
use App\Models\Permit;
use App\Enums\RoleEnum;
use App\Models\WorkRatio;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Policies\UserPolicy;
use App\Policies\WorkPolicy;
use App\Enums\PermissionEnum;
use App\Models\Paycheck;
use App\Policies\PermitPolicy;
use App\Policies\WorkRatioPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\PaycheckPolicy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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

        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Permit::class, PermitPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Work::class, WorkPolicy::class);
        Gate::policy(WorkRatio::class, WorkRatioPolicy::class);
        Gate::policy(Paycheck::class, PaycheckPolicy::class);
    }
}
