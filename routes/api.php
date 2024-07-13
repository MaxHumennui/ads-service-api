<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;

Route::prefix('visitor')
->controller(VisitorController::class)
->group(function () {
    Route::post('/track-impression', 'trackImpression');
    Route::post('/track-click', 'trackClick');
    Route::delete('/clean-old-entries', 'cleanOldEntries');
});

Route::prefix('ads')->group(function () {
    Route::get('/', 'index');
    Route::get('/statistics', 'statistics');
    Route::post('/', 'store');
    Route::put('/{id}', 'update');
    Route::delete('/{id}','destroy');
});
