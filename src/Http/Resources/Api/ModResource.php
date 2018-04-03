<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi\Http\Resources\Api;

use Illuminate\Http\Resources\Json\Resource;

class ModResource extends Resource
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
            'name' => $this->getAttribute(config('launcher-api.attributes.mod.name')),
            'version' => $this->getAttribute(config('launcher-api.attributes.mod.version')),
            'md5' => $this->getAttribute(config('launcher-api.attributes.mod.md5')),
            'url' => $this->getAttribute(config('launcher-api.attributes.mod.url')),
            'filesize' => (int) $this->getAttribute(config('launcher-api.attributes.mod.filesize')),
        ];
    }
}
