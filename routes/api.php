<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StripeDataController;

Route::middleware(['api.token'])->group(function () {
    Route::post('/stripe/charge', [StripeDataController::class, 'storeCharge']);
    Route::post('/stripe/refund', [StripeDataController::class, 'storeRefund']);
});

