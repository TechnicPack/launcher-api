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

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Determine if the given token is valid.
     *
     * @param string $token
     *
     * @return bool
     */
    public static function isValid($token)
    {
        return self::where('token', $token)->exists();
    }
}
