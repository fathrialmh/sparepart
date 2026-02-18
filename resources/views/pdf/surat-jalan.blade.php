@php
    $isPPN = $header->tipe_pajak === 'kena_pajak';
    $sjDate = \Carbon\Carbon::parse($header->tanggal)->locale('id')->translatedFormat('j F Y');
    $sjDateUpper = strtoupper($sjDate);
    $customerName = strtoupper($header->customer->nama);
    $shipTo = $header->alamat_kirim ?: $header->customer->alamat;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Jalan - {{ $header->nomor }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: white;
            margin: 0;
            padding: 1cm 1.2cm;
        }

        .container {
            width: 100%;
            max-width: 19cm;
            margin: 0 auto;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }

        /* ======== PPN Header ======== */
        .header-ppn {
            border-bottom: 1.5px solid #000;
            padding: 6px 10px;
        }

        .header-ppn .logo-box {
            width: 60px;
            height: 60px;
            border: 0;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            padding: 3px;
        }

        .header-ppn .company-info {
            font-size: 13px;
            line-height: 1.4;
            font-weight: normal;
        }

        .header-ppn .company-info strong {
            font-size: 22px;
            font-weight: 900;
        }

        .header-ppn .title {
            text-align: right;
            font-size: 28px;
            font-weight: 900;
            margin: 0;
            letter-spacing: 5px;
        }

        /* ======== Non-PPN Header ======== */
        .header-nonppn {
            border-bottom: 1.5px solid #000;
            padding: 6px 0px 6px 0px;
        }

        .header-nonppn .logo-box {
            width: 115px;
            height: 47px;
            border: 0;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            margin-right: 8px;
            padding: 2px;
        }

        .header-nonppn .title {
            text-align: left;
            font-size: 28px;
            font-weight: 900;
            margin: 0;
            letter-spacing: 5px;
        }

        .company-brand {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
        }

        .company-tagline {
            font-size: 10px;
            margin-top: 2px;
        }

        /* ======== Content ======== */
        .content {
            padding: 10px 12px;
        }

        .doc-info {
            font-size: 13px;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .doc-info table {
            width: 100%;
        }

        .doc-info td {
            padding: 1px 0;
            vertical-align: top;
        }

        /* ======== PPN Items Table (borderless style) ======== */
        .items-table-ppn {
            width: 100%;
            margin: 8px 0;
            font-size: 13px;
            border: none;
        }

        .items-table-ppn th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            border-left: none;
            border-right: none;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            background-color: #fff;
        }

        .items-table-ppn td {
            border: none;
            border-bottom: 1px solid #ddd;
            padding: 8px 6px;
            vertical-align: top;
            font-size: 13px;
        }

        .items-table-ppn tbody tr:last-child td {
            border-bottom: 2px solid #000;
        }

        /* ======== Non-PPN Items Table (bordered style) ======== */
        .items-table-nonppn {
            width: 100%;
            margin: 8px 0;
            font-size: 13px;
            border: 2px solid #000;
        }

        .items-table-nonppn th {
            border-top: none;
            border-bottom: 2px solid #000;
            border-left: 1.5px solid #000;
            border-right: 1.5px solid #000;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            background-color: #fff;
        }

        .items-table-nonppn td {
            border-left: 1.5px solid #000;
            border-right: 1.5px solid #000;
            border-bottom: 1px solid #ddd;
            padding: 8px 6px;
            vertical-align: top;
            font-size: 13px;
        }

        .items-table-nonppn tbody tr:last-child td {
            border-bottom: none;
        }

        /* ======== Shared column widths ======== */
        .col-no { width: 35px; text-align: center; }
        .col-item { width: auto; text-align: left; padding-left: 5px; }
        .col-qty { width: 100px; text-align: center; }
        .col-notes { width: 150px; text-align: center; }

        /* ======== Signatures ======== */
        .signature-section {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .signatures-table {
            width: 100%;
        }

        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }

        .signature-space {
            min-height: 60px;
            margin: 10px 0 5px 0;
        }

        /* ======== Notes ======== */
        .notes {
            font-size: 13px;
            margin-top: 5px;
            line-height: 1.4;
        }

        .notes strong {
            font-weight: bold;
            font-size: 14px;
        }

        .header-table {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">

        @if($isPPN)
        {{-- =============== PPN TEMPLATE =============== --}}
        <div class="header-ppn">
            <table class="header-table">
                <tr>
                    <td style="width: 70px; vertical-align: middle;">
                        <div class="logo-box">
                            @if(file_exists(public_path('assets/img/logo-aj.png')))
                                <img src="{{ public_path('assets/img/logo-aj.png') }}" style="width: 60px; height: 60px; object-fit: contain;">
                            @else
                                <div style="width: 60px; height: 60px; background: #e5e7eb; text-align: center; line-height: 60px; font-size: 10px; color: #6b7280; border-radius: 4px;">AJ LOGO</div>
                            @endif
                        </div>
                    </td>
                    <td style="width: 48%; vertical-align: middle; padding-left: 8px;">
                        <div class="company-info">
                            <strong>CV. ADAM JAYA</strong><br>
                            Jl. Sadang, Rahayu, Kab. Bandung<br>
                            Jawa Barat 40218<br>
                            Telp: 085721322812 | Email: majter.ads@gmail.com
                        </div>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                        <div class="title">SURAT JALAN</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <div class="doc-info">
                <table>
                    <tr>
                        <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                            <div style="margin-bottom: 5px;">
                                <strong>BANDUNG, {{ $sjDateUpper }}</strong>
                            </div>
                            <table style="width: 100%; margin-bottom: 5px;">
                                <tr>
                                    <td style="width: 60px;">NOMOR</td>
                                    <td>: {{ $header->nomor }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 45%; vertical-align: top; padding-left: 10px;">
                            <table style="width: 100%; font-size: 13px;">
                                <tr>
                                    <td style="width: 90px; padding: 2px 0;">TO</td>
                                    <td style="padding: 2px 0;">: <strong>{{ $customerName }} (PPN)</strong></td>
                                </tr>
                            </table>
                            <div style="margin-top: 5px; font-size: 13px;">
                                <strong>SHIP TO :</strong> {{ $shipTo ?: '-' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="items-table-ppn">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-item">NAMA BARANG</th>
                        <th class="col-qty">BANYAKNYA</th>
                        <th class="col-notes">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}.</td>
                        <td class="col-item">{{ strtoupper($detail->barang->nama) }}</td>
                        <td class="col-qty">{{ number_format($detail->qty, 0, ',', '.') }} {{ strtoupper($detail->barang->satuan ?? 'PCS') }}</td>
                        <td class="col-notes"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 15px; font-style: italic;">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="signature-section">
                <table class="signatures-table">
                    <tr>
                        <td style="text-align: center;">
                            <div>Yang Menyerahkan</div>
                            <div class="signature-space"></div>
                        </td>
                        <td style="text-align: center;">
                            <div>Yang Menerima</div>
                            <div class="signature-space"></div>
                        </td>
                    </tr>
                </table>
            </div>

            @if($header->keterangan)
            <div class="notes">
                <strong>Catatan:</strong> {{ $header->keterangan }}
            </div>
            @endif
        </div>

        @else
        {{-- =============== NON-PPN TEMPLATE =============== --}}
        <div class="header-nonppn">
            <table class="header-table">
                <tr>
                    <td style="width: 50%; vertical-align: middle; padding-left: 0;">
                        <div class="title">SURAT JALAN</div>
                    </td>
                    <td style="width: 50%; vertical-align: middle; text-align: right; padding-right: 0;">
                        <table style="margin-left: auto;">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 8px;">
                                    @if(file_exists(public_path('assets/img/logo-majter.png')))
                                        <img src="{{ public_path('assets/img/logo-majter.png') }}" style="width: 115px; height: 47px; object-fit: contain;">
                                    @else
                                        <div style="width: 115px; height: 47px; background: #e5e7eb; text-align: center; line-height: 47px; font-size: 12px; color: #6b7280; border-radius: 4px;">MAJTER LOGO</div>
                                    @endif
                                </td>
                                <td style="vertical-align: middle; border-left: 1px solid #000; padding-left: 12px;">
                                    <div class="company-brand">AJT</div>
                                    <div class="company-tagline">BANDUNG - JAWA BARAT 40218</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <div class="doc-info">
                <table>
                    <tr>
                        <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                            <div style="margin-bottom: 5px;">
                                <strong>BANDUNG, {{ $sjDateUpper }}</strong>
                            </div>
                            <table style="width: 100%; margin-bottom: 5px;">
                                <tr>
                                    <td style="width: 60px;">NOMOR</td>
                                    <td>: {{ $header->nomor }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 45%; vertical-align: top; padding-left: 10px;">
                            <table style="width: 100%; font-size: 13px;">
                                <tr>
                                    <td style="width: 90px; padding: 2px 0;">TO</td>
                                    <td style="padding: 2px 0;">: <strong>{{ $customerName }}</strong></td>
                                </tr>
                            </table>
                            <div style="margin-top: 5px; font-size: 13px;">
                                <strong>SHIP TO :</strong> {{ $shipTo ?: '-' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="items-table-nonppn">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-item">NAMA BARANG</th>
                        <th class="col-qty">BANYAKNYA</th>
                        <th class="col-notes">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}.</td>
                        <td class="col-item">{{ strtoupper($detail->barang->nama) }}</td>
                        <td class="col-qty">{{ number_format($detail->qty, 0, ',', '.') }} {{ strtoupper($detail->barang->satuan ?? 'PCS') }}</td>
                        <td class="col-notes"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 15px; font-style: italic;">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="signature-section">
                <table class="signatures-table">
                    <tr>
                        <td style="text-align: center;">
                            <div>Yang Menyerahkan</div>
                            <div class="signature-space"></div>
                        </td>
                        <td style="text-align: center;">
                            <div>Yang Menerima</div>
                            <div class="signature-space"></div>
                        </td>
                    </tr>
                </table>
            </div>

            @if($header->keterangan)
            <div class="notes">
                <strong>Catatan:</strong> {{ $header->keterangan }}
            </div>
            @endif
        </div>
        @endif

    </div>
</body>
</html>
