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

use TechnicPack\LauncherApi\QueryBuilder;
use Illuminate\Http\Resources\Json\Resource;
use TechnicPack\LauncherApi\Http\Controllers\Controller;
use TechnicPack\LauncherApi\Http\Resources\Api\BuildResource;

class ModpackBuildController extends Controller
{
    /**
     * Return a JSON response containing the releases for a given
     * Modpack name and Build version.
     *
     * @param QueryBuilder $query
     * @param string $modpackName
     * @param string $buildVersion
     *
     * @return BuildResource
     */
    public function show(QueryBuilder $query, $modpackName, $buildVersion)
    {
        Resource::withoutWrapping();

        $modpack = $query->modpacks()
            ->where(config('launcher-api.attributes.modpack.name'), $modpackName)
            ->firstOrFail();

        $build = $query->builds($modpack)
            ->with('mods')
            ->where(config('launcher-api.attributes.build.version'), $buildVersion)
            ->firstOrFail();

        return new BuildResource($build);
    }
}
