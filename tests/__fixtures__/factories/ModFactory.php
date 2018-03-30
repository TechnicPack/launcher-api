<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$factory->define(Tests\Mod::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->slug,
        'version' => $faker->numerify('v#.#.#'),
        'md5' => $faker->md5,
        'url' => $faker->url,
        'filesize' => $faker->numberBetween(10000, 300000),
    ];
});
