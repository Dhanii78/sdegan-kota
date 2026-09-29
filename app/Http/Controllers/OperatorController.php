<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\sumber_data;
use App\Models\rekap;
use App\Models\het;
use App\Models\Data;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;


class OperatorController extends Controller
{
    public function containerbesar(Request $request) {
        // Ambil tanggal filter (default hari ini)
        $tanggalFilter = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        // Cek data tanggal filter
        $latestRekap = rekap::whereDate('tanggal', $tanggalFilter)
            ->orderBy('tanggal', 'desc')
            ->first();

        // Jika tidak ada data di tanggal filter, ambil data kemarin
        if (!$latestRekap) {
            $tanggalKemarin = Carbon::parse($tanggalFilter)->subDay()->format('Y-m-d');
            $latestRekap = rekap::whereDate('tanggal', $tanggalKemarin)
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        // Jika tetap tidak ada, ambil data terakhir yang tersedia
        if (!$latestRekap) {
            $latestRekap = rekap::orderBy('tanggal', 'desc')->first();
        }

        // Set tanggal dari data yang berhasil diambil (bisa dari filter, kemarin, atau terakhir)
        $tanggal = $latestRekap ? $latestRekap->tanggal : null;

        // Data tanggal sebelumnya (untuk perubahan harga)
        $previousRekap = rekap::whereDate('tanggal', Carbon::parse($tanggal)->subDay())
            ->first();

        // Kolom komoditas
        $allColumns = Schema::getColumnListing('rekap');
        $komoditasColumns = array_filter($allColumns, function ($col) {
            return !in_array($col, ['id', 'tanggal']);
        });

        // 7 hari terakhir
        $sevenDayAverage = [];
        foreach ($komoditasColumns as $column) {
            $prices = rekap::whereBetween('tanggal', [
                    Carbon::parse($tanggal)->subDays(6)->format('Y-m-d'),
                    $tanggal
                ])
                ->orderBy('tanggal', 'desc')
                ->get([$column]);

            $filteredPrices = $prices->filter(fn($p) => is_numeric($p->$column));
            $sevenDayAverage[$column] = $filteredPrices->count() > 0
                ? $filteredPrices->avg($column)
                : null;
        }

        // Perubahan harga
        $priceChanges = [];
        foreach ($komoditasColumns as $column) {
            $latest = $latestRekap->$column ?? null;
            $prev = $previousRekap->$column ?? null;

            if (is_numeric($latest) && is_numeric($prev)) {
                $priceChanges[$column] =
                    $latest > $prev ? 'up' :
                    ($latest < $prev ? 'down' : 'same');
            } else {
                $priceChanges[$column] = null;
            }
        }

        $sumber_data = sumber_data::all();
        $indicators = $this->calculateIndicators($latestRekap, $sumber_data);
        $averageIndicators = $this->calculateAverageIndicators($sevenDayAverage, $sumber_data);

        // Ambil data HET dari tabel het berdasarkan tanggal
        $hetData = het::where('tanggal', $tanggal)->first();

        return view('dashboard_operator.index', [
            'sumber_data' => $sumber_data,
            'rekap' => rekap::whereDate('tanggal', $tanggal)->orderBy('tanggal', 'desc')->get(),
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

    private function calculateIndicators($latestRekap, $sumber_data)
    {
        $indicators = [];

        if (!$latestRekap) return $indicators;

        foreach ($sumber_data as $data) {
            $nama = $data->nama_komoditas;
            $harga = $latestRekap->{$nama} ?? null;

            if (!is_numeric($harga)) {
                $indicators[$nama] = null;
                continue;
            }

            if (is_numeric($data->hethap) && $harga < $data->hethap) {

                $waspada = $data->hethap * (1 + ($data->waspada ?? 0)/100);
                $intervensi = $data->hethap * (1 + ($data->intervensi ?? 0)/100);

                $indicators[$nama] =
                    $harga > $intervensi ? "Intervensi" :
                    ($harga > $waspada ? "Waspada" : "Aman");

            } else {
                $indicators[$nama] =
                    ($harga > ($data->harga_intervensi ?? 0)) ? "Intervensi" :
                    ($harga > ($data->harga_waspada ?? 0) ? "Waspada" : "Aman");
            }
        }

        return $indicators;
    }

    private function calculateAverageIndicators($sevenDayAverage, $sumber_data) {
        $averageIndicators = [];
    
        foreach ($sumber_data as $data) {
            $namaKomoditas = $data->nama_komoditas;
            $averagePrice = $sevenDayAverage[$namaKomoditas] ?? null;
    
            Log::info("Nama Komoditas: $namaKomoditas, Harga Rata-rata 7 Hari: $averagePrice, HET/HAP: {$data->hethap}, Harga Waspada: {$data->harga_waspada}, Harga Intervensi: {$data->harga_intervensi}");
    
            if ($averagePrice !== null) {
                if ($averagePrice < $data->hethap) {
                    $waspadaLimit = $data->hethap * (1 + $data->waspada / 100);
                    $intervensiLimit = $data->hethap * (1 + $data->intervensi / 100);
    
                    Log::info("Waspada Limit: $waspadaLimit, Intervensi Limit: $intervensiLimit");
    
                    if ($averagePrice > $intervensiLimit) {
                        $averageIndicators[$namaKomoditas] = "Intervensi";
                    } elseif ($averagePrice > $waspadaLimit) {
                        $averageIndicators[$namaKomoditas] = "Waspada";
                    } else {
                        $averageIndicators[$namaKomoditas] = "Aman";
                    }
                } else {
                    if ($averagePrice > $data->harga_intervensi) {
                        $averageIndicators[$namaKomoditas] = "Intervensi";
                    } elseif ($averagePrice > $data->harga_waspada) {
                        $averageIndicators[$namaKomoditas] = "Waspada";
                    } else {
                        $averageIndicators[$namaKomoditas] = "Aman";
                    }
                }
            } else {
                $averageIndicators[$namaKomoditas] = 'N/A';
            }
        }
    
        return $averageIndicators;
    }     
    
    public function dataPangan(){
        // Ambil data dari database (manual input)
        $dataManual = Data::all();

        // Ambil data dari session (import data)
        $dataImport = session('imported_data', []);

        // Jika $dataManual adalah Collection dan $dataImport berupa array, kamu bisa merge:
        $dataImportCollection = collect($dataImport);
        $dataGabungan = $dataManual->merge($dataImportCollection);

        return view('dashboard_operator.data_pangan', ['dataKomoditas' => $dataGabungan]);
    }
    
    public function checkTanggal(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $exists = Data::whereDate('tanggal', $tanggal)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function generatePDFop(Request $request){
        Log::info('Memulai proses generate PDF', ['input' => $request->all()]);

        try {
            $validated = $request->validate([
                'commodity' => 'required|string',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ]);

            $commodity = $validated['commodity'];
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

            Log::debug('Parameter valid', [
                'commodity' => $commodity,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

            // Ambil data HET, Waspada, dan Intervensi dari sumber_data
            $sumberDataKomoditas = sumber_data::where('nama_komoditas', $commodity)->first();
            $hargaHet = $sumberDataKomoditas->hethap ?? null;
            $hargaWaspada = $sumberDataKomoditas->harga_waspada ?? null;
            $hargaIntervensi = $sumberDataKomoditas->harga_intervensi ?? null;

            Log::debug('Data sumber diperoleh', [
                'harga_het' => $hargaHet,
                'harga_waspada' => $hargaWaspada,
                'harga_intervensi' => $hargaIntervensi
            ]);

            // Ambil data dalam rentang yang lebih luas untuk perhitungan rata-rata 7 hari
            $extendedStartDate = $startDate->copy()->subDays(7); 

            $rawData = Rekap::select('tanggal', $commodity)
                ->whereBetween('tanggal', [$extendedStartDate, $endDate])
                ->orderBy('tanggal', 'asc')
                ->get();
            
            $priceMap = $rawData->keyBy(function ($item) {
                return Carbon::parse($item->tanggal)->toDateString();
            });

            $processedData = collect(); 
            
            $currentDate = $startDate->copy();
            while ($currentDate->lessThanOrEqualTo($endDate)) {
                $todayDateString = $currentDate->toDateString();
                
                if ($priceMap->has($todayDateString)) {
                    $hargaHariIni = $priceMap[$todayDateString]->$commodity;

                    $hargaKemarin = 0;
                    $yesterdayString = $currentDate->copy()->subDay()->toDateString();
                    if ($priceMap->has($yesterdayString)) {
                        $hargaKemarin = $priceMap[$yesterdayString]->$commodity;
                    }

                    $sum7Days = 0;
                    $count7Days = 0;
                    for ($i = 0; $i < 7; $i++) {
                        $pastDateString = $currentDate->copy()->subDays($i)->toDateString();
                        if ($priceMap->has($pastDateString)) {
                            $sum7Days += $priceMap[$pastDateString]->$commodity;
                            $count7Days++;
                        }
                    }
                    $hargaRataRata = ($count7Days > 0) ? $sum7Days / $count7Days : 0;

                    $processedData->push((object) [
                        'tanggal' => $todayDateString,
                        'nama_komoditas' => $commodity,
                        'harga_hari_ini' => $hargaHariIni,
                        'harga_kemarin' => $hargaKemarin,
                        'harga_rata_rata_7_hari' => $hargaRataRata,
                        'harga_het' => $hargaHet,
                        'harga_waspada' => $hargaWaspada,
                        'harga_intervensi' => $hargaIntervensi
                    ]);
                }
                
                $currentDate->addDay();
            }

            Log::debug('Data diproses', ['jumlah_record' => count($processedData)]);

            if ($processedData->isEmpty()) {
                Log::warning('Data tidak ditemukan', [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'commodity' => $commodity
                ]);
                return back()->with('error', 'Data tidak ditemukan untuk rentang tanggal yang dipilih.');
            }

            // Generate PDF
            $pdf = PDF::loadView('pdf.report', [
                'data' => $processedData,
                'startDate' => $startDate->format('d-m-Y'),
                'endDate' => $endDate->format('d-m-Y'),
                'commodity' => $commodity,
                'hargaHet' => $hargaHet, // Juga kirim sebagai variabel terpisah untuk info header
                'hargaWaspada' => $hargaWaspada, // Baru: Kirim untuk info header
                'hargaIntervensi' => $hargaIntervensi // Baru: Kirim untuk info header
            ])->setPaper('A4', 'landscape');

            Log::info('PDF berhasil digenerate', [
                'commodity' => $commodity,
                'periode' => "{$startDate->toDateString()} sampai {$endDate->toDateString()}",
                'file_size' => strlen($pdf->output())
            ]);

            return $pdf->download('commodity_report.pdf');

        } catch (\Exception $e) {
            Log::error('Gagal generate PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat membuat laporan: ' . $e->getMessage());
        }
    }    
}
