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

use Tests\TestCase;

class DescribeApiTest extends TestCase
{
    /** @test **/
    public function describe_the_api()
    {
        config(['launcher-api.provider' => 'Custom Provider']);
        config(['launcher-api.version' => '1.2.3']);
        config(['launcher-api.stream' => 'test']);

        $response = $this->getJson('/api');

        $response->assertStatus(200);
        $response->assertExactJson([
            'api' => 'Custom Provider',
            'version' => '1.2.3',
            'stream' => 'test',
        ]);
    }
}
