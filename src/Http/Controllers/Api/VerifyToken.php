<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi\Http\Controllers\Api;

use TechnicPack\LauncherApi\Key;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TechnicPack\LauncherApi\Http\Controllers\Controller;

class VerifyToken extends Controller
{
    /**
     * Return a JSON response confirming the validity of a given API key.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke($token)
    {
        try {
            $key = Key::where('token', $token)->firstOrFail();

            return response()->json([
                'name' => $key->name,
                'valid' => 'Key Validated.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Key does not exist.']);
        }
    }
}
