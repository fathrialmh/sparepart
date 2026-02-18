@php
    $isPPN = $header->suratJalan->tipe_pajak === 'kena_pajak';
    $invoiceDate = \Carbon\Carbon::parse($header->tanggal)->locale('id')->translatedFormat('j F Y');
    $invoiceDateUpper = strtoupper($invoiceDate);
    $customerName = strtoupper($header->customer->nama);
    $customerAddress = $header->suratJalan->alamat_kirim ?: $header->customer->alamat;
    $poNumber = $header->suratJalan->no_po;

    $fmt = fn($v) => number_format((float) $v, 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $header->nomor }}</title>
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
            padding: 0.6cm 1cm;
        }

        .container {
            width: 100%;
            max-width: 21cm;
            margin: 0 auto;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding: 8px 10px 12px 10px;
            margin-bottom: 10px;
        }

        .header-table-nonppn {
            width: 100%;
            border-bottom: 2px solid #000;
            padding: 8px 0px 12px 0px;
            margin-bottom: 10px;
        }

        .logo-box {
            width: 84px;
            height: 84px;
            border: 0;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            padding: 4px;
        }

        .logo-box-nonppn {
            width: 112px;
            height: 49px;
            border: 0;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            margin-right: 8px;
            padding: 3px;
        }

        .company-info {
            font-size: 13px;
            line-height: 1.4;
            font-weight: normal;
        }

        .company-info strong {
            font-size: 28px;
            font-weight: 900;
        }

        .invoice-title-ppn {
            font-size: 39px;
            font-weight: 900;
            letter-spacing: 8px;
            line-height: 1;
            margin: 0;
        }

        .invoice-title-nonppn {
            text-align: left;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 7px;
            line-height: 1;
            margin: 0;
        }

        .company-brand {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 3px;
        }

        .company-tagline {
            font-size: 11px;
            margin-top: 2px;
        }

        .items-table {
            width: 100%;
            margin: 8px 0;
            font-size: 13px;
        }

        .items-table-ppn {
            border: none;
        }

        .items-table-nonppn {
            border: 2px solid #000;
        }

        .items-table th {
            border-left: 1.5px solid #000;
            border-right: 1.5px solid #000;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            background-color: #fff;
        }

        .items-table-ppn th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .items-table-nonppn th {
            border-top: none;
            border-bottom: 2px solid #000;
        }

        .items-table td {
            border-left: 1.5px solid #000;
            border-right: 1.5px solid #000;
            border-bottom: 1px solid #ddd;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        .items-table-ppn tbody tr:last-child td {
            border-bottom: 2px solid #000;
        }

        .items-table-nonppn tbody tr:last-child td {
            border-bottom: none;
        }

        .col-no { width: 35px; text-align: center; }
        .col-desc { width: auto; padding-left: 6px; }
        .col-qty { width: 70px; text-align: center; }
        .col-price { width: 100px; text-align: right; padding-right: 6px; }
        .col-amount { width: 100px; text-align: right; padding-right: 6px; }

        .bank-box {
            border: 1.5px solid #000;
            padding: 8px;
            width: 48%;
            float: left;
            margin-right: 4%;
            font-size: 13px;
        }

        .signature-box {
            width: 48%;
            float: right;
            text-align: center;
            padding: 8px;
            font-size: 13px;
        }

        .signature-space {
            height: 50px;
            margin: 10px 0;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">

        @if($isPPN)
        {{-- =============== PPN TEMPLATE =============== --}}
        <table class="header-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 94px; vertical-align: top;">
                                <div class="logo-box">
                                    @if(file_exists(public_path('assets/img/logo-aj.png')))
                                        <img src="{{ public_path('assets/img/logo-aj.png') }}" style="width: 84px; height: 84px; object-fit: contain;">
                                    @else
                                        <div style="width: 84px; height: 84px; background: #e5e7eb; text-align: center; line-height: 84px; font-size: 12px; color: #6b7280; border-radius: 4px;">AJ LOGO</div>
                                    @endif
                                </div>
                            </td>
                            <td style="vertical-align: top; padding-left: 10px;">
                                <div class="company-info">
                                    <strong>CV. ADAM JAYA</strong><br>
                                    Jl. Sadang, Rahayu, Kab. Bandung<br>
                                    Jawa Barat 40218<br>
                                    Telp: 085721322812 | Email: majter.ads@gmail.com
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: right; padding-right: 0;">
                    <div class="invoice-title-ppn">INVOICE</div>
                </td>
            </tr>
            <tr><td colspan="2" style="height: 8px;"></td></tr>
        </table>

        <div style="padding: 10px 12px; margin-bottom: 10px;">
            <table style="width: 100%; margin-bottom: 8px;">
                <tr>
                    <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                        <div style="font-size: 14px; line-height: 1.5; margin-bottom: 6px;">
                            <strong>BILL TO : {{ $customerName }}</strong>
                        </div>
                        <div style="font-size: 13px; line-height: 1.4; margin-top: 4px;">
                            {{ $customerAddress ?: '-' }}
                        </div>
                    </td>
                    <td style="width: 45%; vertical-align: top; padding-left: 10px;">
                        <div style="text-align: right;">
                            <table style="font-size: 13px; margin-bottom: 6px; margin-left: auto;">
                                <tr>
                                    <td colspan="2" style="padding: 2px 0; padding-bottom: 6px; text-align: right;">
                                        <strong>BANDUNG, {{ $invoiceDateUpper }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 90px; padding: 2px 0; text-align: left;">NO INVOICE</td>
                                    <td style="padding: 2px 0; text-align: left;">: {{ $header->nomor }}</td>
                                </tr>
                                @if($poNumber)
                                <tr>
                                    <td style="padding: 2px 0; text-align: left;">PO NO.</td>
                                    <td style="padding: 2px 0; text-align: left;">: {{ $poNumber }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 2px 0; text-align: left;">TOP</td>
                                    <td style="padding: 2px 0; text-align: left;">: 30 HARI</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items-table items-table-ppn">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-desc">DESCRIPTION</th>
                        <th class="col-qty">QTY</th>
                        <th class="col-price">HARGA SATUAN (Rp.)</th>
                        <th class="col-amount">AMOUNT (Rp.)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}.</td>
                        <td class="col-desc">{{ strtoupper($detail->barang->nama) }}</td>
                        <td class="col-qty">{{ number_format($detail->qty, 0, ',', '.') }} {{ strtoupper($detail->barang->satuan ?? 'PCS') }}</td>
                        <td class="col-price">Rp. {{ $fmt($detail->harga) }}</td>
                        <td class="col-amount">Rp. {{ $fmt($detail->subtotal) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 15px; font-style: italic;">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="text-align: right; padding-right: 0; margin-top: 8px;">
                <table style="font-size: 13px; margin-left: auto; display: inline-table;">
                    <tr>
                        <td style="padding: 2px 10px 2px 0; width: 150px; text-align: left;">SUB. TOTAL</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->subtotal) }}</td>
                    </tr>
                    @if($header->diskon_nilai > 0)
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">DISKON ({{ $header->diskon_persen }}%)</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->diskon_nilai) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">DPP NILAI LAIN</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. 0</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;"><strong>PPN</strong></td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->ppn) }}</td>
                    </tr>
                    @if($header->ongkos_kirim > 0)
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">ONGKOS KIRIM</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->ongkos_kirim) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 2px 10px 2px 0; border-bottom: 2px solid #000; text-align: left;"><strong>GRAND TOTAL</strong></td>
                        <td style="padding: 2px 0; border-bottom: 2px solid #000; text-align: left;">: <strong>Rp. {{ $fmt($header->total) }}</strong></td>
                    </tr>
                    @if($header->dp > 0)
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">DP</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->dp) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;"><strong>SISA</strong></td>
                        <td style="padding: 2px 0; text-align: left;">: <strong>Rp. {{ $fmt($header->sisa) }}</strong></td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="clearfix" style="margin-top: 5px; margin-bottom: 10px;">
                <div class="bank-box">
                    <strong>PEMBAYARAN HARAP DI TRANSFER KE :</strong><br>
                    <div style="margin-top: 6px;">
                        <table style="width: 100%; font-size: 11px;">
                            <tr>
                                <td style="width: 60px; vertical-align: top;">BCA</td>
                                <td style="vertical-align: top;">- 156 156 2275 A/N <strong>ADAM JAYA CV</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="signature-box">
                    <div>HORMAT KAMI</div>
                    <div class="signature-space"></div>
                </div>
            </div>

            @if($header->keterangan)
            <div style="clear: both; margin-top: 20px; padding: 8px; border: 1px solid #000; font-size: 12px;">
                <strong>Catatan:</strong> {{ $header->keterangan }}
            </div>
            @endif
        </div>

        @else
        {{-- =============== NON-PPN TEMPLATE =============== --}}
        <table class="header-table-nonppn">
            <tr>
                <td style="width: 50%; vertical-align: middle; padding-left: 0;">
                    <div class="invoice-title-nonppn">INVOICE</div>
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: right; padding-right: 0;">
                    <table style="margin-left: auto;">
                        <tr>
                            <td style="vertical-align: middle; padding-right: 8px;">
                                @if(file_exists(public_path('assets/img/logo-majter.png')))
                                    <img src="{{ public_path('assets/img/logo-majter.png') }}" style="width: 112px; height: 49px; object-fit: contain;">
                                @else
                                    <div style="width: 112px; height: 49px; background: #e5e7eb; text-align: center; line-height: 49px; font-size: 12px; color: #6b7280; border-radius: 4px;">MAJTER LOGO</div>
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

        <div style="padding: 10px 12px; margin-bottom: 10px;">
            <table style="width: 100%; margin-bottom: 8px;">
                <tr>
                    <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                        <div style="font-size: 14px; line-height: 1.5; margin-bottom: 6px;">
                            <strong>BILL TO : {{ $customerName }}</strong>
                        </div>
                        <div style="font-size: 13px; line-height: 1.4; margin-top: 4px;">
                            {{ $customerAddress ?: '-' }}
                        </div>
                    </td>
                    <td style="width: 45%; vertical-align: top; padding-left: 10px;">
                        <div style="text-align: right;">
                            <table style="font-size: 13px; margin-bottom: 6px; margin-left: auto;">
                                <tr>
                                    <td colspan="2" style="padding: 2px 0; padding-bottom: 6px; text-align: right;">
                                        <strong>BANDUNG, {{ $invoiceDateUpper }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 90px; padding: 2px 0; text-align: left;">NO INVOICE</td>
                                    <td style="padding: 2px 0; text-align: left;">: {{ $header->nomor }}</td>
                                </tr>
                                @if($poNumber)
                                <tr>
                                    <td style="padding: 2px 0; text-align: left;">PO NO.</td>
                                    <td style="padding: 2px 0; text-align: left;">: {{ $poNumber }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 2px 0; text-align: left;">TOP</td>
                                    <td style="padding: 2px 0; text-align: left;">: 30 HARI</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items-table items-table-nonppn">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-desc">DESCRIPTION</th>
                        <th class="col-qty">QTY</th>
                        <th class="col-price">HARGA SATUAN (Rp.)</th>
                        <th class="col-amount">AMOUNT (Rp.)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}.</td>
                        <td class="col-desc">{{ strtoupper($detail->barang->nama) }}</td>
                        <td class="col-qty">{{ number_format($detail->qty, 0, ',', '.') }} {{ strtoupper($detail->barang->satuan ?? 'PCS') }}</td>
                        <td class="col-price">Rp. {{ $fmt($detail->harga) }}</td>
                        <td class="col-amount">Rp. {{ $fmt($detail->subtotal) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 15px; font-style: italic;">Tidak ada item</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="text-align: right; padding-right: 0; margin-top: 8px;">
                <table style="font-size: 13px; margin-left: auto; display: inline-table;">
                    <tr>
                        <td style="padding: 2px 10px 2px 0; width: 150px; text-align: left;">SUB. TOTAL</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->subtotal) }}</td>
                    </tr>
                    @if($header->diskon_nilai > 0)
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">DISKON ({{ $header->diskon_persen }}%)</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->diskon_nilai) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">DPP NILAI LAIN</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. 0</td>
                    </tr>
                    @if($header->ongkos_kirim > 0)
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">ONGKOS KIRIM</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->ongkos_kirim) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 2px 10px 2px 0; border-bottom: 2px solid #000; text-align: left;"><strong>GRAND TOTAL</strong></td>
                        <td style="padding: 2px 0; border-bottom: 2px solid #000; text-align: left;">: <strong>Rp. {{ $fmt($header->total) }}</strong></td>
                    </tr>
                    @if($header->dp > 0)
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;">DP</td>
                        <td style="padding: 2px 0; text-align: left;">: Rp. {{ $fmt($header->dp) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 10px 2px 0; text-align: left;"><strong>SISA</strong></td>
                        <td style="padding: 2px 0; text-align: left;">: <strong>Rp. {{ $fmt($header->sisa) }}</strong></td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="clearfix" style="margin-top: 5px; margin-bottom: 10px;">
                <div class="bank-box">
                    <strong>PEMBAYARAN HARAP DI TRANSFER KE :</strong><br>
                    <div style="margin-top: 6px;">
                        <table style="width: 100%; font-size: 11px;">
                            <tr>
                                <td style="width: 60px; vertical-align: top;">BCA</td>
                                <td style="vertical-align: top;">- 139 0800 645 A/N <strong>DIO GIANI PUTRA</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="signature-box">
                    <div>HORMAT KAMI</div>
                    <div class="signature-space"></div>
                </div>
            </div>

            @if($header->keterangan)
            <div style="clear: both; margin-top: 20px; padding: 8px; border: 1px solid #000; font-size: 12px;">
                <strong>Catatan:</strong> {{ $header->keterangan }}
            </div>
            @endif
        </div>
        @endif

    </div>
</body>
</html>
