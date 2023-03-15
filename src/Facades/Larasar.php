<?php

namespace Ibis117\Larasar\Facades;

use Illuminate\Support\Facades\Facade;

class Larasar extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'larasar';
    }
}
