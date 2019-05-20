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

Route::group(['prefix' => 'v1', 'middleware'], function(){
    Route::get('applicants', 'ApplicantController@index')->name('api.applicants.get');
    Route::post('applicant', 'ApplicantController@store')->name('api.applicants.store');
    Route::put('applicant', 'ApplicantController@update')->name('api.applicant.update');
    Route::delete('applicant', 'ApplicantController@destroy')->name('api.applicant.delete');
});

//fallback route for 404
Route::fallback(function() {
    return response()->json(['message' => __('apiMsg.not_found')], 404);
})->name('api.fallback.404');