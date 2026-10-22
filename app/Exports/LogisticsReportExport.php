<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LogisticsReportExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithCustomStartCell, 
    WithTitle,
    WithColumnFormatting,
    WithEvents
{
    protected array $filters;
    protected int $rowNumber = 0;
    protected int $totalDataRows = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Tentukan sel mulai tabel (Row 5 memberi ruang untuk header dokumen).
     */
    public function startCell(): string
    {
        return 'A5';
    }

    /**
     * Judul sheet pada file Excel.
     */
    public function title(): string
    {
        return 'Rekap Logistik';
    }

    /**
     * Ambil seluruh data laporan sesuai filter yang aktif.
     */
    public function collection(): Collection
    {
        $startDate = $this->filters['start_date'] ?? null;
        $endDate   = $this->filters['end_date'] ?? null;
        $cabang    = $this->filters['cabang'] ?? null;
        $ekspedisi = $this->filters['ekspedisi'] ?? null;
        $status    = $this->filters['status'] ?? null;
        $search    = $this->filters['search'] ?? null;

        $query = DB::table('collie_sppbs')
            ->select(
                'no_pl',
                'tgl_col',
                DB::raw('MAX(cabang) as cabang'),
                DB::raw('MAX(no_sppb) as no_sppb'),
                DB::raw('MAX(tgl_sppb) as tgl_sppb'),
                DB::raw('MAX(ekspedisi) as ekspedisi'),
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('MAX(customer_address) as customer_address'),
                DB::raw('MAX(supirnopol) as supirnopol'),
                DB::raw('COUNT(DISTINCT no_sm) as total_sm'),
                DB::raw('COUNT(id) as total_items'),
                DB::raw('COALESCE(MAX(jumlah), SUM(qty1)) as total_collie'),
                DB::raw('COALESCE(MAX(berat), SUM(qtyr)) as total_berat')
            )
            ->whereNotNull('no_pl')
            ->groupBy('no_pl', 'tgl_col');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tgl_col', [$startDate, $endDate]);
        }

        if (!empty($cabang)) {
            $query->where('cabang', $cabang);
        }

        if (!empty($ekspedisi)) {
            $query->where('ekspedisi', $ekspedisi);
        }

        if ($status === 'sudah') {
            $query->whereNotNull('no_sppb')->where('no_sppb', '!=', '');
        } elseif ($status === 'belum') {
            $query->where(function ($q) {
                $q->whereNull('no_sppb')->orWhere('no_sppb', '');
            });
        }

        if (!empty($search)) {
            $isPgsql = DB::connection()->getDriverName() === 'pgsql';
            $likeOp = $isPgsql ? 'ilike' : 'like';

            $keywords = array_filter(preg_split('/\s+/', $search));

            $query->where(function ($q) use ($keywords, $likeOp) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word, $likeOp) {
                        $sub->where('no_pl', $likeOp, "%{$word}%")
                            ->orWhere('no_sppb', $likeOp, "%{$word}%")
                            ->orWhere('ekspedisi', $likeOp, "%{$word}%")
                            ->orWhere('customer_name', $likeOp, "%{$word}%");
                    });
                }
            });
        }

        $data = $query
            ->orderByRaw('tgl_col DESC NULLS LAST')
            ->orderByRaw('no_pl DESC NULLS LAST')
            ->get();

        $this->totalDataRows = $data->count();

        return $data;
    }

    /**
     * Judul kolom header pada Excel (Row 5).
     */
    public function headings(): array
    {
        return [
            'NO',
            'NO. PACKING LIST',
            'TGL COLLIE',
            'NO. SPPB',
            'TGL SPPB',
            'CABANG',
            'EKSPEDISI',
            'CUSTOMER / PENERIMA',
            'ALAMAT PENERIMA',
            'SUPIR & NOPOL',
            'JML SM',
            'TOTAL KOLI',
            'BERAT (KG)',
            'STATUS DOKUMEN',
        ];
    }

    /**
     * Transformasi data baris per baris.
     */
    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->no_pl ?? '-',
            !empty($row->tgl_col) ? date('d/m/Y', strtotime($row->tgl_col)) : '-',
            !empty($row->no_sppb) ? $row->no_sppb : '-',
            !empty($row->tgl_sppb) ? date('d/m/Y', strtotime($row->tgl_sppb)) : '-',
            $row->cabang ?: 'JKT0',
            $row->ekspedisi ?: '-',
            $row->customer_name ?: '-',
            $row->customer_address ?: '-',
            $row->supirnopol ?: '-',
            (int) ($row->total_sm ?? 0),
            (int) ($row->total_collie ?? 0),
            (float) round((float) ($row->total_berat ?? 0), 1),
            !empty($row->no_sppb) ? 'SPPB Terbit' : 'Draft PL',
        ];
    }

    /**
     * Format angka dan desimal pada kolom Excel.
     */
    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => '#,##0.0',
        ];
    }

    /**
     * Daftarkan event styling dan penyisipan header dokumen & baris total.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $startDate = $this->filters['start_date'] ?? date('Y-01-01');
                $endDate   = $this->filters['end_date'] ?? date('Y-m-d');
                $periodeText = date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate));

                $filtersApplied = [];
                if (!empty($this->filters['cabang'])) $filtersApplied[] = 'Cabang: ' . $this->filters['cabang'];
                if (!empty($this->filters['ekspedisi'])) $filtersApplied[] = 'Ekspedisi: ' . $this->filters['ekspedisi'];
                if (!empty($this->filters['status'])) $filtersApplied[] = 'Status: ' . ($this->filters['status'] === 'sudah' ? 'Sudah Terbit SPPB' : 'Belum Terbit SPPB');
                if (!empty($this->filters['search'])) $filtersApplied[] = 'Pencarian: "' . $this->filters['search'] . '"';
                $filterInfo = !empty($filtersApplied) ? implode(' | ', $filtersApplied) : 'Semua Data';

                // ==========================================
                // 1. KOP / HEADER PERUSAHAAN (Baris 1 - 3)
                // ==========================================
                $sheet->mergeCells('A1:N1');
                $sheet->setCellValue('A1', 'PT. OTTO PHARMACEUTICAL INDUSTRIES LTD - BANDUNG');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setRGB('0F172A');
                $sheet->getRowDimension(1)->setRowHeight(22);

                $sheet->mergeCells('A2:N2');
                $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI PACKING LIST & SPPB (LOGISTIK)');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('1E293B');
                $sheet->getRowDimension(2)->setRowHeight(18);

                $sheet->mergeCells('A3:N3');
                $sheet->setCellValue('A3', "Periode: {$periodeText}   |   Filter: {$filterInfo}   |   Dicetak: " . date('d/m/Y H:i:s'));
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');
                $sheet->getRowDimension(3)->setRowHeight(16);

                $sheet->getRowDimension(4)->setRowHeight(8); // Spacer row

                // ==========================================
                // 2. HEADER TABEL (Baris 5)
                // ==========================================
                $sheet->getRowDimension(5)->setRowHeight(28);
                $sheet->getStyle('A5:N5')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 10,
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B'], // Dark Slate/Navy
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '334155'],
                        ],
                    ],
                ]);

                // Beri warna pembeda pada kolom kunci (No PL & No SPPB)
                $sheet->getStyle('B5')->getFont()->getColor()->setRGB('38BDF8'); // Sky blue
                $sheet->getStyle('D5')->getFont()->getColor()->setRGB('FDE047'); // Yellow/Amber
                $sheet->getStyle('M5')->getFont()->getColor()->setRGB('FDE047');

                // ==========================================
                // 3. STYLING DATA ROWS & ALIGNMENTS
                // ==========================================
                $dataStartRow = 6;
                $dataEndRow   = $dataStartRow + $this->totalDataRows - 1;

                if ($this->totalDataRows > 0) {
                    // Border seluruh tabel data
                    $sheet->getStyle("A{$dataStartRow}:N{$dataEndRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Perataan kolom
                    $sheet->getStyle("A{$dataStartRow}:A{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$dataStartRow}:B{$dataEndRow}")->getFont()->setBold(true);
                    $sheet->getStyle("C{$dataStartRow}:C{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$dataStartRow}:D{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$dataStartRow}:E{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F{$dataStartRow}:F{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("L{$dataStartRow}:L{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("L{$dataStartRow}:L{$dataEndRow}")->getFont()->setBold(true);
                    $sheet->getStyle("M{$dataStartRow}:M{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("M{$dataStartRow}:M{$dataEndRow}")->getFont()->setBold(true);
                    $sheet->getStyle("N{$dataStartRow}:N{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Tinggi baris data
                    for ($r = $dataStartRow; $r <= $dataEndRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(20);
                    }

                    // ==========================================
                    // 4. BARIS GRAND TOTAL DI PALING BAWAH
                    // ==========================================
                    $totalRow = $dataEndRow + 1;
                    $sheet->getRowDimension($totalRow)->setRowHeight(24);

                    $sheet->mergeCells("A{$totalRow}:K{$totalRow}");
                    $sheet->setCellValue("A{$totalRow}", 'GRAND TOTAL REKAPITULASI:');
                    
                    // Rumus SUM Excel asli
                    $sheet->setCellValue("L{$totalRow}", "=SUM(L{$dataStartRow}:L{$dataEndRow})");
                    $sheet->setCellValue("M{$totalRow}", "=SUM(M{$dataStartRow}:M{$dataEndRow})");
                    $sheet->setCellValue("N{$totalRow}", '');

                    $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray([
                        'font' => [
                            'bold'  => true,
                            'color' => ['rgb' => '0F172A'],
                            'size'  => 10,
                        ],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F1F5F9'], // Slate 100
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475569']],
                            'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                            'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                            'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                        ],
                    ]);

                    $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("L{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("M{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("L{$totalRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("M{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.0');
                }

                // Freeze pane pada header tabel (Row 5) agar saat scroll ke bawah header tetap terlihat
                $sheet->freezePane('A6');
            },
        ];
    }
}
