<?php
namespace Nne\Core\Facade;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade
{
	protected static $slim;

	public static function registerAliases($aliases = null)
	{
		if (!$aliases) {
			$aliases = array(
				'App'      => 'Nne\Core\Facade\App',
				'Config'   => 'Nne\Core\Facade\Config',
				'Input'    => 'Nne\Core\Facade\Input',
				'Log'      => 'Nne\Core\Facade\Log',
				'Request'  => 'Nne\Core\Facade\Request',
				'Response' => 'Nne\Core\Facade\Response',
				'Route'    => 'Nne\Core\Facade\Route',
				'View'     => 'Nne\Core\Facade\View',
			);
		}

		foreach ($aliases as $alias => $class) {
			class_alias($class, $alias);
		}
	}

	public static function setFacadeApplication($app)
	{
		parent::$app = $app->container;
		self::$app   = $app->container;
		
		self::$slim = $app;
	}
}
