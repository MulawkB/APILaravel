<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\PostController as PostControllerV1;
use App\Http\Controllers\API\V2\PostController as PostControllerV2;


Route::get('/ping', function () {
return response()->json([
'message' => 'pong',
]);
});

Route::prefix('v1')->group(function () {
Route::apiResource('posts', \App\Http\Controllers\API\V1\PostController::class);
});
Route::prefix('v2')->group(function () {
Route::apiResource('posts', \App\Http\Controllers\API\V2\PostController::class);
});