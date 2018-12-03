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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});
Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('subscribers/{email?}', 'SubscriberController@getSubscribers');
    Route::post('subscribers', 'SubscriberController@createSubscriber');
    Route::put('subscribers', 'SubscriberController@editSubscriber');
    Route::delete('subscribers', 'SubscriberController@deleteSubscriber');

    Route::get('fields','FieldsController@getAllFields');
    Route::get('fields/types','FieldsController@getAllFieldTypes');
    Route::post('fields','FieldsController@createFields');
    Route::put('fields','FieldsController@updateField');
    Route::delete('fields','FieldsController@deleteField');

}
);