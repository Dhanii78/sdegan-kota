<?php

namespace App\Exports;

use App\Models\Het;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HetExport implements FromCollection, WithHeadings
{
    protected $columns;

    public function __construct()
    {
        // Ambil semua kolom dari tabel het
        $this->columns = Schema::getColumnListing('het');
    }

    /**
     * Mengambil data dari tabel Het secara dinamis
     */
    public function collection()
    {
        return Het::select($this->columns)->get();
    }

    /**
     * Menambahkan header (judul kolom) di file Excel
     */
    public function headings(): array
    {
        return $this->columns;
    }
}
