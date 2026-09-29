<?php

namespace App\Exports;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VerificatorDocumentExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Query data dokumen dengan Eager Loading agar tidak N+1
     */
    public function query(): Builder
    {
        $query = Document::with(['applicant:id,name,email', 'verificator:id,name']);

        // Filter opsional berdasarkan status dokumen
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filter opsional berdasarkan rentang tanggal
        if (! empty($this->filters['start_date']) && ! empty($this->filters['end_date'])) {
            $query->whereBetween('created_at', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        return $query->latest();
    }

    /**
     * Header/Judul Kolom Excel
     */
    public function headings(): array
    {
        return [
            'No. Registrasi',
            'Judul Dokumen',
            'Nama Pemohon',
            'Email Pemohon',
            'Status',
            'Catatan Verifikator',
            'Tanggal Diajukan',
            'Tanggal Disetujui',
        ];
    }

    /**
     * Mapping data per baris
     */
    public function map($document): array
    {
        return [
            $document->number_registration,
            $document->title,
            $document->applicant?->name ?? '-',
            $document->applicant?->email ?? '-',
            strtoupper(is_object($document->status) ? $document->status->value : $document->status),
            $document->verificator_notes ?? '-',
            $document->submitted_at ? $document->submitted_at->format('d-m-Y H:i') : '-',
            $document->approved_at ? $document->approved_at->format('d-m-Y H:i') : '-',
        ];
    }

    /**
     * Styling baris Header (Bold)
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
