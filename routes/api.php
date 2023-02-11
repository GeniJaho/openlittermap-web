<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
Route::group(['prefix' => 'v2', 'middleware' => 'auth:api'], function(){

    // Route::get('/user/setup-intent', 'API\UserController@getSetupIntent');

    Route::get('/photos/web/index', 'API\WebPhotosController@index');

    Route::get('/photos/web/load-more', 'API\WebPhotosController@loadMore');
});

Route::get('/global/stats-data', 'API\GlobalStatsController@index');
Route::get('/mobile-app-version', 'API\MobileAppVersionController');

Route::post('add-tags', 'ApiPhotosController@addTags')
    ->middleware('auth:api');

// Check if current token is valid
Route::post('/validate-token', function(Request $request) {
    return ['message' => 'valid'];
})->middleware('auth:api');

// Create Account
Route::post('/register', 'ApiRegisterController@register');

// Fetch User
Route::get('/user', function (Request $request) {
    return Auth::guard('api')->user()->append('position', 'xp_redis');
});

// Reset Password
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');

// Upload Photos
Route::post('/photos/submit', 'ApiPhotosController@store');

// Upload Photos with tags - old route
Route::post('/photos/submit-with-tags', 'ApiPhotosController@uploadWithOrWithoutTags')
    ->middleware('auth:api');

// Upload Photos with tags - old route
Route::post('/photos/upload-with-tags', 'ApiPhotosController@uploadWithOrWithoutTags')
    ->middleware('auth:api');

// Upload Photos with or without tags -  new route
Route::post('/photos/upload/with-or-without-tags', 'ApiPhotosController@uploadWithOrWithoutTags')
    ->middleware('auth:api');

// Delete Photos
Route::delete('/photos/delete', 'ApiPhotosController@deleteImage');

// Check for any photos uploaded on web
Route::get('/check-web-photos', 'ApiPhotosController@check')
    ->middleware('auth:api');

/**
 * Settings
 */
Route::post('/settings/privacy/maps/name', 'ApiSettingsController@mapsName')
    ->middleware('auth:api');

Route::post('/settings/privacy/maps/username', 'ApiSettingsController@mapsUsername')
    ->middleware('auth:api');

Route::post('/settings/privacy/leaderboard/name', 'ApiSettingsController@leaderboardName')
    ->middleware('auth:api');

Route::post('/settings/privacy/leaderboard/username', 'ApiSettingsController@leaderboardUsername')
    ->middleware('auth:api');

Route::post('/settings/privacy/createdby/name', 'ApiSettingsController@createdByName')
    ->middleware('auth:api');

Route::post('/settings/privacy/createdby/username', 'ApiSettingsController@createdByUsername')
    ->middleware('auth:api');

Route::post('/settings/update', 'ApiSettingsController@update')
    ->middleware('auth:api');

Route::post('/settings/privacy/toggle-previous-tags', 'ApiSettingsController@togglePreviousTags')
    ->middleware('auth:api');
Route::patch('/settings', 'SettingsController@update')->middleware('auth:api');

// Teams
Route::prefix('/teams')->group(function () {
    Route::get('/members', 'API\TeamsController@members');
    Route::get('/leaderboard', 'Teams\TeamsLeaderboardController@index')->middleware('auth:api');
    Route::get('/list', 'API\TeamsController@list');
    Route::get('/types', 'API\TeamsController@types');
    Route::patch('/update/{team}', 'API\TeamsController@update');
    Route::post('/active', 'API\TeamsController@setActiveTeam');
    Route::post('/create', 'API\TeamsController@create');
    Route::post('/download', 'API\TeamsController@download');
    Route::post('/inactivate', 'API\TeamsController@inactivateTeams');
    Route::post('/join', 'API\TeamsController@join');
    Route::post('/leave', 'API\TeamsController@leave');
    Route::post('/leaderboard/visibility', 'Teams\TeamsLeaderboardController@toggle')->middleware('auth:api');
});
