<?php

use Illuminate\Support\Facades\Route;

Route::get('/{path?}', function () {
    $indexPath = public_path('index.html');

    if (! file_exists($indexPath)) {
        return response()->json([
            'message' => 'Frontend not found. Please run "npm run build" in the frontend directory.',
            'error' => 'FRONTEND_NOT_BUILT',
        ], 404);
    }

    return file_get_contents($indexPath);
})->where('path', '.*');
