<?php

use Illuminate\Routing\Router;

/** @var $router Router */

Route::get('login', ['as' => 'login', 'uses' => 'AuthController@showLogin']);
Route::post('login', ['uses' => 'AuthController@login']);
Route::post('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

$router->group(['middleware' => 'auth'], function() use ($router){
    Route::get('/', ['as' => 'home', 'uses' => 'FileBrowserController@home']);


    Route::get('browser', ['uses' => 'FileBrowserController@index']);
    Route::post('browser/mkdir', ['uses' => 'FileBrowserController@mkdir']);
    Route::post('browser/up', ['uses' => 'FileBrowserController@up']);
    Route::post('browser/dir', ['uses' => 'FileBrowserController@chdir']);
    Route::delete('browser/dir', ['uses' => 'FileBrowserController@rmdir']);
    Route::delete('browser/file', ['uses' => 'FileBrowserController@rm']);
    Route::post('browser/file', ['uses' => 'FileBrowserController@upload']);
    Route::get('browser/file/{path}', ['uses' => 'FileBrowserController@download'])->where('path', '.+');
});





