<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use PHPUnit\Framework\Assert;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TechnicPack\LauncherApi\Providers\PlatformServiceProvider;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        Config::set('launcher-api.model.modpack', Modpack::class);
        Config::set('launcher-api.model.build', Build::class);

        $this->withFactories(__DIR__.'/__fixtures__/factories');

        if (array_search(RefreshDatabase::class, get_declared_traits())) {
            $this->loadMigrationsFrom(__DIR__.'/__fixtures__/migrations');
        }

        EloquentCollection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contains the specified value.');
        });
        EloquentCollection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), 'Failed asserting that the collection does not contain the specified value.');
        });
    }

    /**
     * Autoload Service Providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PlatformServiceProvider::class,
        ];
    }

    /**
     * Authorize the given ability.
     *
     * @param $ability
     */
    protected function authorizeAbility($ability)
    {
        Gate::define($ability, function () {
            return true;
        });
    }

    /**
     * Deny the given ability.
     *
     * @param $ability
     */
    protected function denyAbility($ability)
    {
        Gate::define($ability, function () {
            return false;
        });
    }
}
