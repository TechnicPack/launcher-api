<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform\Http\Controllers\Api;

use Platform\QueryBuilder;
use Illuminate\Support\Facades\Storage;
use Platform\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\Resource;
use Platform\Http\Resources\Api\ModpackResource;
use Platform\Http\Resources\Api\ModpackFullResource;

class ModpackController extends Controller
{
    /**
     * Return a JSON response listing all modpacks the requester has access to.
     * If a valid API key is provided in the query string as k={key} then all
     * public and private modpacks and builds. If a valid Client token is
     * provided in the query string as cid={token} then all public modpacks and
     * builds, and any private modpacks and builds that the client has been
     * authorized for will be returned.
     *
     * Additional details can be returned by placing include=full in the query
     * string.
     *
     * @param QueryBuilder $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(QueryBuilder $query)
    {
        Resource::wrap('modpacks');

        // TODO: N+1 bug here; but this result set shouldn't get large
        $modpacks = $query->modpacks()->get()->keyBy(config('platform.attributes.modpack.name'));
        $modpacks->each(function ($modpack) use ($query) {
            $modpack->setRelation('builds', $query->builds($modpack)->get());
        });

        if (request('include') === 'full') {
            $resource = ModpackFullResource::collection($modpacks);
        } else {
            $resource = ModpackResource::collection($modpacks);
        }

        return $resource->additional([
            'mirror_url' => preg_replace('/([^:])(\/{2,})/', '$1/', Storage::url('/')),
        ]);
    }

    /**
     * Return a JSON response containing details of a specific Modpack and
     * list all builds. As with the index method, an API key (k={key}) or
     * Client token (cid={token}) can be appended to the query string to
     * provide access to private modpacks and builds as authorized and
     * required.
     *
     * @param QueryBuilder $query
     * @param $modpackName
     *
     * @return ModpackFullResource
     */
    public function show(QueryBuilder $query, $modpackName)
    {
        Resource::withoutWrapping();

        $modpack = $query->modpacks()
            ->where(config('platform.attributes.modpack.name'), $modpackName)
            ->firstOrFail();

        $modpack->setRelation('builds', $query->builds($modpack)->get());

        return new ModpackFullResource($modpack);
    }
}
