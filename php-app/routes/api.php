<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TaxProfileController;
use App\Http\Controllers\API\InvoiceController;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome! don\'t forget to authenticate for user, tax-profile and invoice routes',
    ]);
});

Route::middleware(['api.key'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::resource('users.tax-profiles', TaxProfileController::class);
    Route::resource('users.tax-profiles.invoices', InvoiceController::class);
});