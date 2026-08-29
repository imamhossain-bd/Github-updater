<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/github-webhook', function (Request $request) {
    $secret = config('github-updater.webhook_secret');
    $signature = $request->header('X-Hub-Signature-256');

    if ($secret) {
        $hash = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        if (!hash_equals($hash, $signature ?? '')) {
            abort(403, 'Invalid signature');
        }
    }

    \Artisan::call('app:update-from-github');

    return response()->json(['status' => 'updated']);
});