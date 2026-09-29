<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\sumber_data;
use App\Models\rekap;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Dashboard utama berdasarkan role pengguna.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'operator') {
            return view('operator.dashboard');
        }

        if ($user->role === 'verifikator') {
            return view('verifikator.dashboard');
        }

        abort(403, 'Role pengguna tidak dikenali.');
    }

    /**
     * Mengambil daftar komoditas.
     */
    public function getKomoditas()
    {
        $komoditas = sumber_data::select('nama_komoditas')
            ->get();

        return response()->json($komoditas);
    }

    /**
     * Dashboard/data umum.
     */
    public function containerbesar(Request $request)
    {
        // Ambil tanggal filter
        $tanggalFilter = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        // Cari data pada tanggal yang dipilih
        $latestRekap = rekap::whereDate('tanggal', $tanggalFilter)
            ->orderBy('tanggal', 'desc')
            ->first();

        // Jika tidak ada, ambil tanggal sebelumnya
        if (!$latestRekap) {
            $tanggalKemarin = Carbon::parse($tanggalFilter)
                ->subDay()
                ->format('Y-m-d');

            $latestRekap = rekap::whereDate('tanggal', $tanggalKemarin)
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        // Jika tetap tidak ada, ambil data terakhir
        if (!$latestRekap) {
            $latestRekap = rekap::orderBy('tanggal', 'desc')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Jika database rekap masih kosong
        |--------------------------------------------------------------------------
        */
        if (!$latestRekap) {
            $sumber_data = sumber_data::all();

            return view('dashboard_umum', [
                'sumber_data' => $sumber_data,
                'rekap' => collect(),
                'latestRekap' => null,
                'previousRekap' => null,
                'sevenDayAverage' => [],
                'indicators' => [],
                'averageIndicators' => [],
                'priceChanges' => [],
                'komoditasColumns' => [],
                'tanggal' => null,
            ]);
        }

        // Tanggal data yang ditemukan
        $tanggal = Carbon::parse($latestRekap->tanggal)->format('Y-m-d');

        // Data tanggal sebelumnya
        $previousRekap = rekap::whereDate(
            'tanggal',
            Carbon::parse($tanggal)->subDay()->format('Y-m-d')
        )->first();

        // Ambil seluruh kolom tabel rekap
        $allColumns = Schema::getColumnListing('rekap');

        // Buang kolom id dan tanggal
        $komoditasColumns = array_values(
            array_filter($allColumns, function ($col) {
                return !in_array($col, ['id', 'tanggal']);
            })
        );

        /*
        |--------------------------------------------------------------------------
        | Rata-rata 7 hari
        |--------------------------------------------------------------------------
        */
        $sevenDayAverage = [];

        foreach ($komoditasColumns as $column) {

            $prices = rekap::whereBetween('tanggal', [
                Carbon::parse($tanggal)
                    ->subDays(6)
                    ->format('Y-m-d'),

                $tanggal
            ])
                ->orderBy('tanggal', 'desc')
                ->get([$column]);

            $filteredPrices = $prices->filter(function ($p) use ($column) {
                return is_numeric($p->$column);
            });

            $sevenDayAverage[$column] = $filteredPrices->count() > 0
                ? $filteredPrices->avg($column)
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Perubahan harga
        |--------------------------------------------------------------------------
        */
        $priceChanges = [];

        foreach ($komoditasColumns as $column) {

            $latest = $latestRekap->$column ?? null;
            $prev = $previousRekap
                ? ($previousRekap->$column ?? null)
                : null;

            if (is_numeric($latest) && is_numeric($prev)) {

                if ($latest > $prev) {
                    $priceChanges[$column] = 'up';
                } elseif ($latest < $prev) {
                    $priceChanges[$column] = 'down';
                } else {
                    $priceChanges[$column] = 'same';
                }

            } else {
                $priceChanges[$column] = null;
            }
        }

        $sumber_data = sumber_data::all();

        $indicators = $this->calculateIndicators(
            $latestRekap,
            $sumber_data
        );

        $averageIndicators = $this->calculateAverageIndicators(
            $sevenDayAverage,
            $sumber_data
        );

        return view('dashboard_umum', [
            'sumber_data' => $sumber_data,

            'rekap' => rekap::whereDate('tanggal', $tanggal)
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

    /**
     * Menghitung indikator harga.
     */
    private function calculateIndicators($latestRekap, $sumber_data)
    {
        $indicators = [];

        if (!$latestRekap) {
            return $indicators;
        }

        foreach ($sumber_data as $data) {

            $namaKomoditas = $data->nama_komoditas;

            $hargaH = $latestRekap->{$namaKomoditas} ?? null;

            if (!is_numeric($hargaH)) {
                $indicators[$namaKomoditas] = 'N/A';
                continue;
            }

            Log::info(
                "Nama Komoditas: {$namaKomoditas}, " .
                "Harga H: {$hargaH}, " .
                "HET/HAP: {$data->hethap}, " .
                "Harga Waspada: {$data->harga_waspada}, " .
                "Harga Intervensi: {$data->harga_intervensi}"
            );

            if ($hargaH < $data->hethap) {

                $waspadaLimit =
                    $data->hethap * (1 + ($data->waspada / 100));

                $intervensiLimit =
                    $data->hethap * (1 + ($data->intervensi / 100));

                if ($hargaH > $intervensiLimit) {
                    $indicators[$namaKomoditas] = 'Intervensi';
                } elseif ($hargaH > $waspadaLimit) {
                    $indicators[$namaKomoditas] = 'Waspada';
                } else {
                    $indicators[$namaKomoditas] = 'Aman';
                }

            } else {

                if ($hargaH > $data->harga_intervensi) {
                    $indicators[$namaKomoditas] = 'Intervensi';
                } elseif ($hargaH > $data->harga_waspada) {
                    $indicators[$namaKomoditas] = 'Waspada';
                } else {
                    $indicators[$namaKomoditas] = 'Aman';
                }
            }
        }

        return $indicators;
    }

    /**
     * Menghitung indikator rata-rata 7 hari.
     */
    private function calculateAverageIndicators(
        $sevenDayAverage,
        $sumber_data
    ) {
        $averageIndicators = [];

        foreach ($sumber_data as $data) {

            $namaKomoditas = $data->nama_komoditas;

            $averagePrice =
                $sevenDayAverage[$namaKomoditas] ?? null;

            if ($averagePrice === null || !is_numeric($averagePrice)) {
                $averageIndicators[$namaKomoditas] = 'N/A';
                continue;
            }

            Log::info(
                "Nama Komoditas: {$namaKomoditas}, " .
                "Harga Rata-rata 7 Hari: {$averagePrice}, " .
                "HET/HAP: {$data->hethap}"
            );

            if ($averagePrice < $data->hethap) {

                $waspadaLimit =
                    $data->hethap * (1 + ($data->waspada / 100));

                $intervensiLimit =
                    $data->hethap * (1 + ($data->intervensi / 100));

                if ($averagePrice > $intervensiLimit) {
                    $averageIndicators[$namaKomoditas] = 'Intervensi';
                } elseif ($averagePrice > $waspadaLimit) {
                    $averageIndicators[$namaKomoditas] = 'Waspada';
                } else {
                    $averageIndicators[$namaKomoditas] = 'Aman';
                }

            } else {

                if ($averagePrice > $data->harga_intervensi) {
                    $averageIndicators[$namaKomoditas] = 'Intervensi';
                } elseif ($averagePrice > $data->harga_waspada) {
                    $averageIndicators[$namaKomoditas] = 'Waspada';
                } else {
                    $averageIndicators[$namaKomoditas] = 'Aman';
                }
            }
        }

        return $averageIndicators;
    }

    /**
     * Filter data berdasarkan komoditas dan tanggal.
     */
    public function filter(Request $request)
    {
        $commodity = $request->input('commodity');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$commodity || !$startDate || !$endDate) {
            return response()->json([
                'error' => 'Komoditas, tanggal mulai dan tanggal akhir harus diisi.'
            ], 400);
        }

        if (!Schema::hasColumn('rekap', $commodity)) {
            return response()->json([
                'error' => 'Komoditas tidak ditemukan.'
            ], 400);
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($endDate->lt($startDate)) {
            return response()->json([
                'error' => 'Tanggal akhir tidak boleh lebih kecil dari tanggal mulai.'
            ], 400);
        }

        $records = rekap::whereBetween(
            'tanggal',
            [$startDate, $endDate]
        )->get([
            $commodity,
            'tanggal'
        ]);

        if ($records->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $validPrices = $records->filter(function ($record) use ($commodity) {
            return is_numeric($record->{$commodity});
        });

        if ($validPrices->isEmpty()) {
            return response()->json([
                'message' => 'Data harga tidak valid.'
            ], 404);
        }

        $averagePrice = ceil(
            $validPrices->avg($commodity)
        );

        $latestRecord = $validPrices
            ->sortByDesc('tanggal')
            ->first();

        $highestRecord = $validPrices
            ->sortByDesc($commodity)
            ->first();

        $lowestRecord = $validPrices
            ->sortBy($commodity)
            ->first();

        $totalRecords = $validPrices->count();

        $sumberData = sumber_data::where(
            'nama_komoditas',
            $commodity
        )->first();

        if (!$sumberData) {
            return response()->json([
                'error' => 'Data sumber_data tidak ditemukan.'
            ], 404);
        }

        $hargaIntervensi = $sumberData->harga_intervensi;
        $hargaWaspada = $sumberData->harga_waspada;

        $statusAverage = $averagePrice > $hargaIntervensi
            ? "<span style='color:red;'>Intervensi</span>"
            : (
                $averagePrice > $hargaWaspada
                    ? "<span style='color:orange;'>Waspada</span>"
                    : "<span style='color:green;'>Aman</span>"
            );

        $statusLatest = $latestRecord->{$commodity} > $hargaIntervensi
            ? "<span style='color:red;'>Intervensi</span>"
            : (
                $latestRecord->{$commodity} > $hargaWaspada
                    ? "<span style='color:orange;'>Waspada</span>"
                    : "<span style='color:green;'>Aman</span>"
            );

        // Perhitungan CV
        $hargaArray = $validPrices
            ->pluck($commodity)
            ->map(fn($value) => (float) $value)
            ->toArray();

        $mean = $averagePrice;
        $jumlah = count($hargaArray);

        if ($jumlah > 0 && $mean > 0) {

            $varians = array_reduce(
                $hargaArray,
                function ($carry, $value) use ($mean) {
                    return $carry + pow($value - $mean, 2);
                },
                0
            ) / $jumlah;

            $standarDeviasi = sqrt($varians);

            $cvAktual = round(
                ($standarDeviasi / $mean) * 100,
                2
            );

        } else {
            $cvAktual = 0;
        }

        $batasCV = $sumberData->cv ?? 0;

        $statusCV = $cvAktual > $batasCV
            ? "<span style='color:red;'>Fluktuatif</span>"
            : "<span style='color:green;'>Stabil</span>";

        $latestDate = Carbon::parse(
            $latestRecord->tanggal
        )->format('Y-m-d');

        $highestDate = Carbon::parse(
            $highestRecord->tanggal
        )->format('Y-m-d');

        $lowestDate = Carbon::parse(
            $lowestRecord->tanggal
        )->format('Y-m-d');

        return response()->json([
            'averagePrice' => number_format(
                $averagePrice,
                0,
                ',',
                '.'
            ),

            'latestPrice' => number_format(
                $latestRecord->{$commodity},
                0,
                ',',
                '.'
            ),

            'statusAverage' => $statusAverage,
            'latestDate' => $latestDate,
            'statusLatest' => $statusLatest,

            'highestPrice' => number_format(
                $highestRecord->{$commodity},
                0,
                ',',
                '.'
            ),

            'highestDate' => $highestDate,

            'lowestPrice' => number_format(
                $lowestRecord->{$commodity},
                0,
                ',',
                '.'
            ),

            'lowestDate' => $lowestDate,
            'dataCount' => $totalRecords,
            'statusCV' => $statusCV,
        ]);
    }

    /**
     * Mengambil seluruh data komoditas.
     */
    public function AllData(Request $request)
    {
        $commodity = $request->input('commodity');

        if (!$commodity) {
            return response()->json([
                'error' => 'Komoditas harus dipilih.'
            ], 400);
        }

        if (!Schema::hasColumn('rekap', $commodity)) {
            return response()->json([
                'error' => 'Komoditas tidak ditemukan.'
            ], 400);
        }

        $totalRecordsAll = rekap::select(
            $commodity,
            'tanggal'
        )->get();

        if ($totalRecordsAll->isEmpty()) {
            return response()->json([
                'totalAveragePrice' => null,
                'totalHighestPrice' => null,
                'totalHighestDate' => null,
                'totalLowestPrice' => null,
                'totalLowestDate' => null,
                'totalDataCount' => 0,
                'statusAverage' => null,
                'latestPrice' => null,
                'latestDate' => null,
                'statusLatest' => null,
                'statusCV' => null
            ]);
        }

        $validRecords = $totalRecordsAll->filter(function ($record) use ($commodity) {
            return is_numeric($record->{$commodity});
        });

        if ($validRecords->isEmpty()) {
            return response()->json([
                'totalAveragePrice' => null,
                'totalHighestPrice' => null,
                'totalHighestDate' => null,
                'totalLowestPrice' => null,
                'totalLowestDate' => null,
                'totalDataCount' => 0,
                'statusAverage' => null,
                'latestPrice' => null,
                'latestDate' => null,
                'statusLatest' => null,
                'statusCV' => null
            ]);
        }

        $totalAveragePrice = $validRecords->avg($commodity);

        $totalHighestRecord = $validRecords
            ->sortByDesc($commodity)
            ->first();

        $totalLowestRecord = $validRecords
            ->sortBy($commodity)
            ->first();

        $totalDataCount = $validRecords->count();

        $latestRecord = $validRecords
            ->sortByDesc('tanggal')
            ->first();

        $sumberData = sumber_data::where(
            'nama_komoditas',
            $commodity
        )->first();

        if (!$sumberData) {
            return response()->json([
                'error' => 'Data sumber_data tidak ditemukan.'
            ], 404);
        }

        // CV
        $hargaArray = $validRecords
            ->pluck($commodity)
            ->map(fn($value) => (float) $value)
            ->toArray();

        $mean = $totalAveragePrice;
        $jumlah = count($hargaArray);

        if ($jumlah > 0 && $mean > 0) {

            $varians = array_reduce(
                $hargaArray,
                function ($carry, $value) use ($mean) {
                    return $carry + pow($value - $mean, 2);
                },
                0
            ) / $jumlah;

            $standarDeviasi = sqrt($varians);

            $cvAktual = round(
                ($standarDeviasi / $mean) * 100,
                2
            );

        } else {
            $cvAktual = 0;
        }

        $batasCV = $sumberData->cv ?? 0;

        $statusCV = $cvAktual > $batasCV
            ? "<span style='color:red;'>Fluktuatif</span>"
            : "<span style='color:green;'>Stabil</span>";

        $hargaIntervensi = $sumberData->harga_intervensi;
        $hargaWaspada = $sumberData->harga_waspada;

        $statusAverage = $totalAveragePrice > $hargaIntervensi
            ? "<span style='color:red;'>Intervensi</span>"
            : (
                $totalAveragePrice > $hargaWaspada
                    ? "<span style='color:orange;'>Waspada</span>"
                    : "<span style='color:green;'>Aman</span>"
            );

        $statusLatest = $latestRecord->{$commodity} > $hargaIntervensi
            ? "<span style='color:red;'>Intervensi</span>"
            : (
                $latestRecord->{$commodity} > $hargaWaspada
                    ? "<span style='color:orange;'>Waspada</span>"
                    : "<span style='color:green;'>Aman</span>"
            );

        $latestDate = Carbon::parse(
            $latestRecord->tanggal
        )->format('Y-m-d');

        $totalHighestDate = $totalHighestRecord
            ? Carbon::parse($totalHighestRecord->tanggal)->format('Y-m-d')
            : null;

        $totalLowestDate = $totalLowestRecord
            ? Carbon::parse($totalLowestRecord->tanggal)->format('Y-m-d')
            : null;

        return response()->json([
            'totalAveragePrice' => number_format(
                $totalAveragePrice,
                0,
                ',',
                '.'
            ),

            'totalHighestPrice' => $totalHighestRecord
                ? number_format(
                    $totalHighestRecord->{$commodity},
                    0,
                    ',',
                    '.'
                )
                : null,

            'totalHighestDate' => $totalHighestDate,

            'totalLowestPrice' => $totalLowestRecord
                ? number_format(
                    $totalLowestRecord->{$commodity},
                    0,
                    ',',
                    '.'
                )
                : null,

            'totalLowestDate' => $totalLowestDate,
            'totalDataCount' => $totalDataCount,

            'statusAverage' => $statusAverage,

            'latestPrice' => number_format(
                $latestRecord->{$commodity},
                0,
                ',',
                '.'
            ),

            'latestDate' => $latestDate,
            'statusLatest' => $statusLatest,
            'statusCV' => $statusCV
        ]);
    }

    /**
     * Data grafik berdasarkan periode.
     */
    public function grafik(Request $request)
    {
        $request->validate([
            'commodity' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $commodity = $request->input('commodity');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!Schema::hasColumn('rekap', $commodity)) {
            return response()->json([
                'error' => 'Komoditas tidak ditemukan di tabel rekap.'
            ], 400);
        }

        $rekapData = DB::table('rekap')
            ->select(
                'tanggal as date',
                DB::raw("`{$commodity}` as price")
            )
            ->whereBetween('tanggal', [
                $startDate,
                $endDate
            ])
            ->orderBy('tanggal')
            ->get();

        $hetData = DB::table('het')
            ->select(
                'tanggal as date',
                DB::raw("`{$commodity}` as hethap")
            )
            ->whereBetween('tanggal', [
                $startDate,
                $endDate
            ])
            ->orderBy('tanggal')
            ->get();

        $hargaIntervensi = DB::table('sumber_data')
            ->where('nama_komoditas', $commodity)
            ->value('harga_intervensi');

        $mergedData = $rekapData->map(
            function ($item) use (
                $hetData,
                $hargaIntervensi
            ) {
                $hetItem = $hetData->firstWhere(
                    'date',
                    $item->date
                );

                $item->hethap = $hetItem
                    ? $hetItem->hethap
                    : null;

                $item->harga_intervensi =
                    $hargaIntervensi;

                return $item;
            }
        );

        return response()->json($mergedData);
    }

    /**
     * Grafik seluruh komoditas.
     */
    public function grafikAll(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $allColumns = Schema::getColumnListing('rekap');

        $commodities = array_values(
            array_filter($allColumns, function ($col) {
                return !in_array($col, ['id', 'tanggal']);
            })
        );

        $result = [];

        foreach ($commodities as $commodity) {

            $rekapData = DB::table('rekap')
                ->select(
                    'tanggal as date',
                    DB::raw("`{$commodity}` as price")
                )
                ->whereBetween('tanggal', [
                    $startDate,
                    $endDate
                ])
                ->orderBy('tanggal')
                ->get();

            $result[$commodity] = $rekapData;
        }

        return response()->json($result);
    }

    /**
     * Generate PDF laporan komoditas.
     */
    public function generatePDFumum(Request $request)
    {
        Log::info(
            'Memulai proses generate PDF',
            [
                'input' => $request->all()
            ]
        );

        try {

            $validated = $request->validate([
                'commodity' => 'required|string',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ]);

            $commodity = $validated['commodity'];

            $startDate = Carbon::parse(
                $validated['start_date']
            );

            $endDate = Carbon::parse(
                $validated['end_date']
            );

            if (!Schema::hasColumn('rekap', $commodity)) {
                return back()->with(
                    'error',
                    'Komoditas tidak ditemukan.'
                );
            }

            $sumberDataKomoditas = sumber_data::where(
                'nama_komoditas',
                $commodity
            )->first();

            if (!$sumberDataKomoditas) {
                return back()->with(
                    'error',
                    'Data sumber komoditas tidak ditemukan.'
                );
            }

            $hargaHet =
                $sumberDataKomoditas->hethap ?? null;

            $hargaWaspada =
                $sumberDataKomoditas->harga_waspada ?? null;

            $hargaIntervensi =
                $sumberDataKomoditas->harga_intervensi ?? null;

            // Ambil data 7 hari sebelum tanggal mulai
            $extendedStartDate =
                $startDate->copy()->subDays(7);

            $rawData = rekap::select(
                'tanggal',
                $commodity
            )
                ->whereBetween('tanggal', [
                    $extendedStartDate,
                    $endDate
                ])
                ->orderBy('tanggal', 'asc')
                ->get();

            $priceMap = $rawData->keyBy(
                function ($item) {
                    return Carbon::parse(
                        $item->tanggal
                    )->toDateString();
                }
            );

            $processedData = collect();

            $currentDate = $startDate->copy();

            while (
                $currentDate->lessThanOrEqualTo($endDate)
            ) {

                $todayDateString =
                    $currentDate->toDateString();

                if ($priceMap->has($todayDateString)) {

                    $hargaHariIni =
                        $priceMap[$todayDateString]->$commodity;

                    $hargaKemarin = 0;

                    $yesterdayString =
                        $currentDate
                            ->copy()
                            ->subDay()
                            ->toDateString();

                    if ($priceMap->has($yesterdayString)) {
                        $hargaKemarin =
                            $priceMap[$yesterdayString]->$commodity;
                    }

                    $sum7Days = 0;
                    $count7Days = 0;

                    for ($i = 0; $i < 7; $i++) {

                        $pastDateString =
                            $currentDate
                                ->copy()
                                ->subDays($i)
                                ->toDateString();

                        if ($priceMap->has($pastDateString)) {

                            $harga =
                                $priceMap[$pastDateString]->$commodity;

                            if (is_numeric($harga)) {
                                $sum7Days += $harga;
                                $count7Days++;
                            }
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
                                $hargaIntervensi
                        ]
                    );
                }

                $currentDate->addDay();
            }

            if ($processedData->isEmpty()) {

                Log::warning(
                    'Data tidak ditemukan',
                    [
                        'start_date' =>
                            $startDate->toDateString(),

                        'end_date' =>
                            $endDate->toDateString(),

                        'commodity' =>
                            $commodity
                    ]
                );

                return back()->with(
                    'error',
                    'Data tidak ditemukan untuk rentang tanggal yang dipilih.'
                );
            }

            $pdf = PDF::loadView(
                'pdf.report',
                [
                    'data' =>
                        $processedData,

                    'startDate' =>
                        $startDate->format('d-m-Y'),

                    'endDate' =>
                        $endDate->format('d-m-Y'),

                    'commodity' =>
                        $commodity,

                    'hargaHet' =>
                        $hargaHet,

                    'hargaWaspada' =>
                        $hargaWaspada,

                    'hargaIntervensi' =>
                        $hargaIntervensi
                ]
            )->setPaper(
                'A4',
                'landscape'
            );

            Log::info(
                'PDF berhasil digenerate',
                [
                    'commodity' => $commodity,
                    'periode' =>
                        $startDate->toDateString() .
                        ' sampai ' .
                        $endDate->toDateString()
                ]
            );

            return $pdf->download(
                'commodity_report.pdf'
            );

        } catch (\Exception $e) {

            Log::error(
                'Gagal generate PDF',
                [
                    'error' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString()
                ]
            );

            return back()->with(
                'error',
                'Terjadi kesalahan saat membuat laporan: ' .
                $e->getMessage()
            );
        }
    }
}