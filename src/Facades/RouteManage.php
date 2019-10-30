<?php

namespace Chunrongl\TqigouService\Facades;


use Illuminate\Support\Facades\Facade;

class RouteManage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tqigou.route';
    }
}