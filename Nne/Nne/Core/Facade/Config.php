<?php
namespace Nne\Core\Facade;

class Config extends Facade
{
	protected static function getFacadeAccessor() { return self::$slim; }

	public static function read($key)
	{
	return self::get($key);
	}
	public static function get($key)
	{
		return self::$slim->config($key);
	}

	public static function set($key, $value)
	{
		return self::$slim->config($key, $value);
	}
	public static function write($key, $value)
	{
		return self::$slim->config($key, $value);
	}
}
