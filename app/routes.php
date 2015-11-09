<?php

/**
 * Sample group routing with user check in middleware
 */

Route::group(
    '/admin',
    function()  use ($app){
//         if(!Sentry::check()){

//             if(Request::isAjax()){
//                 Response::headers()->set('Content-Type', 'application/json');
//                 Response::setBody(json_encode(
//                     array(
//                         'success'   => false,
//                         'message'   => 'Session expired or unauthorized access.',
//                         'code'      => 401
//                     )
//                 ));
//                 App::stop();
//             }else{
//                 $redirect = Request::getResourceUri();
//                 Response::redirect(App::urlFor('adminlogin').'?redirect='.base64_encode($redirect));
//             }
//         }
    },
    function() use ($app) {
        /** sample namespaced controller */
        Route::get('/', 'Admin\AdminController:login')->name('adminlogin');

        foreach (Module::getModules() as $module) {
            $module->registerAdminRoute();
            $module->registerAdminMenu();
        }
    }
);


foreach (Module::getModules() as $module) {
    $module->registerPublicRoute();
}


/** default routing */
Route::get('/', 'HomeController:show')->name('home');

// Route::group(
// 		'/products',
// 		function() use ($app) {
// 			Route::get('/', 'HomeController:productIndex')->name('product');
// 			Route::get('/:item(-:slug)', 'HomeController:product')->conditions(array(
// 					'item'=>'\d+'
// 			))->name('product');
			
// 		}
// );

