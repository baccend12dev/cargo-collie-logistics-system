<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPPB - {{ $header->no_sppb ?? $no_pl }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm 1.5cm 1cm 1.5cm;
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Cocok untuk continuous form / dot matrix / laser data */
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px 30px;
            font-size: 11pt;
            line-height: 1.35;
        }

        /* Mode Cetak Template Kertas (Hanya Data) */
        .mode-template-kertas .border-box,
        .mode-template-kertas table,
        .mode-template-kertas th,
        .mode-template-kertas td {
            border: none !important;
            background: transparent !important;
        }
        .mode-template-kertas th {
            visibility: hidden; /* Header kolom sudah tercetak di kertas fisik */
        }
        .mode-template-kertas .preprinted-hide {
            display: none !important;
        }

        /* Posisi Header Dokumen */
        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-top: 10px;
        }
        .no-sppb-text {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .date-text {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Customer & Ekspedisi */
        .mid-section {
            margin-bottom: 30px;
            min-height: 85px;
        }
        .customer-block {
            float: left;
            width: 52%;
            font-size: 10.5pt;
        }
        .customer-name {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .customer-addr {
            white-space: pre-line;
            line-height: 1.3;
        }
        .via-block {
            float: left;
            width: 48%;
            text-align: left;
            padding-left: 20px;
            box-sizing: border-box;
            font-size: 10.5pt;
        }
        .page-counter {
            margin-top: 12px;
            font-size: 10pt;
            text-align: right;
            padding-right: 20px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Tabel Surat Muat */
        .sm-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 40px;
        }
        .sm-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 11pt;
        }
        .col-no { width: 35px; text-align: left; }
        .col-sm { width: 220px; font-weight: bold; }
        .col-tgl { width: 140px; text-align: left; }
        .col-qty { width: 120px; text-align: right; font-weight: bold; }
        .col-berat { width: 120px; text-align: right; font-weight: bold; }

        /* Tanda Tangan & Supir Bawah */
        .bottom-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11pt;
            padding: 0 40px;
        }
        .driver-text {
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .receiver-text {
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* Toolbar Kontrol Cetak */
        .print-toolbar {
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .btn-action {
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .btn-print { background: #0284c7; color: white; margin-left: 8px; }
        .btn-back { background: #475569; color: white; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="mode-template-kertas" id="print-container">

    <!-- TOOLBAR CONTROLLER (TIDAK TERCETAK DI KERTAS) -->
    <div class="print-toolbar no-print">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-weight: bold; font-size: 14px;">🖨️ Format Cetak SPPB</span>
            <span style="color: #94a3b8;">|</span>
            <span>No. PL: <strong>{{ $no_pl }}</strong></span>
            <span>No. SPPB: <strong>{{ $header->no_sppb ?? 'DRAFT' }}</strong></span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <!-- Pilihan Mode Cetak -->
            <label style="cursor: pointer; display: flex; align-items: center; gap: 6px; background: #1e293b; padding: 5px 10px; border-radius: 6px;">
                <input type="checkbox" id="toggle-grid" onchange="toggleTemplateMode(this.checked)">
                <span>Tampilkan Garis / Blanko HVS</span>
            </label>

            <button onclick="window.close(); window.history.back();" class="btn-action btn-back">Kembali</button>
            <button onclick="window.print()" class="btn-action btn-print">Cetak Sekarang</button>
        </div>
    </div>

    <!-- AREA DOKUMEN CETAK (PERSIS SEPERTI surat muat data saja.png) -->
    <div class="print-content" style="padding-top: 15px;">
        
        <!-- BARIS ATAS: NO SPPB (Kiri) & TANGGAL (Kanan) -->
        <div class="top-row">
            <div class="no-sppb-text">
                {{ $header->no_sppb ?? '0325/10/2025' }}
            </div>
            <div class="date-text">
                {{ $header->tgl_sppb ? $header->tgl_sppb->format('d F Y') : date('d F Y') }}
            </div>
        </div>

        <!-- BARIS KEDUA: CUSTOMER (Kiri) & VIA EKSPEDISI + HALAMAN (Kanan) -->
        <div class="mid-section clearfix">
            <!-- Customer -->
            <div class="customer-block">
                <div class="customer-name">{{ $header->customer_name ?? 'PT. Mensa Bina Sukses' }}</div>
                <div class="customer-addr">{{ $header->customer_address ?? "Pulo Kambing II/26 Kawasan Industri\nPulogadung Jakarta Timur - 13930" }}</div>
            </div>

            <!-- Via Ekspedisi & Halaman -->
            <div class="via-block">
                <div style="margin-bottom: 6px;">
                    Via &nbsp; {{ $header->ekspedisi ?? 'Tunggal Jaya Expressindo' }}
                </div>
                <div class="page-counter">
                    Halaman : &nbsp;&nbsp;&nbsp; 1 &nbsp;/&nbsp; 1
                </div>
            </div>
        </div>

        <!-- TABEL RINCIAN SURAT MUAT (HANYA YANG MEMILIKI NO_SM) -->
        <!-- Total Collie & Berat HANYA TERCETAK DI BARIS PERTAMA (PERSIS SEPERTI GAMBAR) -->
        <table class="sm-table">
            <tbody>
                @php 
                    $no = 1;
                    $totalCollieAll = number_format($header->jumlah ?: $items->sum('qty1'));
                    $totalBeratAll  = number_format($header->berat ?: $items->sum('qtyr'), 1);
                @endphp

                @foreach($smList as $sm)
                <tr>
                    <!-- Kolom 1: No Urut -->
                    <td class="col-no">{{ $no }}</td>

                    <!-- Kolom 2: Nomor Surat Muat -->
                    <td class="col-sm">{{ $sm['no_sm'] }}</td>

                    <!-- Kolom 3: Tanggal SM -->
                    <td class="col-tgl">{{ $sm['tgl_sm'] }}</td>

                    <!-- Kolom 4: Jumlah Collie (Total Akumulasi Semua Koli No. PL tercetak di baris ke-1 saja) -->
                    <td class="col-qty">
                        @if($no === 1)
                            {{ $totalCollieAll }}
                        @endif
                    </td>

                    <!-- Kolom 5: Berat Kg (Total Akumulasi Semua Koli No. PL tercetak di baris ke-1 saja) -->
                    <td class="col-berat">
                        @if($no === 1)
                            {{ $totalBeratAll }}
                        @endif
                    </td>
                </tr>
                @php $no++; @endphp
                @endforeach
            </tbody>
        </table>

        <!-- BARIS BAWAH: SUPIR / NO. POL (Kiri) & PENERIMA / PIC (Kanan) -->
        <div class="bottom-section">
            <div class="driver-text">
                {{ $header->supirnopol ?? 'Sunaryo/B 9742 FO/7213' }}
            </div>
            <div class="receiver-text">
                {{ $header->cat ? explode("\n", $header->cat)[0] : 'Asep Sumarna' }}
            </div>
        </div>

    </div>

    <script>
        function toggleTemplateMode(showGrid) {
            const container = document.getElementById('print-container');
            if (showGrid) {
                container.classList.remove('mode-template-kertas');
            } else {
                container.classList.add('mode-template-kertas');
            }
        }
    </script>
</body>
</html>
