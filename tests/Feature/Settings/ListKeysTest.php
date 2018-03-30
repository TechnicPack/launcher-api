<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature\Settings;

use Tests\User;
use Tests\TestCase;
use TechnicPack\LauncherApi\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListKeysTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function list_keys()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('keys.list');
        factory(Key::class)->create(['name' => 'Key A']);
        factory(Key::class)->create(['name' => 'Key B']);
        factory(Key::class)->create(['name' => 'Key C']);

        $response = $this->getJson('/settings/keys/tokens');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                ['id', 'name', 'token', 'created_at'],
                ['id', 'name', 'token', 'created_at'],
                ['id', 'name', 'token', 'created_at'],
            ],
        ]);
        $response->assertJsonFragment(['name' => 'Key A']);
        $response->assertJsonFragment(['name' => 'Key B']);
        $response->assertJsonFragment(['name' => 'Key C']);
    }

    /** @test **/
    public function unauthenticated_requests_are_dropped()
    {
        $this->authorizeAbility('keys.list');
        factory(Key::class, 3)->create();

        $response = $this->getJson('/settings/keys/tokens');

        $response->assertStatus(401);
    }

    /** @test **/
    public function unauthorized_requests_are_forbidden()
    {
        $this->actingAs(new User);
        $this->denyAbility('keys.list');
        factory(Key::class, 3)->create();

        $response = $this->getJson('/settings/keys/tokens');

        $response->assertStatus(403);
    }
}
