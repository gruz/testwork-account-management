<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/ping', function () {
        return response()->json(['Version 1']);
    });
    Route::post('/amount', [TransactionController::class, 'create']);
    Route::get('/transaction/{transaction_id}', [TransactionController::class, 'getByID']);
    Route::get('/balance/{account_id}', [AccountController::class, 'getBalance']);
    Route::get('/max_transaction_volume', [AccountController::class, 'getMaxTransactionVolume']);
});
