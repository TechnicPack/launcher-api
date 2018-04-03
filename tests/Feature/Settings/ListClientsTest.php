<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature\Settings;

use Tests\User;
use Tests\TestCase;
use TechnicPack\LauncherApi\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListClientsTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function list_clients()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('clients.list');
        factory(Client::class)->create(['title' => 'Client A']);
        factory(Client::class)->create(['title' => 'Client B']);
        factory(Client::class)->create(['title' => 'Client C']);

        $response = $this->getJson('/settings/clients/tokens');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                ['id', 'title', 'token', 'created_at'],
                ['id', 'title', 'token', 'created_at'],
                ['id', 'title', 'token', 'created_at'],
            ],
        ]);
        $response->assertJsonFragment(['title' => 'Client A']);
        $response->assertJsonFragment(['title' => 'Client B']);
        $response->assertJsonFragment(['title' => 'Client C']);
    }

    /** @test **/
    public function unauthenticated_requests_are_dropped()
    {
        $this->authorizeAbility('clients.list');
        factory(Client::class, 3)->create();

        $response = $this->getJson('/settings/clients/tokens');

        $response->assertStatus(401);
    }

    /** @test **/
    public function unauthorized_requests_are_forbidden()
    {
        $this->actingAs(new User);
        $this->denyAbility('clients.list');
        factory(Client::class, 3)->create();

        $response = $this->getJson('/settings/clients/tokens');

        $response->assertStatus(403);
    }
}
