<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Todo'         => 'App\Policies\TodoPolicy',
        'App\User'         => 'App\Policies\UserPolicy',
        'App\Currency'     => 'App\Policies\CurrencyPolicy',
        'App\Plan'         => 'App\Policies\PlanPolicy',
        'App\Instance'     => 'App\Policies\InstancePolicy',
        'App\Subscription' => 'App\Policies\SubscriptionPolicy',
        'App\Subscriber'   => 'App\Policies\SubscriberPolicy',
        'App\Query'        => 'App\Policies\QueryPolicy',
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
    }
}
