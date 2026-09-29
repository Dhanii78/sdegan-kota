<?php

namespace App\Exports;

use App\Models\Rekap;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapExport implements FromCollection, WithHeadings
{
    protected $columns;

    public function __construct()
    {
        // Ambil semua nama kolom dari tabel 'rekap'
        $this->columns = Schema::getColumnListing('rekap');
    }

    /**
     * Ambil data dari tabel Rekap dengan kolom dinamis
     */
    public function collection()
    {
        return Rekap::select($this->columns)->get();
    }

    /**
     * Header Excel = Nama kolom tabel
     */
    public function headings(): array
    {
        return $this->columns;
    }
}
