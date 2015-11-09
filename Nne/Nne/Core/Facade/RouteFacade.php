<?php

namespace Nne\Core\Facade;

class RouteFacade extends \Nne\Core\Facade\Route{

    /**
     * Route resource to single controller
     */
    public static function resource(){
        $arguments  = func_get_args();
        $path       = $arguments[0];
        $controller = end($arguments);
        $options= array();
        // check if opstions is passed
        if(count($arguments)== 3){
        	$options = $arguments[1];
        	unset($arguments[1]);
        }
        $prefix = (App::isAdmin()) ? 'admin_' : '';
        $resourceRoutes = array(
            'get'           => array(
                'pattern'       => "$path",
                'method'        => 'get',
                'handler'       => "$controller:{$prefix}index"
            ),
            'get_paginate'  => array(
                'pattern'       => "$path/page/:page",
                'method'        => 'get',
                'handler'       => "$controller:{$prefix}index"
            ),
            'get_create'    => array(
                'pattern'       => "$path/create",
                'method'        => 'get',
                'handler'       => "$controller:{$prefix}create"
            ),
            'get_edit'      => array(
                'pattern'       => "$path/:id/edit",
                'method'        => 'get',
                'handler'       => "$controller:{$prefix}edit"
            ),
            'get_show'      => array(
                'pattern'       => "$path/:id",
                'method'        => 'get',
                'handler'       => "$controller:{$prefix}show"
            ),
            'post'          => array(
                'pattern'       => "$path",
                'method'        => 'post',
                'handler'       => "$controller:{$prefix}store"
            ),
            'put'           => array(
                'pattern'       => "$path/:id",
                'method'        => 'put',
                'handler'       => "$controller:{$prefix}update"
            ),
            'delete'        => array(
                'pattern'       => "$path/:id",
                'method'        => 'delete',
                'handler'       => "$controller:{$prefix}destroy"
            )
        );

        foreach ($resourceRoutes as $key => $route) {
            $callable   = $arguments;

            //put edited pattern to the top stack
            array_shift($callable);
            array_unshift($callable, $route['pattern']);

            //put edited controller to the bottom stack
            array_pop($callable);
            array_push($callable, $route['handler']);
			if(empty($options['name'])){
				$name  =  $prefix.$key.'-'.ltrim($path,"/");
			}else{
				$name =  $prefix.$key.'-'.$options['name'];
			}
			call_user_func_array(array(self::$slim, $route['method']), $callable)->name($name);
        }
    }

    /**
     * Map route to all public controller method
     *
     * with
     * Route::get('/prefix', 'ClassController')
     *
     * this will map
     * GET  domain.com/prefix -> ClassController::getIndex
     * POST domain.com/prefix -> ClassCOntroller::postIndex
     * PUT  domain.com/prefix -> ClassCOntroller::putIndex
     */
    public static function controller(){

        $arguments  = func_get_args();
        $path       = $arguments[0];
        $controller = end($arguments);

        $class      = new \ReflectionClass($controller);
        $controllerMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $uppercase  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        foreach ($controllerMethods as $method) {
            if(substr($method->name, 0, 2) != '__'){
                $methodName = $method->name;
                $callable   = $arguments;

                $pos        = strcspn($methodName, $uppercase);
                $httpMethod = substr($methodName, 0, $pos);
                $ctrlMethod = lcfirst(strpbrk($methodName, $uppercase));

                if($ctrlMethod == 'index'){
                    $pathMethod = $path;
                }else if($httpMethod == 'get'){
                    $pathMethod = "$path/$ctrlMethod(/:params+)";
                }else{
                    $pathMethod = "$path/$ctrlMethod";
                }

                //put edited pattern to the top stack
                array_shift($callable);
                array_unshift($callable, $pathMethod);

                //put edited controller to the bottom stack
                array_pop($callable);
                array_push($callable, "$controller:$methodName");

                call_user_func_array(array(self::$slim, $httpMethod), $callable);
            }
        }
    }
}