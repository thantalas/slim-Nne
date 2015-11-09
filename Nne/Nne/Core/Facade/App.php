<?php
namespace Nne\Core\Facade;

class App extends Facade
{
	protected static function getFacadeAccessor() { return self::$slim; }

	public static function make($key)
	{
		return self::$app[$key];
	}
}
