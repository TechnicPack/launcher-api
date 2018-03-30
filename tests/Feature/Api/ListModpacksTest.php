<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature\Api;

use Tests\Build;
use Tests\Modpack;
use Tests\TestCase;
use TechnicPack\LauncherApi\Key;
use TechnicPack\LauncherApi\Client;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListModpacksTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown()
    {
        // The wrap attribute is static on Resource. This isn't a
        // problem in normal operation because we usually return a single
        // resource then the application exits. But for our test suite it
        // means we leak some state between requests unless we reset this
        // back to the default 'data' at the end of each test.
        Resource::wrap('data');

        parent::tearDown();
    }

    /** @test */
    public function guests_can_only_list_public_modpacks()
    {
        $public = factory(Modpack::class)->states('public')->create();
        $private = factory(Modpack::class)->states('private')->create();
        $hidden = factory(Modpack::class)->states('hidden')->create();

        $response = $this->getJson('api/modpack');

        $response->assertStatus(200);
        $response->assertJsonFragment([$public->slug => $public->name]);
        $response->assertJsonMissing([$private->slug => $private->name]);
        $response->assertJsonMissing([$hidden->slug => $hidden->name]);
    }

    /** @test */
    public function retrieve_expanded_results_with_include_full()
    {
        factory(Modpack::class)->states('public')->create([
            'slug' => 'public-modpack',
            'name' => 'Public Modpack',
            'recommended' => '1.0.0',
            'latest' => '1.0.1',
        ]);

        $response = $this->getJson('api/modpack?include=full');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'public-modpack',
            'display_name' => 'Public Modpack',
            'recommended' => '1.0.0',
            'latest' => '1.0.1',
        ]);
    }

    /** @test * */
    public function expanded_results_includes_public_builds_list()
    {
        $modpack = factory(Modpack::class)->create();
        $buildA = factory(Build::class)->states('public')->make();
        $buildB = factory(Build::class)->states('private')->make();
        $buildC = factory(Build::class)->states('hidden')->make();
        $modpack->builds()->saveMany([$buildA, $buildB, $buildC]);

        $response = $this->getJson('api/modpack?include=full');

        $response->assertStatus(200);
        $response->assertJsonFragment(['builds' => [$buildA->version]]);
        $response->assertJsonMissing([$buildB->version]);
        $response->assertJsonMissing([$buildC->version]);
    }

    /** @test */
    public function valid_api_token_can_list_non_hidden_modpacks()
    {
        $public = factory(Modpack::class)->states('public')->create();
        $private = factory(Modpack::class)->states('private')->create();
        $hidden = factory(Modpack::class)->states('hidden')->create();
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson('api/modpack?k=valid-token');

        $response->assertStatus(200);
        $response->assertJsonFragment([$public->slug => $public->name]);
        $response->assertJsonFragment([$private->slug => $private->name]);
        $response->assertJsonMissing([$hidden->slug => $hidden->name]);
    }

    /** @test * */
    public function valid_api_token_can_list_private_builds_in_expanded_results()
    {
        $modpack = factory(Modpack::class)->create();
        $buildA = factory(Build::class)->states('public')->make();
        $buildB = factory(Build::class)->states('private')->make();
        $buildC = factory(Build::class)->states('hidden')->make();
        $modpack->builds()->saveMany([$buildA, $buildB, $buildC]);
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson('api/modpack?include=full&k=valid-token');

        $response->assertStatus(200);
        $response->assertJsonFragment(['builds' => [
            $buildA->version,
            $buildB->version,
        ]]);
        $response->assertJsonMissing([$buildC->version]);
    }

    /** @test */
    public function valid_client_token_can_list_linked_private_modpacks()
    {
        $public = factory(Modpack::class)->states('public')->create();
        $private = factory(Modpack::class)->states('private')->create();
        $hidden = factory(Modpack::class)->states('hidden')->create();
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $private->attachClient($client);

        $this->assertTrue($private->knowsClient($client));

        $response = $this->getJson('api/modpack?cid=valid-client-token');

        $response->assertStatus(200);
        $response->assertJsonFragment([$public->slug => $public->name]);
        $response->assertJsonFragment([$private->slug => $private->name]);
        $response->assertJsonMissing([$hidden->slug => $hidden->name]);
    }

    /** @test */
    public function valid_client_token_can_not_list_unlinked_private_modpacks()
    {
        $private = factory(Modpack::class)->states('private')->create();
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);

        $this->assertFalse($private->knowsClient($client));

        $response = $this->getJson('api/modpack?cid=valid-client-token');

        $response->assertStatus(200);
        $response->assertJsonMissing([$private->slug => $private->name]);
    }

    /** @test */
    public function valid_client_token_can_not_list_hidden_modpacks_even_if_linked()
    {
        $hidden = factory(Modpack::class)->states('hidden')->create();
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $hidden->attachClient($client);

        $this->assertTrue($hidden->knowsClient($client));

        $response = $this->getJson('api/modpack?cid=valid-client-token');

        $response->assertStatus(200);
        $response->assertJsonMissing([$hidden->slug => $hidden->name]);
    }

    /** @test * */
    public function valid_client_token_can_list_private_builds_in_expanded_results()
    {
        $modpack = factory(Modpack::class)->create();
        $buildA = factory(Build::class)->states('public')->make();
        $buildB = factory(Build::class)->states('private')->make();
        $buildC = factory(Build::class)->states('hidden')->make();
        $modpack->builds()->saveMany([$buildA, $buildB, $buildC]);
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $modpack->attachClient($client);

        $response = $this->getJson('api/modpack?include=full&cid=valid-client-token');

        $response->assertStatus(200);
        $response->assertJsonFragment(['builds' => [
            $buildA->version,
            $buildB->version,
        ]]);
        $response->assertJsonMissing([$buildC->version]);
    }
}
