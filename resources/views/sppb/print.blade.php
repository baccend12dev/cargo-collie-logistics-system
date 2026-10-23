<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPPB - {{ $header->no_sppb ?? $no_pl }}</title>
    <style>
        /* ============================================================== */
        /* 1. UKURAN KERTAS CUSTOM 16cm x 20cm (160mm x 200mm)            */
        /* ============================================================== */
        @page {
            size: 160mm 200mm;
            margin: 0; /* Margin 0 agar koordinat posisi presisi dengan blanko */
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 0;
            font-size: 9.5pt;
            line-height: 1.25;
        }

        /* Lembar Halaman Fisik 16cm x 20cm */
        .sppb-page {
            width: 160mm;
            height: 200mm;
            max-height: 200mm;
            margin: 0 auto 20px auto;
            padding: 5mm 6mm 5mm 6mm;
            background: #fff;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            page-break-after: always;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sppb-page:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        /* ============================================================== */
        /* 2. MODE CETAK: FORMULIR JADI (BLANKO) vs KERTAS POLOS (HVS)   */
        /* ============================================================== */
        /* Default: Mode Blanko (Hanya Data yang dicetak) */
        body.mode-blanko .template-static {
            visibility: hidden !important; /* Disembunyikan tapi tetap memakan ruang agar posisi data tidak geser */
        }
        body.mode-blanko .table-bordered th,
        body.mode-blanko .table-bordered td {
            border-color: transparent !important;
        }

        /* Mode HVS: Cetak Penuh Semua Garis & Kop */
        body.mode-hvs .template-static {
            visibility: visible !important;
        }
        body.mode-hvs .table-bordered th,
        body.mode-hvs .table-bordered td {
            border: 1px solid #000 !important;
        }

        /* ============================================================== */
        /* 3. SUSUNAN HEADER DOKUMEN                                      */
        /* ============================================================== */
        .header-section {
            margin-bottom: 2mm;
        }

        .company-kop {
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .doc-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 1.5mm 0 2mm 0;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 88mm 60mm;
            font-size: 9pt;
            line-height: 1.25;
            min-height: 22mm;
        }

        .info-row {
            display: flex;
            margin-bottom: 1mm;
        }

        .info-label {
            width: 18mm;
            font-weight: bold;
            flex-shrink: 0;
        }

        .info-val {
            flex: 1;
            word-break: break-word;
        }

        .via-label {
            width: 12mm;
            flex-shrink: 0;
        }

        .page-indicator {
            text-align: right;
            margin-top: 2mm;
            padding-right: 2mm;
            font-size: 8.5pt;
        }

        /* ============================================================== */
        /* 4. TABEL SURAT MUAT (TOTAL 14 BARIS SESUAI EXCEL TEMPLATE)     */
        /* ============================================================== */
        .table-container {
            flex: 1;
            margin-top: 1mm;
            margin-bottom: 2mm;
        }

        table.sm-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9pt;
        }

        table.sm-table th {
            height: 6mm;
            padding: 1mm 1.5mm;
            text-align: center;
            font-weight: bold;
            background: #fff;
            border: 1px solid #000;
        }

        table.sm-table td {
            height: 5.2mm; /* Tinggi presisi baris tabel */
            max-height: 5.2mm;
            padding: 0.6mm 1.5mm;
            vertical-align: middle;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* Baris Catatan Khusus di bawah list No SM */
        .note-row td {
            height: auto;
            min-height: 5.2mm;
            padding: 0.8mm 1.5mm;
            white-space: normal;
            vertical-align: top;
        }

        .col-note-cell {
            font-size: 8pt;
            font-weight: normal;
            text-align: left;
            line-height: 1.25;
            letter-spacing: 0.1px;
            word-break: break-word;
        }

        /* Garis bawah tabel hanya pada baris paling akhir tabel */
        table.sm-table tr.last-row td {
            border-bottom: 1px solid #000;
        }

        /* Lebar Kolom Presisi (Total 148mm = 160mm - margin 12mm) */
        .col-no {
            width: 10mm;
            text-align: center;
        }
        .col-sm {
            width: 58mm;
            text-align: left;
            font-weight: bold;
        }
        .col-tgl {
            width: 28mm;
            text-align: center;
        }
        .col-qty {
            width: 26mm;
            text-align: right;
            font-weight: bold;
        }
        .col-berat {
            width: 26mm;
            text-align: right;
            font-weight: bold;
        }

        /* ============================================================== */
        /* 5. TANDA TANGAN & KETERANGAN BAWAH                             */
        /* ============================================================== */
        .bottom-section {
            font-size: 8.5pt;
            margin-top: 1mm;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: 50mm 48mm 50mm;
            text-align: center;
            margin-bottom: 2mm;
        }

        .sig-col-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 9mm; /* Ruang tanda tangan / cap */
        }

        .sig-content {
            font-weight: bold;
            font-size: 9pt;
            min-height: 4mm;
        }

        .sig-line {
            width: 80%;
            margin: 0 auto 1mm auto;
            border-bottom: 1px solid #000;
        }

        .sig-subtext {
            font-size: 7.5pt;
        }

        .copy-note {
            font-size: 7.5pt;
            margin-top: 1.5mm;
            padding-top: 1mm;
            text-align: left;
            color: #111;
        }

        /* ============================================================== */
        /* 6. TOOLBAR KONTROL DI BROWSER                                  */
        /* ============================================================== */
        .print-toolbar {
            width: 160mm;
            margin: 0 auto 15px auto;
            background: #0f172a;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .btn-action {
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: 0.15s;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print { background: #0284c7; color: white; }
        .btn-print:hover { background: #0369a1; }
        .btn-back { background: #475569; color: white; }
        .btn-back:hover { background: #334155; }

        .mode-switch {
            display: inline-flex;
            align-items: center;
            background: #1e293b;
            padding: 4px 8px;
            border-radius: 6px;
            gap: 8px;
            cursor: pointer;
            font-size: 11px;
            color: #cbd5e1;
        }
        .mode-switch input {
            cursor: pointer;
        }

        /* ============================================================== */
        /* 7. ATURAN CETAK (@media print)                                 */
        /* ============================================================== */
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .sppb-page {
                box-shadow: none !important;
                margin: 0 !important;
                width: 160mm !important;
                height: 200mm !important;
                max-height: 200mm !important;
                padding: 5mm 6mm 5mm 6mm !important;
            }
        }
    </style>
</head>
<body class="mode-blanko" id="print-body">

    <!-- TOOLBAR CONTROLLER (Hanya tampil di layar monitor) -->
    <div class="print-toolbar no-print">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: bold; color: #38bdf8;">🖨️ Cetak SPPB</span>
            <span style="color: #64748b;">|</span>
            <span>Ukuran: <strong>16 x 20 cm</strong> (Max 10 Baris/Lembar)</span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <!-- Switch Mode Cetak -->
            <label class="mode-switch" title="Aktifkan jika mencetak di kertas HVS putih polos. Nonaktifkan jika mencetak di kertas formulir jadi (pre-printed)">
                <input type="checkbox" id="toggle-mode" onchange="toggleTemplateMode(this.checked)">
                <span>Cetak Garis & Kop (Kertas Polos)</span>
            </label>

            <button onclick="window.close(); window.history.back();" class="btn-action btn-back">
                Kembali
            </button>
            <button onclick="window.print()" class="btn-action btn-print">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                Cetak Sekarang
            </button>
        </div>
    </div>

    @php
        $itemsPerPage = 10;
        // Total baris slot fisik pada template tabel (baris 17 s/d 30 di template Excel = 14 baris)
        $totalTableSlots = 14;

        // Jika list SM kosong, buat 1 halaman kosong agar blanko tetap terisi
        $pages = $smList->isNotEmpty() ? $smList->chunk($itemsPerPage) : collect([collect([])]);
        $totalPages = $pages->count();

        // Total akumulasi koli dan berat
        $totalCollieAll = number_format($header->jumlah ?: $items->sum('qty1'));
        $totalBeratAll  = number_format($header->berat ?: $items->sum('qtyr'), 1);
        $globalRowIndex = 1;
    @endphp

    @foreach($pages as $pageIndex => $pageItems)
    @php
        $smCount = $pageItems->count();
        $isLastPage = ($pageIndex + 1 === $totalPages);
        $hasNote = ($isLastPage && !empty(trim($header->cat ?? '')));
        $noteSlots = $hasNote ? 1 : 0;
        $emptyRowsCount = max(0, $totalTableSlots - $smCount - $noteSlots);
    @endphp

    <!-- ============================================================== -->
    <!-- LEMBAR CETAK HALAMAN KE-{{ $pageIndex + 1 }} DARI {{ $totalPages }}              -->
    <!-- ============================================================== -->
    <div class="sppb-page">

        <!-- HEADER SECTION -->
        <div class="header-section">
            <div class="company-kop template-static">
                <div>PT. OTTO PHARMACEUTICAL INDUSTRIES</div>
                <div>JL. DR. SETIABUDI Km. 12,1</div>
                <div>BANDUNG</div>
            </div>

            <div class="doc-title template-static">
                SURAT PENGANTAR PENGIRIMAN BARANG
            </div>

            <div class="info-grid">
                <!-- Kolom Kiri: No SPPB & Kepada -->
                <div>
                    <div class="info-row">
                        <span class="info-label template-static">No. :</span>
                        <span class="info-val" style="font-weight: bold; letter-spacing: 0.5px;">
                            {{ $header->no_sppb ?? '0348/10/2026' }}
                        </span>
                    </div>
                    <div class="info-row" style="align-items: flex-start;">
                        <span class="info-label template-static">Kepada :</span>
                        <div class="info-val">
                            <div style="font-weight: bold;">{{ $header->customer_name ?? 'PT. Mensa Bina Sukses' }}</div>
                            <div style="white-space: pre-line; font-size: 8.5pt;">{{ $header->customer_address ?? 'Pulo Kambing II/26 Kawasan Industri Pulogadung Jakarta Timur' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Via & Halaman -->
                <div style="padding-left: 5mm;">
                    <div class="info-row">
                        <span class="via-label template-static">Via :</span>
                        <span class="info-val" style="font-weight: bold;">
                            {{ $header->ekspedisi ?? 'Tunggal Jaya Expressindo' }}
                        </span>
                    </div>
                    <div class="page-indicator">
                        <span class="template-static">Halaman :</span> &nbsp;
                        <strong>{{ $pageIndex + 1 }}</strong> / <strong>{{ $totalPages }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL RINCIAN (FIXED 14 BARIS TOTAL SESUAI EXCEL) -->
        <div class="table-container">
            <table class="sm-table table-bordered">
                <thead>
                    <tr class="template-static">
                        <th class="col-no">No.</th>
                        <th class="col-sm">Nomor Surat Muat</th>
                        <th class="col-tgl">Tanggal SM</th>
                        <th class="col-qty">Jumlah Collie</th>
                        <th class="col-berat">Berat (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 1. Baris Berisi Data SM (Maksimal 10 Baris per Halaman) -->
                    @foreach($pageItems as $sm)
                    @php 
                        $isDocFirstRow = ($globalRowIndex === 1);
                        $isTableRowLast = (!$hasNote && $emptyRowsCount === 0 && $loop->last);
                    @endphp
                    <tr class="{{ $isTableRowLast ? 'last-row' : '' }}">
                        <td class="col-no">{{ $globalRowIndex }}</td>
                        <td class="col-sm">{{ $sm['no_sm'] }}</td>
                        <td class="col-tgl">{{ $sm['tgl_sm'] }}</td>
                        <!-- Total Koli tercetak pada baris ke-1 dokumen -->
                        <td class="col-qty">
                            @if($isDocFirstRow)
                                {{ $totalCollieAll }}
                            @endif
                        </td>
                        <!-- Total Berat tercetak pada baris ke-1 dokumen -->
                        <td class="col-berat">
                            @if($isDocFirstRow)
                                {{ $totalBeratAll }}
                            @endif
                        </td>
                    </tr>
                    @php $globalRowIndex++; @endphp
                    @endforeach

                    <!-- 2. Baris Catatan (Tepat di bawah list No. SM, menimpa kolom ke samping sesuai gambar template) -->
                    @if($hasNote)
                    @php 
                        $isNoteLastRow = ($emptyRowsCount === 0);
                        $formattedNote = trim($header->cat);
                        if (!str_starts_with($formattedNote, '*')) {
                            $formattedNote = '* ' . $formattedNote;
                        }
                    @endphp
                    <tr class="note-row {{ $isNoteLastRow ? 'last-row' : '' }}">
                        <td class="col-no">&nbsp;</td>
                        <td colspan="4" class="col-note-cell">
                            {!! nl2br(e($formattedNote)) !!}
                        </td>
                    </tr>
                    @endif

                    <!-- 3. Baris Kosong Pelengkap (Agar Tinggi Tabel Konsisten Pas 14 Baris Sesuai Garis Template) -->
                    @for($i = 0; $i < $emptyRowsCount; $i++)
                    @php 
                        $isLastEmptyRow = ($i === $emptyRowsCount - 1);
                    @endphp
                    <tr class="empty-row {{ $isLastEmptyRow ? 'last-row' : '' }}">
                        <td class="col-no">&nbsp;</td>
                        <td class="col-sm">&nbsp;</td>
                        <td class="col-tgl">&nbsp;</td>
                        <td class="col-qty">&nbsp;</td>
                        <td class="col-berat">&nbsp;</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- BOTTOM SECTION (TANDA TANGAN & KETERANGAN) -->
        <div class="bottom-section">
            <div class="signatures-grid">
                <!-- Kolom 1: Penerima -->
                <div>
                    <div class="sig-col-title template-static">PENERIMA</div>
                    <div class="sig-content">
                        <!-- Ruang tanda tangan / cap -->
                    </div>
                    <div class="sig-line template-static"></div>
                    <div class="sig-subtext template-static">Tanda Tangan & Cap Perusahaan</div>
                </div>

                <!-- Kolom 2: Angkutan -->
                <div>
                    <div class="sig-col-title template-static">ANGKUTAN</div>
                    <div class="sig-content">
                        {{ $header->supirnopol ?? '' }}
                    </div>
                    <div class="sig-line template-static" style="visibility: hidden;"></div>
                    <div class="sig-subtext">&nbsp;</div>
                </div>

                <!-- Kolom 3: Pengirim (Menggunakan Nama User yang Sedang Login) -->
                <div>
                    <div class="sig-col-title template-static">PENGIRIM</div>
                    <div class="sig-content">
                        {{ auth()->user()->name ?? 'PENGIRIM' }}
                    </div>
                    <div class="sig-line template-static" style="visibility: hidden;"></div>
                    <div class="sig-subtext">&nbsp;</div>
                </div>
            </div>

            <!-- Catatan Tembusan Rangkap -->
            <div class="copy-note template-static">
                Putih (2 Lembar) - Ekspedisi, Hijau - Distribusi. Biru - Akutansi Biaya, Kuning - Arsip
            </div>
        </div>

    </div>
    @endforeach

    <script>
        /**
         * Mengalihkan Mode Cetak:
         * - mode-blanko: Hanya data yang tercetak (untuk formulir jadi/bercetak fisik)
         * - mode-hvs: Cetak penuh semua kop dan border garis tabel (untuk kertas kosong polos)
         */
        function toggleTemplateMode(showFullTemplate) {
            const body = document.getElementById('print-body');
            if (showFullTemplate) {
                body.classList.remove('mode-blanko');
                body.classList.add('mode-hvs');
            } else {
                body.classList.remove('mode-hvs');
                body.classList.add('mode-blanko');
            }
        }
    </script>
</body>
</html>
