<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\PostController as PostControllerV1;


Route::get('/ping', function () {
return response()->json([
'message' => 'pong',
]);
});

Route::prefix('v1')->group(function () {
Route::apiResource('posts', PostControllerV1::class);
});