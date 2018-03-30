<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi\Http\Resources\Api;

use Illuminate\Http\Resources\Json\Resource;

class BuildResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'minecraft' => $this->getAttribute(config('launcher-api.attributes.build.minecraft')),
            'java' => $this->getAttribute(config('launcher-api.attributes.build.java')),
            'memory' => (int) $this->getAttribute(config('launcher-api.attributes.build.memory')),
            'forge' => $this->getAttribute(config('launcher-api.attributes.build.forge')),
            'mods' => ModResource::collection($this->mods),
        ];
    }
}
