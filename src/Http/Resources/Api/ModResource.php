<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform\Http\Resources\Api;

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
            'name' => $this->getAttribute(config('platform.attributes.mod.name')),
            'version' => $this->getAttribute(config('platform.attributes.mod.version')),
            'md5' => $this->getAttribute(config('platform.attributes.mod.md5')),
            'url' => $this->getAttribute(config('platform.attributes.mod.url')),
            'filesize' => (int) $this->getAttribute(config('platform.attributes.mod.filesize')),
        ];
    }
}
