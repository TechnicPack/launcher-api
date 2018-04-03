<?php

/*
 * This file is part of TechnicPack Launcher API.
 *
 * (c) Syndicate LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TechnicPack\LauncherApi\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the client index.
     *
     * @return mixed
     */
    public function list()
    {
        return false;
    }

    /**
     * Determine whether the user can create clients.
     *
     * @return mixed
     */
    public function create()
    {
        return false;
    }

    /**
     * Determine whether the user can delete the client.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
