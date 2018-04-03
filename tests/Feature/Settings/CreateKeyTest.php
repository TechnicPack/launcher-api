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
use TechnicPack\LauncherApi\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateKeyTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function create_a_key()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('keys.create');

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);

        $response->assertStatus(201);
        $this->assertCount(1, Key::all());
        $this->assertDatabaseHas('keys', [
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'token', 'created_at'],
        ]);
        $response->assertJsonFragment([
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);
    }

    /** @test **/
    public function unauthenticated_requests_are_dropped()
    {
        $this->authorizeAbility('keys.create');

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);

        $response->assertStatus(401);
        $this->assertCount(0, Key::all());
    }

    /** @test **/
    public function unauthorized_requests_are_forbidden()
    {
        $this->actingAs(new User);
        $this->denyAbility('keys.create');

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);

        $response->assertStatus(403);
        $this->assertCount(0, Key::all());
    }

    /** @test **/
    public function name_is_required()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('keys.create');

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => '',
            'token' => 'my-key-token',
        ]);

        $response->assertStatus(422);
        $this->assertCount(0, Key::all());
    }

    /** @test **/
    public function name_is_unique()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('keys.create');
        factory(Key::class)->create([
            'name' => 'My Key',
        ]);

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);

        $response->assertStatus(422);
        $this->assertCount(1, Key::all());
    }

    /** @test **/
    public function token_is_required()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('keys.create');

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => 'My Key',
            'token' => '',
        ]);

        $response->assertStatus(422);
        $this->assertCount(0, Key::all());
    }

    /** @test **/
    public function token_is_unique()
    {
        $this->actingAs(new User);
        $this->authorizeAbility('keys.create');
        factory(Key::class)->create([
            'token' => 'my-key-token',
        ]);

        $response = $this->postJson('/settings/keys/tokens', [
            'name' => 'My Key',
            'token' => 'my-key-token',
        ]);

        $response->assertStatus(422);
        $this->assertCount(1, Key::all());
    }
}
