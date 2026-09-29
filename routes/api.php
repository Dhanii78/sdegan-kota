<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
| Login tidak membutuhkan token.
|--------------------------------------------------------------------------
*/
Route::post('/v1/login', [ApiController::class, 'login'])
    ->name('api.login');


/*
|--------------------------------------------------------------------------
| API YANG MEMBUTUHKAN TOKEN SANCTUM
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/v1/user', [ApiController::class, 'user'])
        ->name('api.user');

    Route::post('/v1/logout', [ApiController::class, 'logout'])
        ->name('api.logout');

    Route::get(
        '/v1/get-komoditas-data',
        [ApiController::class, 'getKomoditasData']
    )->name('api.komoditas.data');

    Route::middleware('api.role:operator')->group(function () {

        Route::post(
            '/v1/insert_data',
            [ApiController::class, 'insertFromMobile']
        )->name('api.insert.data');

        Route::put(
            '/v1/komoditas/harga-hari-ini/{id}',
            [ApiController::class, 'updateHargaHariIni']
        );
    });

    Route::get(
        '/v1/datakomoditas',
        [ApiController::class, 'getDataKomoditas']
    );

    Route::get(
        '/v1/valuasi-harga-pangan',
        [ApiController::class, 'getValuasi']
    )->name('api.valuasi');

    Route::get(
        '/v1/splp-valuasi-harga-pangan',
        [ApiController::class, 'getValuasi2']
    )->name('api.splp.valuasi');
});