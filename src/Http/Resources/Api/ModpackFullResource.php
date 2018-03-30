<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform\Http\Resources\Api;

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
            'name' => $this->getAttribute(config('platform.attributes.modpack.name')),
            'display_name' => $this->getAttribute(config('platform.attributes.modpack.display_name')),
            'recommended' => $this->getAttribute(config('platform.attributes.modpack.recommended')),
            'latest' => $this->getAttribute(config('platform.attributes.modpack.latest')),
            'builds' => $this->builds->pluck(config('platform.attributes.build.version')),
        ];
    }
}
