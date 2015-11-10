<?php

/**
 * Hook, filter, etc should goes here
 */

/**
 * error handling sample
 */
 $app->error(function() use ($app){
 	$Controller = new \ErrorController();
 	$Controller->error();
});
$app->notFound(function () use ($app) {
 	$Controller = new \ErrorController();
 	$Controller->notFound();
});

/**************************************** CONFIGURATIONS ********************/
$app->config('css.version','0.1');
$app->config('js.version','0.1');
$app->config('fe.asset.forceless',false);
$app->config('fe.asset.css',array(
		'bootstrap.min.css',
		'app.less',
	)
);
$app->config('fe.asset.js',array(
		'bootstrap.min.js',
	)
);
/**\.************************************** CONFIGURATIONS ********************/
 

/**************************************** HOOCKS ********************/
/**
 * hook per le lingue
 * in questo modo nelle rotte nn serve metter nessun riferimento alla lingua
 */
$app->hook('slim.before', function () use ($app) {

	$availableLangs = $app->config('languages');
	$env = $app->environment();
	// setup default lang based on first in the list
	$lang = $app->config('DEFAULT_LANGUAGE');
	// if they are accessing the root, you could try and direct them to the correct language
	if ($env['PATH_INFO'] == '/') {
		if (isset($env['ACCEPT_LANGUAGE'])) {
			// try and auto-detect, find the language with the lowest offset as they are in order of priority
			$priority_offset = strlen($env['ACCEPT_LANGUAGE']);
			foreach($availableLangs as $iso2 =>$availableLang) {
				$i = strpos($env['ACCEPT_LANGUAGE'], $iso2);
				if ($i !== false && $i < $priority_offset) {
					$priority_offset = $i;
					$lang = $iso2;
				}
			}
		}
	} else {
		$pathInfo = $env['PATH_INFO'] . (substr($env['PATH_INFO'], -1) !== '/' ? '/' : '');
		// extract lang from PATH_INFO
		foreach($availableLangs as $iso2 => $availableLang) {
			$match = '/'.$iso2;
			if (strpos($pathInfo, $match.'/') === 0) {
				$lang = $iso2;
				$env['PATH_INFO'] = substr($env['PATH_INFO'], strlen($match));
				if (strlen($env['PATH_INFO']) == 0) {
					$env['PATH_INFO'] = '/';
				}
			}
		}
	}
	$app->view()->setLang($lang);
	$app->view()->setAvailableLangs($availableLangs);
	$app->view()->setPathInfo($env['PATH_INFO']);
});
/**	\.************************************** HOOCKS ********************/
