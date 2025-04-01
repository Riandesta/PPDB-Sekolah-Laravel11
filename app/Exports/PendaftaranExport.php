<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendaftaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $pendaftaran = Pendaftaran::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->with(['jurusan', 'administrasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pendaftaran->isEmpty()) {
            // If no data, return a collection with one empty row
            return collect([[
                'No' => '-',
                'NISN' => '-',
                'Nama' => 'Tidak ada data pendaftaran',
                'Jurusan' => '-',
                'Status Seleksi' => '-',
                'Status Pembayaran' => '-',
                'Rata-rata Nilai' => '-',
                'Tanggal Pendaftaran' => '-'
            ]]);
        }

        return $pendaftaran;
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama',
            'Jurusan',
            'Status Seleksi',
            'Status Pembayaran',
            'Rata-rata Nilai',
            'Tanggal Pendaftaran'
        ];
    }

    public function map($pendaftar): array
    {
        // If it's the empty row we created for no data
        if (!$pendaftar instanceof Pendaftaran) {
            return [
                $pendaftar['No'],
                $pendaftar['NISN'],
                $pendaftar['Nama'],
                $pendaftar['Jurusan'],
                $pendaftar['Status Seleksi'],
                $pendaftar['Status Pembayaran'],
                $pendaftar['Rata-rata Nilai'],
                $pendaftar['Tanggal Pendaftaran']
            ];
        }

        static $no = 0;
        $no++;

        return [
            $no,
            $pendaftar->NISN ?? '-',
            $pendaftar->nama ?? '-',
            $pendaftar->jurusan->nama_jurusan ?? 'N/A',
            $pendaftar->status_seleksi ?? '-',
            $pendaftar->administrasi->status_pembayaran ?? 'Belum Ada',
            $pendaftar->rata_rata_nilai ? number_format($pendaftar->rata_rata_nilai, 2) : '-',
            $pendaftar->created_at ? $pendaftar->created_at->format('d/m/Y') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the last row number
        $lastRow = $sheet->getHighestRow();

        // Basic styles for header
        $styles = [
            1 => ['font' => ['bold' => true]],
            'A1:H1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ],
                'font' => ['bold' => true]
            ],
        ];

        // Add borders to all cells
        $sheet->getStyle('A1:H'.$lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Auto-size columns
        foreach(range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $styles;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Pendaftaran ' . $this->tahunAjaran->tahun_ajaran;
    }
}
