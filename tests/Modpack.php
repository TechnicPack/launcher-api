<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Platform\HasClients;
use Illuminate\Database\Eloquent\Model;
use Platform\Modpack as PlatformModpack;
use Illuminate\Database\Eloquent\Builder;

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
