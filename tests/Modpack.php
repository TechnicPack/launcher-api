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
use TechnicPack\LauncherApi\HasClients;
use Illuminate\Database\Eloquent\Builder;
use TechnicPack\LauncherApi\Modpack as PlatformModpack;

class Modpack extends Model implements PlatformModpack
{
    use HasClients;

    public function builds()
    {
        return $this->hasMany(Build::class);
    }

    public function scopePublic(Builder $query)
    {
        return $query->where('hidden', false)
            ->where('private', false);
    }

    public function scopePrivate(Builder $query)
    {
        return $query->where('hidden', false)
            ->where('private', true);
    }
}
