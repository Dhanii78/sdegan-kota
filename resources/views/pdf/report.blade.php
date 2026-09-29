<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harga Komoditas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c5e92;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 0 auto;
        }

        .header-text {
            text-align: center;
            flex: 1;
        }

        .institution-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c5e92;
            text-transform: uppercase;
            line-height: 1.3;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .institution-address {
            font-size: 11px;
            line-height: 1.4;
            color: #555;
        }

        h2 {
            text-align: center;
            font-size: 18px;
            margin: 15px 0;
            color: #2c5e92;
            padding: 8px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            background-color: #f8fafc;
        }

        .info-container {
            background-color: #f5f9ff;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
            border-left: 4px solid #2c5e92;
        }

        .info-item {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #2c5e92;
        }

        .info-value {
            flex: 1;
        }

        .het-highlight {
            font-weight: bold;
            color: #d9534f;
            background-color: #fff3f3;
            padding: 2px 6px;
            border-radius: 3px;
        }

        /* New styles for Waspada and Intervensi */
        .indicator-aman {
            color: #5cb85c; /* Green for safe */
            font-weight: bold;
        }

        .indicator-waspada {
            color: #f0ad4e; /* Orange for warning */
            font-weight: bold;
        }

        .indicator-intervensi {
            color: #d9534f; /* Red for intervention */
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th {
            background-color: #2c5e92;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
        }

        td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        tbody tr:hover {
            background-color: #f0f7ff;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        @media print {
            body {
                margin: 10mm;
            }

            .header, .header-container, .header-text, .institution-name {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-container">
            <div class="header-text">
                <div class="institution-name">
                    DINAS KETAHANAN PANGAN DAN PERTANIAN<br>
                    KOTA KEDIRI
                </div>
                <div class="institution-address">
                    JL. Brigadir Jenderal Polisi Imam Bachri, No. 98A, Bangsal, Kec. Pesantren,<br>
                    Kota Kediri, Jawa Timur 64131
                </div>
            </div>
        </div>
    </div>

    <h2>LAPORAN HARGA KOMODITAS {{ strtoupper($commodity) }}</h2>
    
    <div class="info-container">
        <div class="info-item">
            <div class="info-label">Periode Laporan:</div>
            <div class="info-value">{{ $startDate }} s/d {{ $endDate }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Harga HET/HAP:</div>
            <div class="info-value">
                @if(isset($data->first()->harga_het) && $data->first()->harga_het)
                    <span class="het-highlight">Rp {{ number_format($data->first()->harga_het, 0, ',', '.') }}</span>
                @else
                    <span style="color: #777;">Tidak tersedia</span>
                @endif
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Harga Waspada:</div>
            <div class="info-value">
                @if(isset($hargaWaspada))
                    Rp {{ number_format($hargaWaspada, 0, ',', '.') }}
                @else
                    <span style="color: #777;">Tidak tersedia</span>
                @endif
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Harga Intervensi:</div>
            <div class="info-value">
                @if(isset($hargaIntervensi))
                    Rp {{ number_format($hargaIntervensi, 0, ',', '.') }}
                @else
                    <span style="color: #777;">Tidak tersedia</span>
                @endif
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="15%">Harga Sebelumnya</th>
                <th width="15%">Harga Hari Ini</th>
                <th width="15%">Rata-rata 7 Hari</th>
                <th width="15%">Indikator 7 Hari</th>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->tanggal }}</td>
                    <td>Rp {{ number_format($row->harga_kemarin, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($row->harga_hari_ini, 0, ',', '.') }}</strong></td>
                    <td>Rp {{ number_format($row->harga_rata_rata_7_hari, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            $statusText = 'Aman'; // Default status
                    
                            // Gunakan rata-rata 7 hari untuk indikator 7 hari
                            $currentPrice = $row->harga_rata_rata_7_hari;
                            $waspadaPrice = $row->harga_waspada ?? null;
                            $intervensiPrice = $row->harga_intervensi ?? null;
                    
                            // Pastikan semua harga bertipe numerik
                            $currentPrice = (float) $currentPrice;
                            $waspadaPrice = (float) $waspadaPrice;
                            $intervensiPrice = (float) $intervensiPrice;
                    
                            if ($intervensiPrice !== null && $currentPrice >= $intervensiPrice) {
                                $statusClass = 'indicator-intervensi';
                                $statusText = 'Intervensi';
                            } elseif ($waspadaPrice !== null && $currentPrice >= $waspadaPrice) {
                                $statusClass = 'indicator-waspada';
                                $statusText = 'Waspada';
                            } else {
                                $statusClass = 'indicator-aman';
                                $statusText = 'Aman';
                            }
                        @endphp
                        <span class="{{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }} | Dinas Ketahanan Pangan dan Pertanian Kota Kediri
    </div>
</body>
</html>