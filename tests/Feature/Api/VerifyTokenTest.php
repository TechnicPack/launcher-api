<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature\Api;

use Tests\TestCase;
use TechnicPack\LauncherApi\Key;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verify_a_valid_api_key()
    {
        factory(Key::class)->create([
            'token' => 'APIKEY1234',
            'name' => 'Test Key',
        ]);

        $response = $this->getJson('/api/verify/APIKEY1234');

        $response->assertStatus(200);
        $response->assertExactJson([
            'valid' => 'Key Validated.',
            'name' => 'Test Key',
        ]);
    }

    /** @test */
    public function return_error_on_invalid_key()
    {
        $response = $this->getJson('/api/verify/INVALIDKEY');

        $response->assertStatus(200);
        $response->assertExactJson([
            'error' => 'Key does not exist.',
        ]);
    }
}
