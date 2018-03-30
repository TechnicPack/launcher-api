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

use Illuminate\Database\Query\Builder;

trait HasClients
{
    /**
     * Model clients.
     */
    public function clients()
    {
        return $this->morphToMany(Client::class, 'exposes', 'client_exposes');
    }

    /**
     * Model has at least one client.
     *
     * @return bool
     */
    public function hasClients()
    {
        return $this->clients()->count() > 0;
    }

    /**
     * Model belongs to the given client.
     *
     * @param int|Client $client
     * @return bool
     */
    public function knowsClient($client)
    {
        if (is_int($client)) {
            $client = (object) ['id' => $client];
        }

        return $this->clients()->where('id', $client->id)->exists();
    }

    /**
     * Attach the given client to the model.
     *
     * @param Client $client
     */
    public function attachClient($client)
    {
        $this->clients()->attach($client);
    }

    /**
     * Detach the given client from the model.
     *
     * @param Client $client
     */
    public function detachClient($client)
    {
        $this->clients()->detach($client);
    }

    /**
     * Scope results to a specific client.
     *
     * @param Builder $query
     * @param $client
     * @return Builder
     */
    public function scopeForClient($query, $client)
    {
        if (is_int($client)) {
            $client = (object) ['id' => $client];
        }

        return $query->whereHas('clients', function ($query) use ($client) {
            $query->where('id', $client->id);
        });
    }

    /**
     * Scope results to a specific client token.
     *
     * @param Builder $query
     * @param $clientToken
     * @return Builder
     */
    public function scopeForClientToken($query, $clientToken)
    {
        return $query->whereHas('clients', function ($query) use ($clientToken) {
            $query->where('token', $clientToken);
        });
    }
}
