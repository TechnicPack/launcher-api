<?php

/*
 * This file is part of TechnicPack Launcher Api.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform;

use Illuminate\Database\Eloquent\Builder;

interface Modpack
{
    /**
     * Return a has many relationship for the modpack builds.
     *
     * @return mixed
     */
    public function builds();

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
     * Scope queries to models that are linked to the client token.
     *
     * @param Builder $query
     * @param string $clientToken
     * @return Builder
     */
    public function scopeForClientToken($query, $clientToken);
}
