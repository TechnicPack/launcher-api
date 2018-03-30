<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Platform\Build as PlatformBuild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
