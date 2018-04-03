<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use TechnicPack\LauncherApi\Build as PlatformBuild;

class Build extends Model implements PlatformBuild
{
    public function mods()
    {
        return $this->belongsToMany(Mod::class);
    }

    public function scopePublic(Builder $query)
    {
        return $query->where('is_published', true)
            ->where('private', false);
    }

    public function scopePrivate(Builder $query)
    {
        return $query->where('is_published', true)
            ->where('private', true);
    }
}
