<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform\Http\Controllers\Api;

use Platform\QueryBuilder;
use Platform\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\Resource;
use Platform\Http\Resources\Api\BuildResource;

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
            ->where(config('platform.attributes.modpack.name'), $modpackName)
            ->firstOrFail();

        $build = $query->builds($modpack)
            ->with('mods')
            ->where(config('platform.attributes.build.version'), $buildVersion)
            ->firstOrFail();

        return new BuildResource($build);
    }
}
