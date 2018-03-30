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

use Tests\Mod;
use Tests\Build;
use Platform\Key;
use Tests\Modpack;
use Tests\TestCase;
use Platform\Client;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowBuildsTest extends TestCase
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
    public function show_build_details()
    {
        $modpack = factory(Modpack::class)->create();
        $build = factory(Build::class)->create([
            'modpack_id' => $modpack->id,
            'minecraft' => '1.12.2',
            'forge' => '14.23.2.2623',
            'min_java' => '1.8',
            'min_memory' => 2048,
        ]);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'minecraft' => '1.12.2',
            'forge' => '14.23.2.2623',
            'java' => '1.8',
            'memory' => 2048,
        ]);
    }

    /** @test **/
    public function build_details_includes_mods()
    {
        $modpack = factory(Modpack::class)->create();
        $build = factory(Build::class)->create(['modpack_id' => $modpack->id]);
        $build->mods()->save(factory(Mod::class)->make([
            'name' => 'advancedgenetics',
            'version' => 'v1.4.3',
            'md5' => '5d46251de21e6d07910127bd345c99cd',
            'url' => 'http://example.com/advancedgenetics-v1.4.3.zip',
            'filesize' => 794508,
        ]));

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['mods' => [['name', 'version', 'md5', 'url', 'filesize']]]);
        $response->assertJsonFragment([
            'name' => 'advancedgenetics',
            'version' => 'v1.4.3',
            'md5' => '5d46251de21e6d07910127bd345c99cd',
            'url' => 'http://example.com/advancedgenetics-v1.4.3.zip',
            'filesize' => 794508,
        ]);
    }

    /** @test **/
    public function drop_requests_for_invalid_modpack()
    {
        $build = factory(Build::class)->create();

        $response = $this->getJson("api/modpack/invalid-modpack/{$build->version}");

        $response->assertStatus(404);
    }

    /** @test **/
    public function drop_requests_for_invalid_build()
    {
        $modpack = factory(Modpack::class)->states('public')->create();

        $response = $this->getJson("api/modpack/{$modpack->slug}/invalid-build");

        $response->assertStatus(404);
    }

    /** @test **/
    public function drop_request_for_private_modpack()
    {
        $modpack = factory(Modpack::class)->states('private')->create();
        $build = factory(Build::class)->create(['modpack_id' => $modpack->id]);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}");

        $response->assertStatus(404);
    }

    /** @test **/
    public function drop_request_for_private_build()
    {
        $modpack = factory(Modpack::class)->states('public')->create();
        $build = factory(Build::class)->states('private')->create(['modpack_id' => $modpack->id]);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}");

        $response->assertStatus(404);
    }

    /** @test **/
    public function drop_request_for_hidden_modpack()
    {
        $modpack = factory(Modpack::class)->states('hidden')->create();
        $build = factory(Build::class)->create(['modpack_id' => $modpack->id]);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}");

        $response->assertStatus(404);
    }

    /** @test */
    public function valid_api_token_can_show_a_private_build()
    {
        $modpack = factory(Modpack::class)->states('public')->create();
        $build = factory(Build::class)->states('private')->create(['modpack_id' => $modpack->id]);
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}?k=valid-token");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'minecraft' => $build->minecraft,
            'forge' => $build->forge,
            'java' => $build->min_java,
            'memory' => $build->min_memory,
        ]);
    }

    /** @test */
    public function valid_api_token_can_not_show_a_hidden_build()
    {
        $modpack = factory(Modpack::class)->states('public')->create();
        $build = factory(Build::class)->states('hidden')->create(['modpack_id' => $modpack->id]);
        factory(Key::class)->create(['token' => 'valid-token']);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}?k=valid-token");

        $response->assertStatus(404);
    }

    /** @test */
    public function valid_client_token_can_show_a_linked_private_build()
    {
        $modpack = factory(Modpack::class)->states('public')->create();
        $build = factory(Build::class)->states('private')->create(['modpack_id' => $modpack->id]);
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $modpack->attachClient($client);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}?cid=valid-client-token");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'minecraft' => $build->minecraft,
            'forge' => $build->forge,
            'java' => $build->min_java,
            'memory' => $build->min_memory,
        ]);
    }

    /** @test */
    public function valid_client_token_can_not_show_a_hidden_private_build_even_if_linked()
    {
        $modpack = factory(Modpack::class)->states('public')->create();
        $build = factory(Build::class)->states('hidden')->create(['modpack_id' => $modpack->id]);
        $client = factory(Client::class)->create(['token' => 'valid-client-token']);
        $modpack->attachClient($client);

        $response = $this->getJson("api/modpack/{$modpack->slug}/{$build->version}?cid=valid-client-token");

        $response->assertStatus(404);
    }
}
