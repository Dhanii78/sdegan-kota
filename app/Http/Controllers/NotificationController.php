<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Endpoint polling pengecekan status notifikasi secara berkala
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'unauthenticated'], 401);
        }

        $role = strtolower($user->role ?? '');

        // 1. Verifikator & Admin: Memeriksa apakah ada kiriman data baru dari Operator (status Belum Diverifikasi)
        if ($role === 'verifikator' || $role === 'admin') {
            $pendingQuery = Data::whereIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi']);
            $pendingCount = (clone $pendingQuery)->count();
            
            // Ambil data terbaru yang masuk
            $latestData = (clone $pendingQuery)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();

            $latestId = $latestData ? $latestData->id : 0;
            $latestTanggal = $latestData ? $latestData->tanggal : null;
            $latestTime = $latestData && $latestData->created_at 
                ? $latestData->created_at->toIso8601String() 
                : ($latestData ? (string)$latestData->tanggal : null);

            return response()->json([
                'status' => 'ok',
                'role' => $role,
                'has_notification' => $pendingCount > 0,
                'pending_count' => $pendingCount,
                'latest_id' => $latestId,
                'latest_tanggal' => $latestTanggal,
                'latest_time' => $latestTime,
                'type' => 'new_operator_data',
                'title' => 'Data Pangan Baru Masuk!',
                'message' => $latestTanggal 
                    ? "Operator telah mengirim data komoditas pangan untuk tanggal " . date('d-m-Y', strtotime($latestTanggal)) . ". Siap untuk diverifikasi."
                    : "Terdapat data pangan baru yang menunggu verifikasi.",
                'target_url' => route('dashboard_verifikator.verify_data')
            ]);
        }

        // 2. Operator: Memeriksa apakah ada data yang sudah divalidasi oleh Verifikator
        if ($role === 'operator') {
            $validatedQuery = Data::whereNotIn('status_verifikasi', ['Belum_Diverifikasi', 'Belum Diverifikasi']);
            
            // Ambil data yang paling terakhir diverifikasi / diupdate
            $latestValidated = (clone $validatedQuery)->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->first();

            $latestId = $latestValidated ? $latestValidated->id : 0;
            $latestTanggal = $latestValidated ? $latestValidated->tanggal : null;
            $latestStatus = $latestValidated ? $latestValidated->status_verifikasi : null;

            if ($latestValidated) {
                // Prioritaskan status ditolak/tidak valid/revisi jika ada salah satu komoditas yang bermasalah pada tanggal tersebut
                $anyRejected = Data::where('tanggal', $latestTanggal)
                    ->whereIn('status_verifikasi', ['Tidak_valid', 'Tidak Valid', 'Harga_Tidak_Wajar', 'Data_Ganda', 'Revisi'])
                    ->first();
                if ($anyRejected) {
                    $latestStatus = $anyRejected->status_verifikasi;
                }
            }

            $latestTime = $latestValidated && $latestValidated->updated_at 
                ? $latestValidated->updated_at->toIso8601String() 
                : ($latestValidated ? (string)$latestValidated->tanggal : null);

            $statusLabel = $latestStatus === 'Valid' ? 'Tervalidasi (Valid)' : ($latestStatus === 'Revisi' ? 'Perlu Revisi' : 'Ditolak / Catatan');

            return response()->json([
                'status' => 'ok',
                'role' => $role,
                'has_notification' => $latestValidated !== null,
                'latest_id' => $latestId,
                'latest_tanggal' => $latestTanggal,
                'latest_status' => $latestStatus,
                'latest_time' => $latestTime,
                'type' => 'validation_updated',
                'title' => 'Status Validasi Diperbarui!',
                'message' => $latestTanggal 
                    ? "Data tanggal " . date('d-m-Y', strtotime($latestTanggal)) . " telah diproses Verifikator dengan status: " . $statusLabel . "."
                    : "Verifikator telah memperbarui status validasi data pangan.",
                'target_url' => route('dashboard_operator.data_pangan')
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'role' => $role,
            'has_notification' => false
        ]);
    }
}
