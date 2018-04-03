<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Tests\TestCase;
use TechnicPack\LauncherApi\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tokens_can_be_validated()
    {
        factory(Client::class)->create([
            'token' => 'CLIENT-TOKEN-1234',
        ]);

        $this->assertTrue(Client::isValid('CLIENT-TOKEN-1234'));
        $this->assertFalse(Client::isValid('INVALID-TOKEN-9876'));
    }
}
