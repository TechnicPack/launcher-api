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
    'prefix' => 'api',
    'middleware' => 'api',
    'namespace' => 'TechnicPack\LauncherApi\Http\Controllers\Api',
], function () {

    // API Root
    Route::get('/', 'DescribeApi');

    // Verify Token Routes ...
    Route::get('verify/{token}', 'VerifyToken');

    // Modpack Routes ...
    Route::get('modpack', 'ModpackController@index');
    Route::get('modpack/{modpackName}', 'ModpackController@show');
    Route::get('modpack/{modpackName}/{buildVersion}', 'ModpackBuildController@show');
});
