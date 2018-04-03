<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group([
    'prefix' => 'settings',
    'middleware' => ['web', 'auth'],
    'namespace' => 'TechnicPack\LauncherApi\Http\Controllers\Settings',
], function () {

    // Key Routes ...
    Route::get('keys/tokens', 'KeysController@index');
    Route::post('keys/tokens', 'KeysController@store');
    Route::delete('keys/tokens/{key}', 'KeysController@destroy');

    // Client Routes ...
    Route::get('clients/tokens', 'ClientsController@index');
    Route::post('clients/tokens', 'ClientsController@store');
    Route::delete('clients/tokens/{client}', 'ClientsController@destroy');
});
