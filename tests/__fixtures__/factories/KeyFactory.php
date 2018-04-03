<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$factory->define(TechnicPack\LauncherApi\Key::class, function () {
    return [
        'name' => 'Test Key',
        'token' => 'test-key-token',
    ];
});
