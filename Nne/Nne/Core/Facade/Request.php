<?php
namespace Nne\Core\Facade;

class Request extends Facade
{
	protected static function getFacadeAccessor() { return 'request'; }
}
