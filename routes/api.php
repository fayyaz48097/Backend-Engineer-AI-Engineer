<?php

use App\Http\Controllers\Ticket\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('tickets')->group(function () {

    Route::post('/', [TicketController::class, 'store']);

    Route::get('/', [TicketController::class, 'index']);

});

// Route::get('/test-ai', function (App\Services\AI\TicketAnalyzer $ai) {

//     return $ai->analyzeTicket(
//         'Cannot Login',
//         'I forgot my password and cannot login.'
//     );

// });