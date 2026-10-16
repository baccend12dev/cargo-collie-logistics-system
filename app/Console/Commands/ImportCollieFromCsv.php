<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCollieFromCsv extends Command
{
    /**
     * Nama dan signature command artisan
     */
    protected $signature = 'import:collie-csv
                            {file : Path ke file CSV yang akan diimpor}
                            {--chunk=500 : Jumlah baris yang diproses setiap batch insert}';

    /**
     * Deskripsi command
     */
    protected $description = 'Import data Collie dari file CSV legacy ke tabel collie_sppbs PostgreSQL. Data lama akan dihapus terlebih dahulu.';

    /**
     * Panjang maksimum kolom string (untuk keamanan truncate)
     */
    private const COL_MAX_LEN = [
        'no_pl'            => 100,
        'no_sm'            => 150,
        'no_col'           => 150,
        'cabang'           => 100,
        'code'             => 100,
        'ekspedisi'        => 200,
        'no_sppb'          => 150,
        'supirnopol'       => 250,
        'cab'              => 100,
        'customer_name'    => 200,
    ];

    /**
     * Jalankan command
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $chunkSize = (int) $this->option('chunk');

        // -------------------------------------------------------
        // 1. Validasi file
        // -------------------------------------------------------
        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return self::FAILURE;
        }

        $this->info("File CSV  : {$filePath}");
        $this->info("Chunk size: {$chunkSize}");

        // -------------------------------------------------------
        // 2. Hapus semua data lama + reset sequence ID
        // -------------------------------------------------------
        $this->warn('Menghapus semua data lama di tabel collie_sppbs ...');
        DB::statement('TRUNCATE TABLE collie_sppbs RESTART IDENTITY CASCADE');
        $this->info('Data lama berhasil dihapus.');

        // -------------------------------------------------------
        // 3. Buka file CSV
        // -------------------------------------------------------
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error('Gagal membuka file CSV.');
            return self::FAILURE;
        }

        // Baca baris header — deteksi BOM UTF-8
        $firstBytes = fread($handle, 3);
        if ($firstBytes !== "\xEF\xBB\xBF") {
            // Bukan BOM, kembalikan pointer ke awal
            rewind($handle);
        }

        // Deteksi separator: ; atau ,
        $rawHeader = fgets($handle);
        $separator = (substr_count($rawHeader, ';') >= substr_count($rawHeader, ',')) ? ';' : ',';
        rewind($handle);

        // Skip BOM jika ada
        $firstBytes = fread($handle, 3);
        if ($firstBytes !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, $separator);
        if (!$header) {
            $this->error('File CSV kosong atau tidak dapat dibaca.');
            fclose($handle);
            return self::FAILURE;
        }

        // Normalisasi header ke lowercase & trim
        $header = array_map(fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x7F\xFE\xFF]/u', '', $h))), $header);
        $this->line('Header CSV: ' . implode(', ', $header));
        $this->line('Jumlah kolom: ' . count($header));

        // -------------------------------------------------------
        // 4. Mapping kolom CSV → kolom tabel
        // -------------------------------------------------------
        $colMap = [
            'id'               => 'old_id',
            'no_pl'            => 'no_pl',
            'no_sm'            => 'no_sm',
            'no_col'           => 'no_col',
            'tgl_col'          => 'tgl_col',
            'cabang'           => 'cabang',
            'code'             => 'code',
            'qty'              => 'qty',
            'qty1'             => 'qty1',
            'qty2'             => 'qty2',
            'qtyr'             => 'qtyr',
            'ad'               => 'ad',
            'jucol'            => 'jucol',
            'ekspedisi'        => 'ekspedisi',
            'no_sppb'          => 'no_sppb',
            'tgl_sppb'         => 'tgl_sppb',
            'customer_name'    => 'customer_name',
            'customer_address' => 'customer_address',
            'creation_date'    => 'creation_date',
            'supirnopol'       => 'supirnopol',
            'cat'              => 'cat',
            'jumlah'           => 'jumlah',
            'berat'            => 'berat',
            'cab'              => 'cab',
        ];

        // -------------------------------------------------------
        // 5. Import baris per chunk
        // -------------------------------------------------------
        $now        = now()->toDateTimeString();
        $chunk      = [];
        $total      = 0;
        $skipped    = 0;
        $errors     = 0;

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% baris [%bar%] %elapsed:6s%');
        $bar->start();

        while (($row = fgetcsv($handle, 0, $separator)) !== false) {
            // Abaikan baris terlalu pendek (minimal perlu kolom: id, no_pl, code)
            if (count($row) < 3) {
                $skipped++;
                continue;
            }

            // Map ke array asosiatif (sesuai panjang header, abaikan kolom lebih)
            $data = [];
            foreach ($header as $idx => $key) {
                $data[$key] = $row[$idx] ?? null;
            }

            // Lewati baris garbage (no_pl dan code keduanya kosong)
            $noPl = trim($data['no_pl'] ?? '');
            $code = trim($data['code'] ?? '');
            if ($noPl === '' && $code === '') {
                $skipped++;
                continue;
            }

            // Lewati baris di mana no_pl tidak mengandung /PL (shift kolom dari embedded ; di CSV)
            if ($noPl !== '' && stripos($noPl, '/PL') === false) {
                $skipped++;
                continue;
            }

            // Siapkan record yang akan diinsert
            $record = ['created_at' => $now, 'updated_at' => $now];

            foreach ($colMap as $csvCol => $dbCol) {
                if (!array_key_exists($csvCol, $data)) {
                    $record[$dbCol] = null;
                    continue;
                }

                $val = isset($data[$csvCol]) ? trim((string)$data[$csvCol]) : null;

                // Nilai NULL / kosong
                if ($val === '' || strtoupper($val) === 'NULL') {
                    $record[$dbCol] = null;
                    continue;
                }

                // Pastikan encoding UTF-8 valid (mengatasi karakter non-UTF8 seperti derajat 0xb0 dari Windows-1252)
                if (!mb_check_encoding($val, 'UTF-8')) {
                    $val = mb_convert_encoding($val, 'UTF-8', 'Windows-1252');
                }

                // Konversi tanggal (tgl_col, tgl_sppb, creation_date)
                if (in_array($dbCol, ['tgl_col', 'tgl_sppb', 'creation_date'])) {
                    $record[$dbCol] = $this->parseDate($val);
                    continue;
                }

                // Konversi numerik integer
                if (in_array($dbCol, ['old_id', 'qty', 'qty1', 'qty2', 'ad', 'jucol', 'jumlah'])) {
                    // Hanya ambil jika benar-benar numeric
                    $cleanVal = preg_replace('/[^0-9\-]/', '', $val);
                    $record[$dbCol] = is_numeric($cleanVal) ? (int)$cleanVal : null;
                    continue;
                }

                // Konversi numerik desimal
                if (in_array($dbCol, ['qtyr', 'berat'])) {
                    $val = str_replace(',', '.', $val);
                    $record[$dbCol] = is_numeric($val) ? (float)$val : null;
                    continue;
                }

                // String — truncate sesuai batas kolom
                if (isset(self::COL_MAX_LEN[$dbCol])) {
                    $record[$dbCol] = mb_substr($val, 0, self::COL_MAX_LEN[$dbCol]);
                } else {
                    $record[$dbCol] = $val;
                }
            }

            $chunk[] = $record;
            $total++;
            $bar->advance();

            // Batch insert
            if (count($chunk) >= $chunkSize) {
                try {
                    DB::table('collie_sppbs')->insert($chunk);
                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Batch error: " . $e->getMessage());
                }
                $chunk = [];
            }
        }

        // Sisa chunk terakhir
        if (!empty($chunk)) {
            try {
                DB::table('collie_sppbs')->insert($chunk);
            } catch (\Exception $e) {
                $errors++;
                $this->error("Final batch error: " . $e->getMessage());
            }
        }

        fclose($handle);
        $bar->finish();
        $this->newLine(2);

        $dbCount = DB::table('collie_sppbs')->count();

        $this->info("✅ Import selesai!");
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total baris diproses', $total],
                ['Baris dilewati (terlalu pendek)', $skipped],
                ['Batch error', $errors],
                ['Record tersimpan di DB', $dbCount],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Parse berbagai format tanggal: d/m/Y, Y-m-d, d-m-Y, dll.
     */
    private function parseDate(?string $val): ?string
    {
        if (empty($val) || strtoupper($val) === 'NULL') {
            return null;
        }

        $val = trim($val);

        // Format d/m/Y atau d/m/Y H:i:s
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})/', $val, $m)) {
            try {
                $dt = \Carbon\Carbon::createFromFormat('d/m/Y', "{$m[1]}/{$m[2]}/{$m[3]}");
                return $dt ? $dt->toDateString() : null;
            } catch (\Exception $e) {
                return null;
            }
        }

        // Format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $val)) {
            return substr($val, 0, 10);
        }

        // Format d-m-Y
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})/', $val, $m)) {
            try {
                $dt = \Carbon\Carbon::createFromFormat('d-m-Y', "{$m[1]}-{$m[2]}-{$m[3]}");
                return $dt ? $dt->toDateString() : null;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
