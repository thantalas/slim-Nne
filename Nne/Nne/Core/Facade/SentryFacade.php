<?php

namespace Nne\Core\Facade;

class SentryFacade extends \Nne\Core\Facade\Facade{
    protected static function getFacadeAccessor() { return 'sentry'; }
}