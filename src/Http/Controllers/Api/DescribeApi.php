<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi\Http\Controllers\Api;

use TechnicPack\LauncherApi\Http\Controllers\Controller;

class DescribeApi extends Controller
{
    /**
     * Return a JSON response describing the API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        return response()->json([
            'api' => config('launcher-api.provider'),
            'version' => config('launcher-api.version'),
            'stream' => config('launcher-api.stream'),
        ]);
    }
}
