<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Response;
use App\Customer;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Gate::define('is-customer', function($user) {
            return $user->customer;
        });
        Gate::define('edit-subscription', function($user, $subscription) {
             return $subscription->customer === $user->customer;
        });
        Gate::define('is-subbed', function($user) {
            return $user->customer->subscriptions->where('status', 'Active');
        });
    }
}
