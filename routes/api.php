<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WhatsAppController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute Webhook WhatsApp Cloud API
Route::get('/wa/webhook', [WhatsAppController::class, 'verifyWebhook']);
Route::post('/wa/webhook', [WhatsAppController::class, 'handleWebhook']);