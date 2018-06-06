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

Use App\loans;
Use App\users;

/* Question 1 */
Route::get('loans/live', 'LoansController@live');

/* Question 2 */
Route::get('lender/{id}', 'UsersController@lender');

/* Question 3 */
Route::post('bids', 'BidsController@post');

/* Additional routes */
//Route::get('loans', 'LoansController@index');
//
//Route::get('loans/{id}', 'LoansController@show');
//
//Route::post('loans', 'LoansController@store');
//
//Route::put('loans/{id}', 'LoansController@update');
//
//Route::delete('loans/{id}', 'LoansController@delete');
//
///* Users */
//Route::get('users', 'UsersController@index');
//
//Route::get('users/all', 'UsersController@all');
//
//Route::get('users/{id}', 'UsersController@show');
//
//Route::post('users', 'UsersController@store');
//
//Route::put('users/{id}', 'UsersController@update');
//
//Route::delete('users/{id}', 'UsersController@delete');

