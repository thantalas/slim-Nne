<?php

session_cache_limiter(false);
session_start();

require VENDOR_PATH.'autoload.php';

/**
 * Load the configuration
 */
$config = array(
    'path.root'     => ROOT_PATH,
    'path.public'   => PUBLIC_PATH,
    'path.app'      => APP_PATH,
    'path.module'   => APP_MODULE_PATH
);

foreach (glob(APP_PATH.'config/*.php') as $configFile) {
    require $configFile;
}

/** Merge cookies config to slim config */
if(isset($config['cookies'])){
    foreach($config['cookies'] as $configKey => $configVal){
        $config['slim']['cookies.'.$configKey] = $configVal;
    }
}

/**
 * Initialize Slim and SlimStarter application
 */
//$app        = new \Slim\Slim($config['slim']);
$app        = new \Nne\NneSlim($config['slim']);

$env = $app->environment();
if(preg_match("/^\/admin/",$env['PATH_INFO'])) {
	$app->config('isAdmin',true);
}
$app->view(
		new \Nne\View($app,
				new \Nne\Libs\Translator($app->getLog(), $config['slim']['language.path'])
		)
);

$starter    = new \Nne\Bootstrap($app);
$starter->setConfig($config);



    /** boot up SlimStarter */
    $starter->boot();

    /** Setting up Slim hooks and middleware */
    require APP_PATH.'bootstrap/app.php';
    
    /** registering modules */
    foreach ($config['modules'] as $module => $moduleConfiguration) {
    	if($moduleConfiguration['active']){
	        $className = basename($module);
	        $moduleBootstrap = "\\$className\\Initialize";
	
	        $app->module->register(new $moduleBootstrap);
    	}
    }


    $app->module->boot();

    /** Start the route */
    require APP_PATH.'routes.php';


return $starter;