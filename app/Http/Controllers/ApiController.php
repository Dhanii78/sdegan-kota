<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\rekap;
use App\Models\sumber_data;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * ============================================================
     * LOGIN API MENGGUNAKAN LARAVEL SANCTUM
     * Login menggunakan ID + Password
     * ============================================================
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'password' => 'required|string',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::find($validated['id']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID atau password salah.',
            ], 401);
        }

        $user->tokens()
            ->where('name', $validated['device_name'])
            ->delete();

        $token = $user->createToken(
            $validated['device_name']
        )->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login API berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? null,
            ],
        ], 200);
    }


    /**
     * ============================================================
     * LOGOUT API
     * ============================================================
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout API berhasil.',
        ], 200);
    }


    /**
     * ============================================================
     * GET USER YANG SEDANG LOGIN
     * ============================================================
     */
    public function user(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama ?? $user->name,
                'email' => $user->email,
                'role' => $user->role ?? null,
            ],
        ], 200);
    }


    /**
     * ============================================================
     * MENGAMBIL DAFTAR NAMA KOMODITAS
     * ============================================================
     */
    public function getKomoditasData()
    {
        $komoditasList = DB::table('sumber_data')
            ->pluck('nama_komoditas');

        $result = [];

        foreach ($komoditasList as $komoditas) {
            $result[] = [
                'nama_komoditas' => $komoditas,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ], 200);
    }


    /**
     * ============================================================
     * INSERT DATA DARI MOBILE / API (Menggunakan updateOrCreate)
     * ============================================================
     */
    public function insertFromMobile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date',
                'data' => 'required|array|min:1',
                'data.*.nama_komoditas' => 'required|string',
                'data.*.harga_hari_ini' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::warning(
                    'Validasi insert data gagal',
                    $validator->errors()->toArray()
                );

                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            $lastHet = DB::table('het')
                ->orderByDesc('tanggal')
                ->first();

            $rekapKemarin = DB::table('rekap')
                ->orderByDesc('tanggal')
                ->first();

            if (!$lastHet || !$rekapKemarin) {
                throw new \Exception(
                    'Data HET atau Rekap tidak ditemukan.'
                );
            }

            foreach ($request->data as $item) {
                $komoditas = $item['nama_komoditas'];

                $hargaHet = $lastHet->{$komoditas} ?? 0;
                $hargaKemarin = $rekapKemarin->{$komoditas} ?? 0;
                $hargaHariIni = $item['harga_hari_ini'];

                Log::info(
                    "Memproses komoditas: {$komoditas}",
                    [
                        'harga_het' => $hargaHet,
                        'harga_kemarin' => $hargaKemarin,
                        'harga_hari_ini' => $hargaHariIni,
                    ]
                );

                Data::updateOrCreate(
                    [
                        'tanggal' => $request->tanggal,
                        'nama_komoditas' => $komoditas,
                    ],
                    [
                        'harga_het' => $hargaHet,
                        'harga_kemarin' => $hargaKemarin,
                        'harga_hari_ini' => $hargaHariIni,
                        'status_verifikasi' => 'Belum_Diverifikasi',
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan.',
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Insert Data Error: ' . $e->getMessage(),
                [
                    'request_data' => $request->all(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * ============================================================
     * MENGAMBIL SEMUA DATA KOMODITAS (Dengan Filter & Diurutkan)
     * ============================================================
     */
    public function getDataKomoditas(Request $request)
    {
        $query = Data::select(
            'id',
            'tanggal',
            'nama_komoditas',
            'harga_het',
            'harga_kemarin',
            'harga_hari_ini',
            'status_verifikasi'
        );

        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->has('nama_komoditas') && !empty($request->nama_komoditas)) {
            $query->where('nama_komoditas', 'LIKE', '%' . $request->nama_komoditas . '%');
        }

        $komoditasData = $query->orderByDesc('id')->get();

        return response()->json([
            'status' => 'success',
            'data' => $komoditasData,
        ], 200);
    }


    /**
     * ============================================================
     * UPDATE HARGA HARI INI
     * ============================================================
     */
    public function updateHargaHariIni(Request $request, $id)
    {
        $validated = $request->validate([
            'harga_hari_ini' => 'required|numeric|min:0',
        ]);

        $komoditas = Data::find($id);

        if (!$komoditas) {
            return response()->json([
                'status' => 'error',
                'message' => 'Komoditas tidak ditemukan.',
            ], 404);
        }

        $komoditas->harga_hari_ini = $validated['harga_hari_ini'];
        $komoditas->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Harga hari ini berhasil diperbarui.',
        ], 200);
    }


    /**
     * ============================================================
     * MENGIRIM NOTIFIKASI FCM
     * ============================================================
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ])->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'to' => $validated['token'],
                'notification' => [
                    'title' => $validated['title'],
                    'body' => $validated['body'],
                ],
                'priority' => 'high',
            ]
        );

        return response()->json([
            'success' => true,
            'response' => $response->json(),
        ]);
    }


    /**
     * ============================================================
     * VALUASI HARGA PANGAN
     * ============================================================
     */
    public function getValuasi()
    {
        $sumberData = sumber_data::all();

        $rekap = rekap::orderBy(
            'tanggal',
            'desc'
        )->take(2)->get();

        $latestRekap = $rekap->first();

        $previousRekap =
            $rekap->count() > 1
                ? $rekap->last()
                : null;

        if (!$latestRekap) {
            return response()->json([
                'success' => false,
                'message' => 'Data rekap belum tersedia.',
            ], 404);
        }

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

        $sevenDayAverage = [];

        foreach ($komoditasColumns as $column) {
            $prices = rekap::orderBy(
                'tanggal',
                'desc'
            )
                ->limit(7)
                ->get([$column]);

            $filtered = $prices->filter(
                function ($item) use ($column) {
                    return is_numeric(
                        $item->{$column}
                    );
                }
            );

            $sevenDayAverage[$column] =
                $filtered->count() > 0
                    ? round(
                        $filtered->avg($column),
                        2
                    )
                    : null;
        }

        $priceChanges = [];

        if ($previousRekap) {
            foreach ($komoditasColumns as $column) {
                $latestPrice = $latestRekap->{$column};
                $previousPrice = $previousRekap->{$column};

                if (
                    is_numeric($latestPrice) &&
                    is_numeric($previousPrice)
                ) {
                    if ($latestPrice > $previousPrice) {
                        $priceChanges[$column] = 'up';
                    } elseif ($latestPrice < $previousPrice) {
                        $priceChanges[$column] = 'down';
                    } else {
                        $priceChanges[$column] = 'same';
                    }
                } else {
                    $priceChanges[$column] = null;
                }
            }
        }

        $indicators = $this->calculateIndicators(
            $latestRekap,
            $sumberData
        );

        $averageIndicators = $this->calculateAverageIndicators(
            $sevenDayAverage,
            $sumberData
        );

        $data = [];

        foreach ($sumberData as $item) {
            $nama = $item->nama_komoditas;

            $data[] = [
                'komoditas' => $nama,
                'satuan' => $item->satuan,
                'hethap' => $item->hethap,
                'harga_terkini' => $latestRekap->{$nama} ?? null,
                'harga_kemarin' => $previousRekap ? ($previousRekap->{$nama} ?? null) : null,
                'rata_rata_7_hari' => $sevenDayAverage[$nama] ?? null,
                'indikator_hari_ini' => $indicators[$nama] ?? null,
                'indikator_rata_rata_7_hari' => $averageIndicators[$nama] ?? null,
                'perubahan_harga' => $priceChanges[$nama] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'tanggal_data' => $latestRekap->tanggal,
            'data' => $data,
        ], 200);
    }


    /**
     * ============================================================
     * VALUASI HARGA PANGAN SPLP
     * ============================================================
     */
    public function getValuasi2()
    {
        $sumberData = sumber_data::all();

        $rekap = rekap::orderBy(
            'tanggal',
            'desc'
        )->take(2)->get();

        $latestRekap = $rekap->first();

        $previousRekap =
            $rekap->count() > 1
                ? $rekap->last()
                : null;

        if (!$latestRekap) {
            return response()->json([
                'success' => false,
                'message' => 'Data rekap belum tersedia.',
            ], 404);
        }

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

        $sevenDayAverage = [];

        foreach ($komoditasColumns as $column) {
            $prices = rekap::orderBy(
                'tanggal',
                'desc'
            )
                ->limit(7)
                ->get([$column]);

            $filtered = $prices->filter(
                function ($item) use ($column) {
                    return is_numeric(
                        $item->{$column}
                    );
                }
            );

            $sevenDayAverage[$column] =
                $filtered->count() > 0
                    ? round(
                        $filtered->avg($column),
                        2
                    )
                    : null;
        }

        $priceChanges = [];

        if ($previousRekap) {
            foreach ($komoditasColumns as $column) {
                $latestPrice = $latestRekap->{$column};
                $previousPrice = $previousRekap->{$column};

                if (
                    is_numeric($latestPrice) &&
                    is_numeric($previousPrice)
                ) {
                    if ($latestPrice > $previousPrice) {
                        $priceChanges[$column] = 'up';
                    } elseif ($latestPrice < $previousPrice) {
                        $priceChanges[$column] = 'down';
                    } else {
                        $priceChanges[$column] = 'same';
                    }
                } else {
                    $priceChanges[$column] = null;
                }
            }
        }

        $indicators = $this->calculateIndicators(
            $latestRekap,
            $sumberData
        );

        $averageIndicators = $this->calculateAverageIndicators(
            $sevenDayAverage,
            $sumberData
        );

        $data = [];

        foreach ($sumberData as $item) {
            $nama = $item->nama_komoditas;

            $data[] = [
                'komoditas' => $nama,
                'satuan' => $item->satuan,
                'hethap' => $item->hethap,
                'harga_terkini' => $latestRekap->{$nama} ?? null,
                'harga_kemarin' => $previousRekap ? ($previousRekap->{$nama} ?? null) : null,
                'rata_rata_7_hari' => $sevenDayAverage[$nama] ?? null,
                'indikator_hari_ini' => $indicators[$nama] ?? null,
                'indikator_rata_rata_7_hari' => $averageIndicators[$nama] ?? null,
                'perubahan_harga' => $priceChanges[$nama] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tanggal_data' => $latestRekap->tanggal,
                'data_valuasi' => $data,
            ],
        ], 200);
    }


    /**
     * ============================================================
     * HITUNG INDIKATOR HARGA
     * ============================================================
     */
    private function calculateIndicators(
        $latestRekap,
        $sumberData
    ) {
        $indicators = [];

        foreach ($sumberData as $data) {
            $namaKomoditas = $data->nama_komoditas;
            $hargaH = $latestRekap->{$namaKomoditas} ?? null;

            if ($hargaH === null) {
                $indicators[$namaKomoditas] = 'N/A';
                continue;
            }

            if ($hargaH < $data->hethap) {
                $waspadaLimit = $data->hethap * (1 + $data->waspada / 100);
                $intervensiLimit = $data->hethap * (1 + $data->intervensi / 100);

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
     * ============================================================
     * HITUNG INDIKATOR RATA-RATA 7 HARI
     * ============================================================
     */
    private function calculateAverageIndicators(
        $sevenDayAverage,
        $sumberData
    ) {
        $averageIndicators = [];

        foreach ($sumberData as $data) {
            $namaKomoditas = $data->nama_komoditas;
            $averagePrice = $sevenDayAverage[$namaKomoditas] ?? null;

            if ($averagePrice === null) {
                $averageIndicators[$namaKomoditas] = 'N/A';
                continue;
            }

            if ($averagePrice < $data->hethap) {
                $waspadaLimit = $data->hethap * (1 + $data->waspada / 100);
                $intervensiLimit = $data->hethap * (1 + $data->intervensi / 100);

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
}