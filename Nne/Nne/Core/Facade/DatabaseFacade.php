<?php

namespace Nne\Core\Facade;

class DatabaseFacade extends \Nne\Core\Facade\Facade{
    protected static function getFacadeAccessor() { return 'db'; }
}