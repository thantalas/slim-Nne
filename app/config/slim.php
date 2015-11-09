<?php

$config['slim'] = array(
    // Modular
    'modular'       => true,

    // Application
    'mode'          => (APP_MODE =='prod') ? 'production' : 'development',

    // Debugging
    'debug'         => (APP_MODE =='prod') ? false : true,

    // Logging
    'log.writer'    => null,
    'log.level'     => (APP_MODE =='prod') ? \Slim\Log::ERROR : \Slim\Log::DEBUG,
    'log.enabled'   => true,

    //View
    //'view'          => new \Nne\View(),
    'templates.path'=> APP_PATH.'Views',
    'templates.admin.path'=> ADMIN_TEMPLATE_PATH,
    'language.path'=> APP_PATH.'languages',
    'asset.path'=> ASSET_PATH,
    'css.path'=> CSS_PATH,
    'less.path'=> LESS_PATH,

    // HTTP
    'http.version' => '1.1',

    // Routing
    'routes.case_sensitive' => true
);