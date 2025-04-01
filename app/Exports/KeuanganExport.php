<?php

namespace App\Exports;

use App\Models\Administrasi;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KeuanganExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tahunAjaranId;
    protected $tahunAjaran;

    public function __construct($tahunAjaran)
    {
        $this->tahunAjaran = $tahunAjaran;
        $this->tahunAjaranId = $tahunAjaran->id;
    }

    public function collection()
    {
        $administrasis = Administrasi::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->with(['pendaftaran.jurusan'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($administrasis->isEmpty()) {
            // Return empty data placeholder
            return collect([[
                'No' => '-',
                'Nama Siswa' => 'Tidak ada data pembayaran',
                'Jurusan' => '-',
                'No. Pembayaran' => '-',
                'Total Biaya' => '-',
                'Total Bayar' => '-',
                'Sisa Pembayaran' => '-',
                'Status' => '-',
                'Tanggal Pembayaran' => '-'
            ]]);
        }

        return $administrasis;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Jurusan',
            'No. Pembayaran',
            'Total Biaya',
            'Total Bayar',
            'Sisa Pembayaran',
            'Status',
            'Tanggal Pembayaran'
        ];
    }

    public function map($administrasi): array
    {
        // Check if it's our empty data placeholder
        if (!$administrasi instanceof Administrasi) {
            return [
                $administrasi['No'],
                $administrasi['Nama Siswa'],
                $administrasi['Jurusan'],
                $administrasi['No. Pembayaran'],
                $administrasi['Total Biaya'],
                $administrasi['Total Bayar'],
                $administrasi['Sisa Pembayaran'],
                $administrasi['Status'],
                $administrasi['Tanggal Pembayaran']
            ];
        }

        static $no = 0;
        $no++;

        return [
            $no,
            $administrasi->pendaftaran->nama ?? 'N/A',
            $administrasi->pendaftaran->jurusan->nama_jurusan ?? 'N/A',
            $administrasi->no_bayar ?? '-',
            'Rp ' . number_format($administrasi->total_biaya ?? 0, 0, ',', '.'),
            'Rp ' . number_format($administrasi->total_bayar ?? 0, 0, ',', '.'),
            'Rp ' . number_format($administrasi->sisa_pembayaran ?? 0, 0, ',', '.'),
            $administrasi->status_pembayaran ?? 'N/A',
            $administrasi->tanggal_bayar ? $administrasi->tanggal_bayar_pendaftaran->format('d/m/Y') : '-',
            $administrasi->tanggal_bayar ? $administrasi->tanggal_bayar_ppdb->format('d/m/Y') : '-',
            $administrasi->tanggal_bayar ? $administrasi->tanggal_bayar_mpls->format('d/m/Y') : '-',
            $administrasi->tanggal_bayar ? $administrasi->tanggal_bayar_awal_tahun->format('d/m/Y') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the last row number
        $lastRow = $sheet->getHighestRow();

        // Basic styles for header
        $styles = [
            1 => ['font' => ['bold' => true]],
            'A1:I1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ],
                'font' => ['bold' => true]
            ],
        ];

        // Add borders to all cells
        $sheet->getStyle('A1:I'.$lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(30); // Nama Siswa
        $sheet->getColumnDimension('C')->setWidth(20); // Jurusan
        $sheet->getColumnDimension('D')->setWidth(20); // No. Pembayaran
        $sheet->getColumnDimension('E')->setWidth(15); // Total Biaya
        $sheet->getColumnDimension('F')->setWidth(15); // Total Bayar
        $sheet->getColumnDimension('G')->setWidth(15); // Sisa Pembayaran
        $sheet->getColumnDimension('H')->setWidth(12); // Status
        $sheet->getColumnDimension('I')->setWidth(20); // Tanggal Pembayaran

        // Center align the 'No' column
        $sheet->getStyle('A1:A'.$lastRow)->getAlignment()->setHorizontal('center');

        // Right align the amount columns
        $sheet->getStyle('E2:G'.$lastRow)->getAlignment()->setHorizontal('right');

        // Set title
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN PPDB ' . $this->tahunAjaran->tahun_ajaran);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        return $styles;
    }
}
