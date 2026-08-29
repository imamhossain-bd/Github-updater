<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/github-pull', function (Request $request) {
    \Artisan::call('app:update-from-github');

    return response()->json([
        'status' => 'updated',
        'output' => \Artisan::output(),
    ]);
});