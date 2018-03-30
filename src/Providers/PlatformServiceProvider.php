<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PlatformServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any platform services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');

        $this->publishes([__DIR__.'/../../config/platform.php' => config_path('platform.php')]);

        $this->mergeConfigFrom(__DIR__.'/../../config/platform.php', 'platform');

        $this->registerPolicies();
    }

    /**
     * Register policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        Gate::define('keys.list', config('platform.authorize.keys.list'));
        Gate::define('keys.create', config('platform.authorize.keys.create'));
        Gate::define('keys.delete', config('platform.authorize.keys.delete'));
        Gate::define('clients.list', config('platform.authorize.clients.list'));
        Gate::define('clients.create', config('platform.authorize.clients.create'));
        Gate::define('clients.delete', config('platform.authorize.clients.delete'));
    }
}
