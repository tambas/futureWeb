<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace' => 'Api', 'domain' => Config::get('dofus.domain.main')], function () {

            Route::get('/get/posts', [
                'uses' => 'PostController@get'
            ])->where('number', '[0-9]+');
});