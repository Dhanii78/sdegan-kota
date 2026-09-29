<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SumberDataController;
use App\Http\Controllers\penggunaController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RekapOpController;
use App\Http\Controllers\RekapVeriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifikatorController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckRole;

Route::get('/notifications/check', [NotificationController::class, 'check'])
    ->middleware('auth')
    ->name('notifications.check');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECT BERDASARKAN ROLE
|--------------------------------------------------------------------------
|
| Setelah login berhasil, pengguna diarahkan ke sini.
|
| admin       → /admin/index
| operator    → /operator/index
| verifikator → /verifikator/index
|
*/

Route::get('/dashboard', function () {

    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    switch ($user->role) {

        case 'admin':
            return redirect()->route('dashboard_admin.index');

        case 'operator':
            return redirect()->route('dashboard_operator.index');

        case 'verifikator':
            return redirect()->route('dashboard_verifikator.index');

        default:
            abort(403, 'Role pengguna tidak dikenali.');
    }

})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| FORGOT PASSWORD
|--------------------------------------------------------------------------
*/

Route::get(
    '/forgot-password',
    [AuthController::class, 'showForgotForm']
)->name('forgot.password');

Route::post(
    '/forgot-password',
    [AuthController::class, 'processForgotPassword']
)->name('forgot.password.post');


/*
|--------------------------------------------------------------------------
| API DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get(
    '/api/komoditas',
    [DashboardController::class, 'getKomoditas']
);

Route::get(
    '/api/filter',
    [DashboardController::class, 'filter']
);

Route::get(
    '/api/loadTotalData',
    [DashboardController::class, 'AllData']
);

Route::get(
    '/api/grafik',
    [DashboardController::class, 'grafik']
);

Route::get(
    '/api/grafikAll',
    [DashboardController::class, 'grafikAll']
);

Route::get(
    '/api/grafikTerbaru',
    [DashboardController::class, 'grafikTerbaru']
);


/*
|--------------------------------------------------------------------------
| API TAMBAHAN
|--------------------------------------------------------------------------
*/

Route::get(
    '/api/datakomoditas',
    [ApiController::class, 'getDataKomoditas']
);

Route::get(
    '/api/get-komoditas-data',
    [ApiController::class, 'getKomoditasData']
)->name('api.komoditas.data');

Route::post(
    '/api/insert_data',
    [ApiController::class, 'insertFromMobile']
)->name('api.insert.data');

Route::put(
    '/api/komoditas/harga-hari-ini/{id}',
    [ApiController::class, 'updateHargaHariIni']
);

Route::post(
    '/api/login',
    [ApiController::class, 'login']
)->name('api.login');

Route::post(
    '/api/logout',
    [ApiController::class, 'logout']
)->name('api.logout');

Route::get(
    '/api/user',
    [ApiController::class, 'user']
)->name('api.user');

Route::get(
    '/api/valuasi-harga-pangan',
    [ApiController::class, 'getValuasi']
)->name('api.valuasi');

Route::get(
    '/api/splp-valuasi-harga-pangan',
    [ApiController::class, 'getValuasi2']
)->name('api.splp.valuasi');

Route::post(
    '/send-notifikasi',
    [ApiController::class, 'send']
);


/*
|--------------------------------------------------------------------------
| DASHBOARD UMUM
|--------------------------------------------------------------------------
*/

Route::get(
    '/dashboard_umum',
    [DashboardController::class, 'containerbesar']
)->name('dashboard_umum');

Route::get(
    '/unduh-pdf',
    [DashboardController::class, 'generatePDFumum']
)->name('unduh_umum.pdf');

Route::get(
    '/check-tanggal',
    [OperatorController::class, 'checkTanggal']
)->name('check.tanggal');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    CheckRole::class . ':admin'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/index',
        [AdminController::class, 'containerbesar']
    )->name('dashboard_admin.index');


    /*
    |--------------------------------------------------------------------------
    | Sumber Data
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/SumberData',
        [SumberDataController::class, 'index']
    )->name('SumberData.index');

    Route::get(
        '/admin/SumberData/create',
        [SumberDataController::class, 'create']
    )->name('SumberData.create');

    Route::post(
        '/admin/SumberData',
        [SumberDataController::class, 'store']
    )->name('SumberData.store');

    Route::get(
        '/admin/SumberData/{SumberData}',
        [SumberDataController::class, 'show']
    )->name('SumberData.show');

    Route::get(
        '/admin/SumberData/{SumberData}/edit',
        [SumberDataController::class, 'edit']
    )->name('SumberData.edit');

    Route::put(
        '/admin/SumberData/{SumberData}',
        [SumberDataController::class, 'update']
    )->name('SumberData.update');

    Route::delete(
        '/admin/SumberData/{SumberData}',
        [SumberDataController::class, 'destroy']
    )->name('SumberData.destroy');


    /*
    |--------------------------------------------------------------------------
    | Pengguna
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/pengguna',
        [penggunaController::class, 'index']
    )->name('pengguna.index');

    Route::get(
        '/admin/pengguna/create',
        [penggunaController::class, 'create']
    )->name('pengguna.create');

    Route::post(
        '/admin/pengguna',
        [penggunaController::class, 'store']
    )->name('pengguna.store');

    Route::get(
        '/admin/pengguna/{pengguna}',
        [penggunaController::class, 'show']
    )->name('pengguna.show');

    Route::get(
        '/admin/pengguna/{pengguna}/edit',
        [penggunaController::class, 'edit']
    )->name('pengguna.edit');

    Route::put(
        '/admin/pengguna/{pengguna}',
        [penggunaController::class, 'update']
    )->name('pengguna.update');

    Route::delete(
        '/admin/pengguna/{pengguna}',
        [penggunaController::class, 'destroy']
    )->name('pengguna.destroy');


    /*
    |--------------------------------------------------------------------------
    | Data Pangan Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/data_pangan',
        [DataController::class, 'index']
    )->name('dashboard_admin.sub_menu.data_pangan');

    Route::get(
        '/admin/input_data',
        [DataController::class, 'showForm']
    )->name('dashboard_admin.sub_menu.input_data');

    Route::post(
        '/admin/input_data',
        [DataController::class, 'insert']
    )->name('dashboard_admin.sub_menu.data.insert');

    Route::get(
        '/admin/edit_entry/{id}',
        [DataController::class, 'edit']
    )->name('dashboard_admin.sub_menu.data.edit');

    Route::put(
        '/admin/data/{id}',
        [DataController::class, 'update']
    )->name('dashboard_admin.sub_menu.data.update');

    Route::delete(
        '/admin/sub_menu/data/{id}/destroy',
        [DataController::class, 'destroy']
    )->name('dashboard_admin.sub_menu.data.destroy');

    Route::post(
        '/admin/data-pangan/bulk-delete',
        [DataController::class, 'bulkDelete']
    )->name('dashboard_admin.sub_menu.data.bulkDelete');


    /*
    |--------------------------------------------------------------------------
    | Verifikasi Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/verify_data',
        [DataController::class, 'verifyadmin']
    )->name('dashboard_admin.sub_menu.verify_data');

    Route::put(
        '/admin/verifikasi/data/{id}',
        [DataController::class, 'updateStatusVerifikasi']
    )->name('dashboard_admin.sub_menu.update_status_verifikasi');

    Route::post(
        '/admin/kembalikan-data',
        [DataController::class, 'kembalikanData']
    )->name('dashboard_admin.sub_menu.kembalikan_data');

    Route::post(
        '/admin/save_data',
        [AdminController::class, 'saveData']
    )->name('dashboard_admin.sub_menu.save_data');


    /*
    |--------------------------------------------------------------------------
    | Rekap Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/rekap',
        [RekapController::class, 'index']
    )->name('rekap.admin');

    Route::get(
        '/export/rekap',
        [RekapController::class, 'exportRekap']
    )->name('export.rekap');

    Route::get(
        '/export/het',
        [RekapController::class, 'exportHet']
    )->name('export.het');


    /*
    |--------------------------------------------------------------------------
    | PDF Admin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/unduh-pdf',
        [AdminController::class, 'generatePdf']
    )->name('unduh.pdf');


    /*
    |--------------------------------------------------------------------------
    | Turunkan Status
    |--------------------------------------------------------------------------
    */

    Route::put(
        '/data/turunkan/{id}',
        [DataController::class, 'turunkanStatus']
    )->name('dashboard_admin.sub_menu.data.turunkan');


    /*
    |--------------------------------------------------------------------------
    | Hapus Rekap Berdasarkan Tanggal
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/rekap/delete-tanggal',
        [RekapController::class, 'deleteTanggal']
    )->name('rekap.deleteTanggal');

});


/*
|--------------------------------------------------------------------------
| OPERATOR
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    CheckRole::class . ':operator'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Operator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/operator/index',
        [OperatorController::class, 'containerbesar']
    )->name('dashboard_operator.index');


    /*
    |--------------------------------------------------------------------------
    | Data Operator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/operator/data_pangan',
        [DataController::class, 'indexoperator']
    )->name('dashboard_operator.data_pangan');

    Route::get(
        '/operator/input_data',
        [DataController::class, 'showFormoperator']
    )->name('dashboard_operator.input_data');

    Route::post(
        '/operator/input_data',
        [DataController::class, 'insertoperator']
    )->name('dashboard_operator.data.insert');

    Route::get(
        '/operator/edit_entry/{id}',
        [DataController::class, 'editoperator']
    )->name('dashboard_operator.data.edit');

    Route::put(
        '/operator/data/{id}',
        [DataController::class, 'updateoperator']
    )->name('dashboard_operator.data.update');

    Route::post(
        '/operator/data-pangan/bulk-delete',
        [DataController::class, 'bulkDelete']
    )->name('dashboard_operator.data.bulkDelete');


    /*
    |--------------------------------------------------------------------------
    | Rekap Operator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/operator/rekap',
        [RekapOpController::class, 'index']
    )->name('rekap.operator');

    Route::get(
        '/export/rekapop',
        [RekapOpController::class, 'exportRekapOp']
    )->name('export.rekap_op');

    Route::get(
        '/export/het_op',
        [RekapOpController::class, 'exportHetOp']
    )->name('export.het_op');


    /*
    |--------------------------------------------------------------------------
    | PDF Operator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/operator/unduh-pdf',
        [OperatorController::class, 'generatePdfop']
    )->name('unduh_op.pdf');

});


/*
|--------------------------------------------------------------------------
| VERIFIKATOR
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    CheckRole::class . ':verifikator'
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Verifikator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/verifikator/index',
        [VerifikatorController::class, 'containerbesar']
    )->name('dashboard_verifikator.index');


    /*
    |--------------------------------------------------------------------------
    | Verifikasi Data
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/verifikator/verify_data',
        [DataController::class, 'verifyverifikator']
    )->name('dashboard_verifikator.verify_data');


    /*
    |--------------------------------------------------------------------------
    | Simpan Data Verifikator
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/verifikator/save',
        [VerifikatorController::class, 'saveDataverifikator']
    )->name('dashboard_verifikator.save_data');


    /*
    |--------------------------------------------------------------------------
    | Update Status Verifikasi
    |--------------------------------------------------------------------------
    */

    Route::put(
        '/verifikator/verifikasi/data/{id}',
        [DataController::class, 'updateStatusVerifikasiVer']
    )->name('dashboard_verifikator.update_status_verifikasi');


    /*
    |--------------------------------------------------------------------------
    | Kembalikan Data
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/verifikator/kembalikan-data',
        [DataController::class, 'kembalikanDataVer']
    )->name('dashboard_verifikator.kembalikan_data');


    /*
    |--------------------------------------------------------------------------
    | Rekap Verifikator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/verifikator/rekap',
        [RekapVeriController::class, 'index']
    )->name('rekap.verifikator');

    Route::get(
        '/export/rekap_veri',
        [RekapVeriController::class, 'exportRekap']
    )->name('export.rekap_veri');

    Route::get(
        '/export/het_veri',
        [RekapVeriController::class, 'exportHet']
    )->name('export.het_veri');


    /*
    |--------------------------------------------------------------------------
    | PDF Verifikator
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/verifikator/unduh-pdf',
        [VerifikatorController::class, 'generatePdfveri']
    )->name('unduh_veri.pdf');

});