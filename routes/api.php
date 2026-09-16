<?php

use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\Ticket\TicketEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('tickets')->group(function () {

    Route::post('/', [TicketController::class, 'store']);

    Route::get('/', [TicketController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Email automation
    |--------------------------------------------------------------------------
    |
    | WARNING: unauthenticated, like the rest of this API. Approving an email
    | causes mail to go to a real customer, so put these behind auth (e.g.
    | ->middleware('auth:sanctum')) before enabling AI_EMAIL_SENDING_ENABLED
    | in production.
    |
    */
    Route::prefix('{ticket}/email')->group(function () {

        Route::get('/', [TicketEmailController::class, 'show']);

        Route::post('/generate', [TicketEmailController::class, 'generate']);

        Route::post('/approve', [TicketEmailController::class, 'approve']);

        Route::post('/reject', [TicketEmailController::class, 'reject']);

    });

});
