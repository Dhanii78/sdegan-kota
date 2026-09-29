<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\sumber_data;
use App\Models\rekap;
use App\Models\het;
use App\Models\Data;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Admin
     */
    public function containerbesar(Request $request)
    {
        // Ambil tanggal filter
        $tanggalFilter = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        // ==========================================
        // 1. CARI DATA REKAP
        // ==========================================

        // Cari data pada tanggal yang dipilih
        $latestRekap = rekap::whereDate('tanggal', $tanggalFilter)
            ->orderBy('tanggal', 'desc')
            ->first();

        // Jika tidak ada, ambil data kemarin
        if (!$latestRekap) {
            $tanggalKemarin = Carbon::parse($tanggalFilter)
                ->subDay()
                ->format('Y-m-d');

            $latestRekap = rekap::whereDate('tanggal', $tanggalKemarin)
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        // Jika masih tidak ada, ambil data rekap terakhir
        if (!$latestRekap) {
            $latestRekap = rekap::orderBy('tanggal', 'desc')->first();
        }

        // ==========================================
        // 2. TENTUKAN TANGGAL DATA
        // ==========================================

        $tanggal = $latestRekap
            ? Carbon::parse($latestRekap->tanggal)->format('Y-m-d')
            : null;

        // ==========================================
        // 3. DATA REKAP SEBELUMNYA
        // ==========================================

        $previousRekap = null;

        if ($tanggal) {
            $tanggalSebelumnya = Carbon::parse($tanggal)
                ->subDay()
                ->format('Y-m-d');

            $previousRekap = rekap::whereDate(
                'tanggal',
                $tanggalSebelumnya
            )->first();
        }

        // ==========================================
        // 4. AMBIL KOLOM KOMODITAS
        // ==========================================

        $allColumns = Schema::getColumnListing('rekap');

        $komoditasColumns = array_values(
            array_filter($allColumns, function ($col) {
                return !in_array($col, [
                    'id',
                    'tanggal',
                    'created_at',
                    'updated_at'
                ]);
            })
        );

        // ==========================================
        // 5. RATA-RATA 7 HARI
        // ==========================================

        $sevenDayAverage = [];

        foreach ($komoditasColumns as $column) {

            $sevenDayAverage[$column] = null;

            // Kalau belum ada tanggal, jangan lakukan query
            if (!$tanggal) {
                continue;
            }

            $prices = rekap::whereBetween('tanggal', [
                Carbon::parse($tanggal)
                    ->subDays(6)
                    ->format('Y-m-d'),

                $tanggal
            ])
                ->get([$column]);

            $filteredPrices = $prices->filter(function ($p) use ($column) {
                return isset($p->$column) && is_numeric($p->$column);
            });

            if ($filteredPrices->count() > 0) {
                $sevenDayAverage[$column] =
                    $filteredPrices->avg($column);
            }
        }

        // ==========================================
        // 6. PERUBAHAN HARGA
        // ==========================================

        $priceChanges = [];

        foreach ($komoditasColumns as $column) {

            $priceChanges[$column] = null;

            // Jika tidak ada data terbaru
            if (!$latestRekap) {
                continue;
            }

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
            }
        }

        // ==========================================
        // 7. DATA SUMBER KOMODITAS
        // ==========================================

        $sumber_data = sumber_data::all();

        // ==========================================
        // 8. INDIKATOR
        // ==========================================

        $indicators = $this->calculateIndicators(
            $latestRekap,
            $sumber_data
        );

        $averageIndicators = $this->calculateAverageIndicators(
            $sevenDayAverage,
            $sumber_data
        );

        // ==========================================
        // 9. DATA REKAP UNTUK VIEW
        // ==========================================

        $rekapData = collect();

        if ($tanggal) {
            $rekapData = rekap::whereDate(
                'tanggal',
                $tanggal
            )
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        // ==========================================
        // 10. DATA HET BERDASARKAN TANGGAL
        // ==========================================

        $hetData = $tanggal ? het::where('tanggal', $tanggal)->first() : null;

        // ==========================================
        // 11. KIRIM KE VIEW
        // ==========================================

        return view('dashboard_admin.index', [
            'sumber_data' => $sumber_data,
            'rekap' => $rekapData,
            'latestRekap' => $latestRekap,
            'previousRekap' => $previousRekap,
            'sevenDayAverage' => $sevenDayAverage,
            'indicators' => $indicators,
            'averageIndicators' => $averageIndicators,
            'priceChanges' => $priceChanges,
            'komoditasColumns' => $komoditasColumns,
            'tanggal' => $tanggal,
            'hetData' => $hetData,
        ]);
    }

    /**
     * Menghitung indikator berdasarkan harga terbaru
     */
    private function calculateIndicators($latestRekap, $sumber_data)
    {
        $indicators = [];

        foreach ($sumber_data as $data) {

            $namaKomoditas = $data->nama_komoditas;

            // ==========================================
            // FIX ERROR:
            // latestRekap bisa NULL
            // ==========================================

            if (!$latestRekap) {
                $indicators[$namaKomoditas] = 'N/A';
                continue;
            }

            // Pastikan kolom komoditas tersedia di tabel rekap
            if (!Schema::hasColumn('rekap', $namaKomoditas)) {
                Log::warning(
                    "Kolom komoditas tidak ditemukan di tabel rekap",
                    [
                        'komoditas' => $namaKomoditas
                    ]
                );

                $indicators[$namaKomoditas] = 'N/A';
                continue;
            }

            // Ambil harga hari ini
            $hargaH = $latestRekap->$namaKomoditas ?? null;

            // Kalau harga kosong
            if (!is_numeric($hargaH)) {
                $indicators[$namaKomoditas] = 'N/A';
                continue;
            }

            $hethap = is_numeric($data->hethap)
                ? (float) $data->hethap
                : 0;

            $hargaWaspada = is_numeric($data->harga_waspada)
                ? (float) $data->harga_waspada
                : 0;

            $hargaIntervensi = is_numeric($data->harga_intervensi)
                ? (float) $data->harga_intervensi
                : 0;

            $waspadaPersen = is_numeric($data->waspada)
                ? (float) $data->waspada
                : 0;

            $intervensiPersen = is_numeric($data->intervensi)
                ? (float) $data->intervensi
                : 0;

            Log::info(
                "Perhitungan indikator",
                [
                    'komoditas' => $namaKomoditas,
                    'harga_hari_ini' => $hargaH,
                    'hethap' => $hethap,
                    'harga_waspada' => $hargaWaspada,
                    'harga_intervensi' => $hargaIntervensi
                ]
            );

            // ==========================================
            // PERHITUNGAN INDIKATOR
            // ==========================================

            if ($hargaH < $hethap) {

                $waspadaLimit =
                    $hethap * (1 + $waspadaPersen / 100);

                $intervensiLimit =
                    $hethap * (1 + $intervensiPersen / 100);

                if ($hargaH > $intervensiLimit) {

                    $indicators[$namaKomoditas] = 'Intervensi';

                } elseif ($hargaH > $waspadaLimit) {

                    $indicators[$namaKomoditas] = 'Waspada';

                } else {

                    $indicators[$namaKomoditas] = 'Aman';
                }

            } else {

                if ($hargaH > $hargaIntervensi) {

                    $indicators[$namaKomoditas] = 'Intervensi';

                } elseif ($hargaH > $hargaWaspada) {

                    $indicators[$namaKomoditas] = 'Waspada';

                } else {

                    $indicators[$namaKomoditas] = 'Aman';
                }
            }
        }

        return $indicators;
    }

    /**
     * Menghitung indikator rata-rata 7 hari
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

            // Tidak ada data
            if ($averagePrice === null) {
                $averageIndicators[$namaKomoditas] = 'N/A';
                continue;
            }

            $hethap = is_numeric($data->hethap)
                ? (float) $data->hethap
                : 0;

            $hargaWaspada = is_numeric($data->harga_waspada)
                ? (float) $data->harga_waspada
                : 0;

            $hargaIntervensi = is_numeric($data->harga_intervensi)
                ? (float) $data->harga_intervensi
                : 0;

            $waspadaPersen = is_numeric($data->waspada)
                ? (float) $data->waspada
                : 0;

            $intervensiPersen = is_numeric($data->intervensi)
                ? (float) $data->intervensi
                : 0;

            if ($averagePrice < $hethap) {

                $waspadaLimit =
                    $hethap * (1 + $waspadaPersen / 100);

                $intervensiLimit =
                    $hethap * (1 + $intervensiPersen / 100);

                if ($averagePrice > $intervensiLimit) {

                    $averageIndicators[$namaKomoditas] =
                        'Intervensi';

                } elseif ($averagePrice > $waspadaLimit) {

                    $averageIndicators[$namaKomoditas] =
                        'Waspada';

                } else {

                    $averageIndicators[$namaKomoditas] =
                        'Aman';
                }

            } else {

                if ($averagePrice > $hargaIntervensi) {

                    $averageIndicators[$namaKomoditas] =
                        'Intervensi';

                } elseif ($averagePrice > $hargaWaspada) {

                    $averageIndicators[$namaKomoditas] =
                        'Waspada';

                } else {

                    $averageIndicators[$namaKomoditas] =
                        'Aman';
                }
            }
        }

        return $averageIndicators;
    }

    /**
     * Generate PDF laporan komoditas
     */
    public function generatePDF(Request $request)
    {
        Log::info(
            'Memulai proses generate PDF',
            ['input' => $request->all()]
        );

        try {

            // ==========================================
            // VALIDASI
            // ==========================================

            $validated = $request->validate([
                'commodity' => 'required|string',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' =>
                    'required|date_format:Y-m-d|after_or_equal:start_date',
            ]);

            $commodity = $validated['commodity'];

            $startDate =
                Carbon::parse($validated['start_date']);

            $endDate =
                Carbon::parse($validated['end_date']);

            // ==========================================
            // CEK KOLOM KOMODITAS
            // ==========================================

            if (!Schema::hasColumn('rekap', $commodity)) {

                return back()->with(
                    'error',
                    'Komoditas "' . $commodity .
                    '" tidak ditemukan pada tabel rekap.'
                );
            }

            // ==========================================
            // DATA SUMBER
            // ==========================================

            $sumberDataKomoditas =
                sumber_data::where(
                    'nama_komoditas',
                    $commodity
                )->first();

            if (!$sumberDataKomoditas) {

                return back()->with(
                    'error',
                    'Data sumber komoditas "' .
                    $commodity .
                    '" tidak ditemukan.'
                );
            }

            $hargaHet =
                $sumberDataKomoditas->hethap ?? null;

            $hargaWaspada =
                $sumberDataKomoditas->harga_waspada ?? null;

            $hargaIntervensi =
                $sumberDataKomoditas->harga_intervensi ?? null;

            // ==========================================
            // AMBIL DATA REKAP
            // ==========================================

            $extendedStartDate =
                $startDate->copy()->subDays(7);

            $rawData = Rekap::select(
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
                ->orderBy('tanggal', 'asc')
                ->get();

            // ==========================================
            // BUAT MAP TANGGAL
            // ==========================================

            $priceMap = $rawData->keyBy(function ($item) {

                return Carbon::parse(
                    $item->tanggal
                )->toDateString();

            });

            $processedData = collect();

            // ==========================================
            // PROSES SETIAP TANGGAL
            // ==========================================

            $currentDate =
                $startDate->copy();

            while (
                $currentDate->lessThanOrEqualTo($endDate)
            ) {

                $todayDateString =
                    $currentDate->toDateString();

                // Hanya proses jika ada data
                if ($priceMap->has($todayDateString)) {

                    $record =
                        $priceMap->get($todayDateString);

                    $hargaHariIni =
                        $record->$commodity ?? null;

                    // Kalau harga tidak valid
                    if (!is_numeric($hargaHariIni)) {

                        $currentDate->addDay();
                        continue;
                    }

                    // ==================================
                    // HARGA KEMARIN
                    // ==================================

                    $hargaKemarin = 0;

                    $yesterdayString =
                        $currentDate
                            ->copy()
                            ->subDay()
                            ->toDateString();

                    if ($priceMap->has($yesterdayString)) {

                        $yesterdayRecord =
                            $priceMap->get(
                                $yesterdayString
                            );

                        $hargaKemarin =
                            $yesterdayRecord->$commodity ?? 0;
                    }

                    // ==================================
                    // RATA-RATA 7 HARI
                    // ==================================

                    $sum7Days = 0;
                    $count7Days = 0;

                    for ($i = 0; $i < 7; $i++) {

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

                            $pastRecord =
                                $priceMap->get(
                                    $pastDateString
                                );

                            $pastPrice =
                                $pastRecord->$commodity ?? null;

                            if (is_numeric($pastPrice)) {

                                $sum7Days +=
                                    $pastPrice;

                                $count7Days++;
                            }
                        }
                    }

                    $hargaRataRata =
                        $count7Days > 0
                            ? $sum7Days / $count7Days
                            : 0;

                    // ==================================
                    // MASUKKAN DATA
                    // ==================================

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

            // ==========================================
            // CEK HASIL
            // ==========================================

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

            // ==========================================
            // GENERATE PDF
            // ==========================================

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
                        $hargaIntervensi,
                ]
            )->setPaper(
                'A4',
                'landscape'
            );

            Log::info(
                'PDF berhasil digenerate',
                [
                    'commodity' =>
                        $commodity,

                    'periode' =>
                        $startDate->toDateString() .
                        ' sampai ' .
                        $endDate->toDateString(),

                    'file_size' =>
                        strlen($pdf->output())
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

    /**
     * Data Pangan
     */
    public function dataPangan()
    {
        // Data manual dari database
        $dataManual = Data::all();

        // Data import dari session
        $dataImport =
            session('imported_data', []);

        // Ubah import menjadi Collection
        $dataImportCollection =
            collect($dataImport);

        // Gabungkan
        $dataGabungan =
            $dataManual->merge(
                $dataImportCollection
            );

        return view(
            'dashboard_admin.sub_menu.data_pangan',
            [
                'dataKomoditas' =>
                    $dataGabungan
            ]
        );
    }

    /**
     * Simpan Data
     */
    public function saveData(Request $request)
    {
        $dataList =
            $request->input('data', []);

        $action =
            $request->input('action');

        // ==========================================
        // CEK DATA
        // ==========================================

        if (empty($dataList)) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Tidak ada data yang dikirim.'
                );
        }

        // ==========================================
        // SIMPAN DATA
        // ==========================================

        foreach ($dataList as $data) {

            if (
                !isset($data['tanggal']) ||
                !isset($data['nama_komoditas'])
            ) {
                continue;
            }

            Data::where('tanggal', $data['tanggal'])
                ->where('nama_komoditas', $data['nama_komoditas'])
                ->update(
                [
                    'harga_het' =>
                        $data['harga_het'] ?? 0,

                    'harga_kemarin' =>
                        $data['harga_kemarin'] ?? 0,

                    'harga_hari_ini' =>
                        $data['harga_hari_ini'] ?? 0,

                    'status_verifikasi' =>
                        $data['status_verifikasi']
                        ?? 'Belum Diverifikasi',
                    'updated_at' => now(),
                ]
            );
        }

        // ==========================================
        // JIKA RETURN
        // ==========================================

        if ($action === 'return') {

            return redirect()
                ->route(
                    'dashboard_admin.sub_menu.data_pangan'
                )
                ->with(
                    'success',
                    'Data berhasil dikembalikan ke halaman Data Pangan!'
                );
        }

        // ==========================================
        // SIAPKAN DATA REKAP DAN HET
        // ==========================================

        $rekapDataArray = [];
        $hetDataArray = [];

        foreach ($dataList as $data) {

            if (
                !isset(
                    $data['status_verifikasi'],
                    $data['tanggal'],
                    $data['nama_komoditas']
                )
            ) {
                continue;
            }

            // Hanya data VALID
            if (
                $data['status_verifikasi']
                === 'Valid'
            ) {

                $tanggal =
                    $data['tanggal'];

                $namaKomoditas =
                    $data['nama_komoditas'];

                // ==============================
                // REKAP
                // ==============================

                if (
                    Schema::hasColumn(
                        'rekap',
                        $namaKomoditas
                    )
                ) {

                    $rekapDataArray[$tanggal]
                        [$namaKomoditas] =
                        $data['harga_hari_ini'];
                }

                // ==============================
                // HET
                // ==============================

                if (
                    Schema::hasColumn(
                        'het',
                        $namaKomoditas
                    )
                ) {

                    $hetDataArray[$tanggal]
                        [$namaKomoditas] =
                        $data['harga_het'];
                }
            }
        }

        // ==========================================
        // SIMPAN KE REKAP
        // ==========================================

        foreach (
            $rekapDataArray
            as $tanggal => $rekapData
        ) {

            if (empty($rekapData)) {
                continue;
            }

            DB::table('rekap')
                ->updateOrInsert(
                    [
                        'tanggal' =>
                            $tanggal
                    ],
                    $rekapData
                );
        }

        // ==========================================
        // SIMPAN KE HET
        // ==========================================

        foreach (
            $hetDataArray
            as $tanggal => $hetData
        ) {

            if (empty($hetData)) {
                continue;
            }

            DB::table('het')
                ->updateOrInsert(
                    [
                        'tanggal' =>
                            $tanggal
                    ],
                    $hetData
                );
        }

        // ==========================================
        // DATA VALID TIDAK DIHAPUS
        // ==========================================

        /*
        foreach ($dataList as $data) {

            if (
                $data['status_verifikasi']
                === 'Valid'
            ) {

                Data::where(
                    'tanggal',
                    $data['tanggal']
                )
                    ->where(
                        'nama_komoditas',
                        $data['nama_komoditas']
                    )
                    ->delete();
            }
        }
        */

        return redirect()
            ->route(
                'dashboard_admin.sub_menu.data_pangan'
            )
            ->with(
                'success',
                'Data berhasil disimpan!'
            );
    }

    /**
     * Update Status Verifikasi
     */
    public function updateStatusVerifikasi(
        Request $request,
        $id
    ) {

        $validated =
            $request->validate([
                'status_verifikasi' =>
                    'required|in:Valid,Tidak Valid,Belum Diverifikasi',
            ]);

        $dataKomoditas =
            Data::findOrFail($id);

        $dataKomoditas->status_verifikasi =
            $validated['status_verifikasi'];

        $dataKomoditas->save();

        return redirect()
            ->route(
                'dashboard_admin.sub_menu.verifikasi_data'
            )
            ->with(
                'success',
                'Status data berhasil diperbarui!'
            );
    }

    /**
     * Kembalikan Data
     */
    public function kembalikanData(
        Request $request
    ) {

        $dataList =
            $request->input('data', []);

        if (empty($dataList)) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Tidak ada data yang dikirim.'
                );
        }

        foreach ($dataList as $data) {

            if (!isset($data['id'])) {
                continue;
            }

            $dataKomoditas =
                Data::find($data['id']);

            if ($dataKomoditas) {

                $dataKomoditas->status_verifikasi =
                    $data['status_verifikasi']
                    ?? 'Revisi';

                $dataKomoditas->save();
            }
        }

        return redirect()
            ->route(
                'dashboard_admin.sub_menu.data_pangan'
            )
            ->with(
                'success',
                'Data berhasil dikembalikan ke halaman Data Pangan!'
            );
    }
}