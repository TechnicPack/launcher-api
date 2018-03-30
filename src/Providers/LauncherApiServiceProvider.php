<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LauncherApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');

        $this->publishes([__DIR__.'/../../config/launcher-api.php' => config_path('launcher-api.php')]);

        $this->mergeConfigFrom(__DIR__.'/../../config/launcher-api.php', 'launcher-api');

        $this->registerPolicies();
    }

    /**
     * Register policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        Gate::define('keys.list', config('launcher-api.authorize.keys.list'));
        Gate::define('keys.create', config('launcher-api.authorize.keys.create'));
        Gate::define('keys.delete', config('launcher-api.authorize.keys.delete'));
        Gate::define('clients.list', config('launcher-api.authorize.clients.list'));
        Gate::define('clients.create', config('launcher-api.authorize.clients.create'));
        Gate::define('clients.delete', config('launcher-api.authorize.clients.delete'));
    }
}
