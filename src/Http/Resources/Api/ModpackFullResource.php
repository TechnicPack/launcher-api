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

class ModpackFullResource extends Resource
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
            'name' => $this->getAttribute(config('launcher-api.attributes.modpack.name')),
            'display_name' => $this->getAttribute(config('launcher-api.attributes.modpack.display_name')),
            'recommended' => $this->getAttribute(config('launcher-api.attributes.modpack.recommended')),
            'latest' => $this->getAttribute(config('launcher-api.attributes.modpack.latest')),
            'builds' => $this->builds->pluck(config('launcher-api.attributes.build.version')),
        ];
    }
}
