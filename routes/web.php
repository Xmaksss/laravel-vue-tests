<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'api'], function() {
    Route::get('tests', [
        'uses' => 'TestController@getTests'
    ]);
    Route::post('tests', [
        'uses' => 'TestController@postTest'
    ]);
    Route::post('tests/update', [
        'uses' => 'TestController@update'
    ]);
    Route::post('tests/delete', [
        'uses' => 'TestController@delete'
    ]);

    Route::get('questions', [
        'uses' => 'QuestionController@getQuestions'
    ]);
    Route::post('questions', [
        'uses' => 'QuestionController@postQuestion'
    ]);
    Route::post('questions/delete', [
        'uses' => 'QuestionController@delete'
    ]);
    Route::post('questions/parse', [
        'uses' => 'QuestionController@parse'
    ]);

    Route::get('testing', [
        'uses' => 'TestingController@getQuestions'
    ]);
    Route::post('testing/get-results', [
        'uses' => 'TestingController@getResults'
    ]);
});
