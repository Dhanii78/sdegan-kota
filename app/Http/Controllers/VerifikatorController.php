<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\sumber_data;
use App\Models\rekap;
use App\Models\Data;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;

class VerifikatorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD VERIFIKATOR
    |--------------------------------------------------------------------------
    */

    public function containerbesar(Request $request)
    {
        $tanggalFilter = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $latestRekap = rekap::whereDate('tanggal', $tanggalFilter)
            ->orderBy('tanggal', 'desc')
            ->first();

        if (!$latestRekap) {
            $tanggalKemarin = Carbon::parse($tanggalFilter)
                ->subDay()
                ->format('Y-m-d');

            $latestRekap = rekap::whereDate(
                'tanggal',
                $tanggalKemarin
            )
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        if (!$latestRekap) {
            $latestRekap = rekap::orderBy(
                'tanggal',
                'desc'
            )->first();
        }

        if (!$latestRekap) {
            return view('dashboard_verifikator.index', [
                'sumber_data' => sumber_data::all(),
                'rekap' => collect(),
                'latestRekap' => null,
                'previousRekap' => null,
                'sevenDayAverage' => [],
                'indicators' => [],
                'averageIndicators' => [],
                'priceChanges' => [],
                'komoditasColumns' => [],
                'tanggal' => $tanggalFilter,
            ])->with(
                'error',
                'Belum ada data rekap harga pangan yang divalidasi.'
            );
        }

        $tanggal = $latestRekap->tanggal;

        $previousRekap = rekap::whereDate(
            'tanggal',
            Carbon::parse($tanggal)->subDay()
        )->first();

        $allColumns = Schema::getColumnListing('rekap');

        $komoditasColumns = array_filter(
            $allColumns,
            function ($col) {
                return !in_array(
                    $col,
                    ['id', 'tanggal']
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | RATA-RATA 7 HARI
        |--------------------------------------------------------------------------
        */

        $sevenDayAverage = [];

        foreach ($komoditasColumns as $column) {

            $prices = rekap::whereBetween(
                'tanggal',
                [
                    Carbon::parse($tanggal)
                        ->subDays(6)
                        ->format('Y-m-d'),

                    $tanggal
                ]
            )
                ->orderBy('tanggal', 'desc')
                ->get([$column]);

            $filteredPrices = $prices->filter(
                fn($p) => is_numeric($p->$column)
            );

            $sevenDayAverage[$column] =
                $filteredPrices->count() > 0
                    ? $filteredPrices->avg($column)
                    : null;
        }

        /*
        |--------------------------------------------------------------------------
        | PERUBAHAN HARGA
        |--------------------------------------------------------------------------
        */

        $priceChanges = [];

        foreach ($komoditasColumns as $column) {

            $latest = $latestRekap->$column ?? null;
            $prev = $previousRekap->$column ?? null;

            if (
                is_numeric($latest) &&
                is_numeric($prev)
            ) {

                $priceChanges[$column] =
                    $latest > $prev
                        ? 'up'
                        : (
                            $latest < $prev
                                ? 'down'
                                : 'same'
                        );

            } else {

                $priceChanges[$column] = null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INDIKATOR
        |--------------------------------------------------------------------------
        */

        $sumber_data = sumber_data::all();

        $indicators = $this->calculateIndicators(
            $latestRekap,
            $sumber_data
        );

        $averageIndicators =
            $this->calculateAverageIndicators(
                $sevenDayAverage,
                $sumber_data
            );

        return view('dashboard_verifikator.index', [
            'sumber_data' => $sumber_data,

            'rekap' => rekap::whereDate(
                'tanggal',
                $tanggal
            )
                ->orderBy('tanggal', 'desc')
                ->get(),

            'latestRekap' => $latestRekap,

            'previousRekap' => $previousRekap,

            'sevenDayAverage' => $sevenDayAverage,

            'indicators' => $indicators,

            'averageIndicators' => $averageIndicators,

            'priceChanges' => $priceChanges,

            'komoditasColumns' => $komoditasColumns,

            'tanggal' => $tanggal,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG INDIKATOR
    |--------------------------------------------------------------------------
    */

    private function calculateIndicators(
        $latestRekap,
        $sumber_data
    ) {
        $indicators = [];

        foreach ($sumber_data as $data) {

            $namaKomoditas =
                $data->nama_komoditas;

            $hargaH =
                $latestRekap->{$namaKomoditas}
                ?? null;

            if ($hargaH === null) {

                $indicators[$namaKomoditas] =
                    'N/A';

                continue;
            }

            if ($hargaH < $data->hethap) {

                $waspadaLimit =
                    $data->hethap *
                    (1 + $data->waspada / 100);

                $intervensiLimit =
                    $data->hethap *
                    (1 + $data->intervensi / 100);

                if (
                    $hargaH >
                    $intervensiLimit
                ) {

                    $indicators[$namaKomoditas] =
                        'Intervensi';

                } elseif (
                    $hargaH >
                    $waspadaLimit
                ) {

                    $indicators[$namaKomoditas] =
                        'Waspada';

                } else {

                    $indicators[$namaKomoditas] =
                        'Aman';
                }

            } else {

                if (
                    $hargaH >
                    $data->harga_intervensi
                ) {

                    $indicators[$namaKomoditas] =
                        'Intervensi';

                } elseif (
                    $hargaH >
                    $data->harga_waspada
                ) {

                    $indicators[$namaKomoditas] =
                        'Waspada';

                } else {

                    $indicators[$namaKomoditas] =
                        'Aman';
                }
            }
        }

        return $indicators;
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG INDIKATOR RATA-RATA 7 HARI
    |--------------------------------------------------------------------------
    */

    private function calculateAverageIndicators(
        $sevenDayAverage,
        $sumber_data
    ) {
        $averageIndicators = [];

        foreach ($sumber_data as $data) {

            $namaKomoditas =
                $data->nama_komoditas;

            $averagePrice =
                $sevenDayAverage[$namaKomoditas]
                ?? null;

            if ($averagePrice !== null) {

                if ($averagePrice < $data->hethap) {

                    $waspadaLimit =
                        $data->hethap *
                        (1 + $data->waspada / 100);

                    $intervensiLimit =
                        $data->hethap *
                        (1 + $data->intervensi / 100);

                    if (
                        $averagePrice >
                        $intervensiLimit
                    ) {

                        $averageIndicators[$namaKomoditas] =
                            'Intervensi';

                    } elseif (
                        $averagePrice >
                        $waspadaLimit
                    ) {

                        $averageIndicators[$namaKomoditas] =
                            'Waspada';

                    } else {

                        $averageIndicators[$namaKomoditas] =
                            'Aman';
                    }

                } else {

                    if (
                        $averagePrice >
                        $data->harga_intervensi
                    ) {

                        $averageIndicators[$namaKomoditas] =
                            'Intervensi';

                    } elseif (
                        $averagePrice >
                        $data->harga_waspada
                    ) {

                        $averageIndicators[$namaKomoditas] =
                            'Waspada';

                    } else {

                        $averageIndicators[$namaKomoditas] =
                            'Aman';
                    }
                }

            } else {

                $averageIndicators[$namaKomoditas] =
                    'N/A';
            }
        }

        return $averageIndicators;
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN HASIL VERIFIKASI
    |--------------------------------------------------------------------------
    |
    | ATURAN:
    |
    | Valid
    |   -> Catatan tidak wajib
    |
    | Tidak_valid / Tidak Valid
    |   -> Catatan WAJIB diisi
    |
    |--------------------------------------------------------------------------
    */

    public function saveDataverifikator(
        Request $request
    ) {
        $dataList =
            $request->input('data', []);

        $action =
            $request->input('action');


        /*
        |--------------------------------------------------------------------------
        | CEK DATA
        |--------------------------------------------------------------------------
        */

        if (empty($dataList)) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Tidak ada data yang dikirim.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SETIAP DATA
        |--------------------------------------------------------------------------
        */

        foreach ($dataList as $index => $data) {

            /*
            |--------------------------------------------------------------------------
            | STATUS WAJIB DIPILIH
            |--------------------------------------------------------------------------
            */

            if (
                empty(
                    $data['status_verifikasi']
                    ?? null
                )
            ) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Status verifikasi untuk semua komoditas wajib dipilih.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | TIDAK VALID WAJIB ADA CATATAN
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $data['status_verifikasi'],
                    [
                        'Tidak_valid',
                        'Tidak Valid'
                    ]
                )
            ) {

                $catatan =
                    trim(
                        $data['catatan_verifikasi']
                        ?? ''
                    );

                if ($catatan === '') {

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(
                            'error',
                            'Catatan wajib diisi untuk data yang ditolak/tidak valid.'
                        );
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL
        |--------------------------------------------------------------------------
        */

        $totalData =
            count($dataList);

        $validCount =
            collect($dataList)
                ->where(
                    'status_verifikasi',
                    'Valid'
                )
                ->count();

        $rejectedCount =
            collect($dataList)
                ->filter(
                    function ($data) {

                        return in_array(
                            $data['status_verifikasi'],
                            [
                                'Tidak_valid',
                                'Tidak Valid'
                            ]
                        );
                    }
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA VERIFIKASI
        |--------------------------------------------------------------------------
        */

        foreach ($dataList as $data) {

            Data::where('tanggal', $data['tanggal'])
                ->where('nama_komoditas', $data['nama_komoditas'])
                ->update(
                [
                    'harga_het' =>
                        $data['harga_het']
                        ?? null,

                    'harga_kemarin' =>
                        $data['harga_kemarin']
                        ?? null,

                    'harga_hari_ini' =>
                        $data['harga_hari_ini']
                        ?? null,

                    'status_verifikasi' =>
                        $data['status_verifikasi'],

                    'catatan_verifikasi' =>
                        !empty(
                            $data['catatan_verifikasi']
                            ?? null
                        )
                            ? trim(
                                $data['catatan_verifikasi']
                            )
                            : null,
                    'updated_at' => now(),
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATA DIKEMBALIKAN
        |--------------------------------------------------------------------------
        */

        if ($action === 'return') {

            return redirect()
                ->route(
                    'dashboard_verifikator.verify_data'
                )
                ->with([
                    'success' =>
                        'Data berhasil dikembalikan.',

                    'verification_total' =>
                        $totalData,

                    'verification_valid' =>
                        $validCount,

                    'verification_rejected' =>
                        $rejectedCount,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | SIAPKAN DATA REKAP
        |--------------------------------------------------------------------------
        |
        | Hanya data dengan status VALID
        | yang dimasukkan ke tabel rekap dan het.
        |
        |--------------------------------------------------------------------------
        */

        $rekapDataArray = [];

        $hetDataArray = [];


        foreach ($dataList as $data) {

            if (
                $data['status_verifikasi']
                === 'Valid'
            ) {

                $rekapDataArray[
                    $data['tanggal']
                ][
                    $data['nama_komoditas']
                ] =
                    $data['harga_hari_ini'];

                $hetDataArray[
                    $data['tanggal']
                ][
                    $data['nama_komoditas']
                ] =
                    $data['harga_het'];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN REKAP
        |--------------------------------------------------------------------------
        */

        foreach (
            $rekapDataArray
            as $tanggal => $rekapData
        ) {

            DB::table('rekap')
                ->updateOrInsert(
                    [
                        'tanggal' =>
                            $tanggal
                    ],
                    $rekapData
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN HET
        |--------------------------------------------------------------------------
        */

        foreach (
            $hetDataArray
            as $tanggal => $hetData
        ) {

            DB::table('het')
                ->updateOrInsert(
                    [
                        'tanggal' =>
                            $tanggal
                    ],
                    $hetData
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SELESAI
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'dashboard_verifikator.verify_data'
            )
            ->with([
                'success' =>
                    'Keputusan validasi telah disimpan di dalam sistem.',

                'verification_total' =>
                    $totalData,

                'verification_valid' =>
                    $validCount,

                'verification_rejected' =>
                    $rejectedCount,
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS VERIFIKASI
    |--------------------------------------------------------------------------
    */

    public function updateStatusVerifikasiVer(
        Request $request,
        $id
    ) {
        $request->validate([
            'status_verifikasi' =>
                'required|in:Valid,Tidak Valid,Tidak_valid,Belum Diverifikasi',

            'catatan_verifikasi' =>
                'nullable|string|max:1000',
        ]);


        $dataKomoditas =
            Data::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | TIDAK VALID WAJIB CATATAN
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $request->status_verifikasi,
                [
                    'Tidak_valid',
                    'Tidak Valid'
                ]
            )
        ) {

            if (
                trim(
                    $request->catatan_verifikasi
                    ?? ''
                ) === ''
            ) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Catatan wajib diisi untuk data yang ditolak/tidak valid.'
                    );
            }
        }


        $dataKomoditas->status_verifikasi =
            $request->status_verifikasi;

        $dataKomoditas->catatan_verifikasi =
            $request->filled(
                'catatan_verifikasi'
            )
                ? trim(
                    $request->catatan_verifikasi
                )
                : null;

        $dataKomoditas->save();


        return redirect()
            ->route(
                'dashboard_verifikator.verify_data'
            )
            ->with(
                'success',
                'Status berhasil diperbarui!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | KEMBALIKAN DATA
    |--------------------------------------------------------------------------
    */

    public function kembalikanDataVer(
        Request $request
    ) {
        foreach (
            $request->input('data', [])
            as $data
        ) {

            $dataKomoditas =
                Data::find(
                    $data['id']
                );

            if ($dataKomoditas) {

                $dataKomoditas->status_verifikasi =
                    $data['status_verifikasi'];

                if (
                    isset(
                        $data['catatan_verifikasi']
                    )
                ) {

                    $dataKomoditas->catatan_verifikasi =
                        trim(
                            $data['catatan_verifikasi']
                        );
                }

                $dataKomoditas->save();
            }
        }


        return redirect()
            ->route(
                'dashboard_verifikator.verify_data'
            )
            ->with(
                'success',
                'Data berhasil dikembalikan!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE PDF VERIFIKATOR
    |--------------------------------------------------------------------------
    */

    public function generatePDFveri(
        Request $request
    ) {
        try {

            $validated =
                $request->validate([
                    'commodity' =>
                        'required|string',

                    'start_date' =>
                        'required|date_format:Y-m-d',

                    'end_date' =>
                        'required|date_format:Y-m-d|after_or_equal:start_date',
                ]);


            $commodity =
                $validated['commodity'];

            $startDate =
                Carbon::parse(
                    $validated['start_date']
                );

            $endDate =
                Carbon::parse(
                    $validated['end_date']
                );


            /*
            |--------------------------------------------------------------------------
            | DATA SUMBER KOMODITAS
            |--------------------------------------------------------------------------
            */

            $sumberDataKomoditas =
                sumber_data::where(
                    'nama_komoditas',
                    $commodity
                )->first();


            $hargaHet =
                $sumberDataKomoditas->hethap
                ?? null;

            $hargaWaspada =
                $sumberDataKomoditas->harga_waspada
                ?? null;

            $hargaIntervensi =
                $sumberDataKomoditas->harga_intervensi
                ?? null;


            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA REKAP
            |--------------------------------------------------------------------------
            */

            $extendedStartDate =
                $startDate
                    ->copy()
                    ->subDays(7);


            $rawData =
                Rekap::select(
                    'tanggal',
                    $commodity
                )
                    ->whereBetween(
                        'tanggal',
                        [
                            $extendedStartDate,
                            $endDate
                        ]
                    )
                    ->orderBy(
                        'tanggal',
                        'asc'
                    )
                    ->get();


            $priceMap =
                $rawData->keyBy(
                    fn($item) =>
                        Carbon::parse(
                            $item->tanggal
                        )->toDateString()
                );


            $processedData =
                collect();


            /*
            |--------------------------------------------------------------------------
            | PROSES TANGGAL
            |--------------------------------------------------------------------------
            */

            $currentDate =
                $startDate->copy();


            while (
                $currentDate->lessThanOrEqualTo(
                    $endDate
                )
            ) {

                $todayDateString =
                    $currentDate->toDateString();


                if (
                    $priceMap->has(
                        $todayDateString
                    )
                ) {

                    $hargaHariIni =
                        $priceMap[
                            $todayDateString
                        ]->$commodity;


                    $hargaKemarin =
                        $priceMap->has(
                            $currentDate
                                ->copy()
                                ->subDay()
                                ->toDateString()
                        )
                            ? $priceMap[
                                $currentDate
                                    ->copy()
                                    ->subDay()
                                    ->toDateString()
                            ]->$commodity
                            : 0;


                    /*
                    |--------------------------------------------------------------------------
                    | RATA-RATA 7 HARI
                    |--------------------------------------------------------------------------
                    */

                    $sum7Days = 0;

                    $count7Days = 0;


                    for (
                        $i = 0;
                        $i < 7;
                        $i++
                    ) {

                        $pastDateString =
                            $currentDate
                                ->copy()
                                ->subDays($i)
                                ->toDateString();


                        if (
                            $priceMap->has(
                                $pastDateString
                            )
                        ) {

                            $sum7Days +=
                                $priceMap[
                                    $pastDateString
                                ]->$commodity;

                            $count7Days++;
                        }
                    }


                    $hargaRataRata =
                        $count7Days > 0
                            ? $sum7Days / $count7Days
                            : 0;


                    $processedData->push(
                        (object) [

                            'tanggal' =>
                                $todayDateString,

                            'nama_komoditas' =>
                                $commodity,

                            'harga_hari_ini' =>
                                $hargaHariIni,

                            'harga_kemarin' =>
                                $hargaKemarin,

                            'harga_rata_rata_7_hari' =>
                                $hargaRataRata,

                            'harga_het' =>
                                $hargaHet,

                            'harga_waspada' =>
                                $hargaWaspada,

                            'harga_intervensi' =>
                                $hargaIntervensi,
                        ]
                    );
                }


                $currentDate->addDay();
            }


            /*
            |--------------------------------------------------------------------------
            | DATA KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $processedData->isEmpty()
            ) {

                return back()
                    ->with(
                        'error',
                        'Data tidak ditemukan untuk rentang tanggal.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | GENERATE PDF
            |--------------------------------------------------------------------------
            */

            $pdf =
                PDF::loadView(
                    'pdf.report',
                    [
                        'data' =>
                            $processedData,

                        'startDate' =>
                            $startDate->format(
                                'd-m-Y'
                            ),

                        'endDate' =>
                            $endDate->format(
                                'd-m-Y'
                            ),

                        'commodity' =>
                            $commodity,

                        'hargaHet' =>
                            $hargaHet,

                        'hargaWaspada' =>
                            $hargaWaspada,

                        'hargaIntervensi' =>
                            $hargaIntervensi,
                    ]
                )
                    ->setPaper(
                        'A4',
                        'landscape'
                    );


            return $pdf->download(
                'commodity_report.pdf'
            );

        } catch (\Exception $e) {

            Log::error(
                'Generate PDF Verifikator Error',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );

            return back()
                ->with(
                    'error',
                    'Terjadi kesalahan: ' .
                    $e->getMessage()
                );
        }
    }
}