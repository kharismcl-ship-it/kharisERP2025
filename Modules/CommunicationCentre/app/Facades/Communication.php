<?php

namespace Modules\CommunicationCentre\Facades;

use Illuminate\Support\Facades\Facade;

class Communication extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'communication';
    }
}
