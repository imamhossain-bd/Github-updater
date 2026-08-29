<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/github-pull', function (Request $request) {
    \Artisan::call('app:update-from-github');

    $output = preg_replace('/\x1B\[[0-9;]*[a-zA-Z]/', '', \Artisan::output());

    return response()->json([
        'status' => 'updated',
        'output' => $output,
    ]);
});