<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Platform;

use Illuminate\Http\Request;

class QueryBuilder
{
    /**
     * The application request.
     *
     * @var Request
     */
    private $request;

    /**
     * QueryBuilder constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Create a query builder for modpacks from the request.
     *
     * @return Builder
     */
    public function modpacks()
    {
        $modpack = config('platform.model.modpack');
        $query = $modpack::query()->public();

        if (Key::isValid($this->request->get('k'))) {
            $query->union($modpack::query()->private());
        }

        if (Client::isValid($this->request->get('cid'))) {
            $query->union($modpack::query()->private()->forClientToken($this->request->get('cid')));
        }

        return $query;
    }

    /**
     * Create a query builder for builds from the request.
     *
     * @param $modpack
     * @return Builder
     */
    public function builds($modpack)
    {
        $query = $modpack->builds()->public();

        if (Key::isValid($this->request->get('k')) || Client::isValid($this->request->get('cid'))) {
            $query->union($modpack->builds()->private());
        }

        return $query;
    }
}
