<?php

// Merge the boot() body into your existing AppServiceProvider.

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * Every @can('x.y'), $user->can('x.y'), can:x.y middleware and
         * $this->authorize('x.y') call lands here first.
         *
         * Returning true grants, returning null falls through to policies
         * (row-level rules, e.g. a parent only sees their own children),
         * and we never return false so policies still get their say.
         */
        Gate::before(function (User $user, string $ability) {
            // Optional: lock out non-active accounts entirely. Careful - the
            // users.status default is 'pending', so make sure your approval
            // flow sets 'active' before enabling this.
            // if ($user->status !== 'active') {
            //     return false;
            // }

            return $user->hasPermission($ability) ? true : null;
        });
    }
}
