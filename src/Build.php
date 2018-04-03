<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Build
{
    /**
     * Scope queries to models that are public.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublic(Builder $query);

    /**
     * Scope queries to models that are private.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePrivate(Builder $query);

    /**
     * The mods that make up this build.
     *
     * @return HasMany|BelongsToMany
     */
    public function mods();
}
