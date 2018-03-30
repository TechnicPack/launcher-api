<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature\Api;

use Tests\Build;
use Platform\Key;
use Tests\Modpack;
use Tests\TestCase;
use Platform\Client;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowModpacksTest extends TestCase
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
    public function show_modpack_details()
    {
        factory(Modpack::class)->states('public')->create([
            'slug' => 'public-modpack',
            'name' => 'Public Modpack',
            'recommended' => '1.0.0',
            'latest' => '1.0.1',
        ]);

        $response = $this->getJson('api/modpack/public-modpack');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'public-modpack',
            'display_name' => 'Public Modpack',
            'recommended' => '1.0.0',
            'latest' => '1.0.1',
        ]);
    }

    /** @test * */
    public function modpack_details_includes_public_builds_list()
    {
        $modpack = factory(Modpack::class)->create();
        $buildA = factory(Build::class)->states('public')->make();
        $buildB = factory(Build::class)->states('private')->make();
        $buildC = factory(Build::class)->states('hidden')->make();
        $modpack->builds()->saveMany([$buildA, $buildB, $buildC]);

        $response = $this->getJson("api/modpack/{$modpack->slug}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['builds' => [$buildA->version]]);
        $response->assertJsonMissing([$buildB->version]);
        $response->assertJsonMissing([$buildC->version]);
    }

    /** @test * */
    public function drop_requests_for_invalid_modpack()
    {
        $response = $this->getJson('api/modpack/invalid-modpack');

        $response->assertStatus(404);
    }

    /** @test * */
    public function drop_request_for_private_modpack()
    {
        $modpack = factory(Modpack::class)->states('private')->create();

        $response = $this->getJson("api/modpack/{$modpack->slug}");

        $response->assertStatus(404);
    }

    /** @test * */
    public function drop_request_for_hidden_modpack()
    {
        $modpack = factory(Modpack::class)->states('hidden')->create();

        $response = $this->getJson("api/modpack/{$modpack->slug}");

        $response->assertStatus(404);
    }

    /** @test */
    public function valid_api_token_can_show_a_private_modpack()
    {
        $modpack = factory(Modpack::class)->states('private')->create();
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson("api/modpack/{$modpack->slug}?k=valid-token");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $modpack->slug]);
    }

    /** @test * */
    public function valid_api_token_can_show_private_builds()
    {
        $modpack = factory(Modpack::class)->create();
        $buildA = factory(Build::class)->states('public')->make();
        $buildB = factory(Build::class)->states('private')->make();
        $buildC = factory(Build::class)->states('hidden')->make();
        $modpack->builds()->saveMany([$buildA, $buildB, $buildC]);
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson("api/modpack/{$modpack->slug}?k=valid-token");

        $response->assertStatus(200);
        $response->assertJsonFragment(['builds' => [
            $buildA->version,
            $buildB->version,
        ]]);
        $response->assertJsonMissing([$buildC->version]);
    }

    /** @test * */
    public function drop_request_for_hidden_modpack_even_with_api_token()
    {
        $modpack = factory(Modpack::class)->states('hidden')->create();
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson("api/modpack/{$modpack->slug}?k=valid-token");

        $response->assertStatus(404);
    }

    /** @test */
    public function valid_client_token_can_show_linked_private_modpacks()
    {
        $modpack = factory(Modpack::class)->states('private')->create();
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $modpack->attachClient($client);

        $this->assertTrue($modpack->knowsClient($client));

        $response = $this->getJson("api/modpack/{$modpack->slug}?cid=valid-client-token");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $modpack->slug]);
    }

    /** @test * */
    public function valid_client_token_can_show_linked_private_builds()
    {
        $modpack = factory(Modpack::class)->create();
        $buildA = factory(Build::class)->states('public')->make();
        $buildB = factory(Build::class)->states('private')->make();
        $buildC = factory(Build::class)->states('hidden')->make();
        $modpack->builds()->saveMany([$buildA, $buildB, $buildC]);
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $modpack->attachClient($client);

        $response = $this->getJson("api/modpack/{$modpack->slug}?cid=valid-client-token");

        $response->assertStatus(200);
        $response->assertJsonFragment(['builds' => [
            $buildA->version,
            $buildB->version,
        ]]);
        $response->assertJsonMissing([$buildC->version]);
    }

    /** @test */
    public function drop_request_for_private_modpack_if_client_is_not_linked()
    {
        $modpack = factory(Modpack::class)->states('private')->create();
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);

        $this->assertFalse($modpack->knowsClient($client));

        $response = $this->getJson("api/modpack/{$modpack->slug}?cid=valid-client-token");

        $response->assertStatus(404);
    }

    /** @test * */
    public function drop_request_for_hidden_modpack_even_if_client_is_linked()
    {
        $modpack = factory(Modpack::class)->states('hidden')->create();
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $modpack->attachClient($client);

        $this->assertTrue($modpack->knowsClient($client));

        $response = $this->getJson("api/modpack/{$modpack->slug}?cid=valid-client-token");

        $response->assertStatus(404);
    }
}
