<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$factory->define(Tests\Modpack::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'slug' => $faker->slug,
        'recommended' => '',
        'latest' => '',
        'private' => false,
        'hidden' => false,
    ];
});

$factory->state(Tests\Modpack::class, 'public', function (Faker\Generator $faker) {
    return [
       'private' => false,
       'hidden' => false,
   ];
});

$factory->state(Tests\Modpack::class, 'private', function (Faker\Generator $faker) {
    return [
       'private' => true,
       'hidden' => false,
   ];
});

$factory->state(Tests\Modpack::class, 'hidden', function (Faker\Generator $faker) {
    return [
       'hidden' => true,
   ];
});
