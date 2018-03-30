<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$factory->define(Tests\Build::class, function (Faker\Generator $faker) {
    return [
        'version' => $faker->numerify('#.#.#'),
        'forge' => $faker->numerify('#.#.#.#'),
        'minecraft' => $faker->numerify('#.#.#'),
        'min_java' => $faker->numerify('1.#'),
        'min_memory' => $faker->numberBetween(512, 4096),
        'modpack_id' => function () {
            return factory(\Tests\Modpack::class)->create()->id;
        },
    ];
});

$factory->state(Tests\Build::class, 'public', function (Faker\Generator $faker) {
    return [
       'private' => false,
   ];
});

$factory->state(Tests\Build::class, 'private', function (Faker\Generator $faker) {
    return [
       'private' => true,
   ];
});

$factory->state(Tests\Build::class, 'hidden', function (Faker\Generator $faker) {
    return [
       'is_published' => false,
   ];
});
