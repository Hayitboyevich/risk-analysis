<?php

use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IllegalObjectController;


Route::post('/login', [LoginController::class, 'login']);
Route::post('/auth', [LoginController::class, 'auth']);
Route::post('challenge', [LoginController::class, 'challenge']);
Route::post('check-user', [LoginController::class, 'checkUser']);

Route::group([
    'middleware' => ['role_check', 'auth:api', 'refresh_token']
], function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::group(['prefix' => 'illegal'], function () {
        Route::get('objects', [IllegalObjectController::class, 'objectsList']);
        Route::get('statistics', [IllegalObjectController::class, 'getStatistics']);
        Route::get('questions/{id}', [IllegalObjectController::class, 'questionList']);
        Route::get('object/{id}', [IllegalObjectController::class, 'getObject']);
        Route::get('districts', [IllegalObjectController::class, 'districtList']);
        Route::post('create-object', [IllegalObjectController::class, 'createObject']);
        Route::post('update-object', [IllegalObjectController::class, 'updateObject']);
//        Route::post('save-object/{id}', [IllegalObjectController::class, 'saveObject']);
        Route::post('update-checklist', [IllegalObjectController::class, 'updateCheckList']);
        Route::get('object-history/{id}', [IllegalObjectController::class, 'objectHistory']);
        Route::get('check-list-history/{id}', [IllegalObjectController::class, 'checklistHistory']);
    });
});
