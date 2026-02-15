<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        .no-border td { border: none; padding: 2px 0; }
    </style>
</head>
<body>
<h2 style="text-align:center;margin:0;">INVOICE</h2>
<table class="no-border">
    <tr><td width="20%">No Invoice</td><td>: {{ $header->nomor }}</td></tr>
    <tr><td>Tanggal</td><td>: {{ $header->tanggal }}</td></tr>
    <tr><td>No Surat Jalan</td><td>: {{ $header->suratJalan->nomor }}</td></tr>
    <tr><td>Customer</td><td>: {{ $header->customer->nama }}</td></tr>
    <tr><td>Alamat</td><td>: {{ $header->customer->alamat }}</td></tr>
</table>
<br>
<table>
    <thead><tr><th>No</th><th>Barang</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
    <tbody>
    @foreach($details as $detail)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $detail->barang->kode }} - {{ $detail->barang->nama }}</td>
            <td>{{ $detail->qty }}</td>
            <td>{{ \App\Helpers\NumberGenerator::rupiah((float) $detail->harga) }}</td>
            <td>{{ \App\Helpers\NumberGenerator::rupiah((float) $detail->subtotal) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<br>
<table>
    <tr><td width="70%">Subtotal</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->subtotal) }}</td></tr>
    <tr><td>Diskon</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->diskon_nilai) }}</td></tr>
    <tr><td>PPN</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->ppn) }}</td></tr>
    <tr><td>Ongkos Kirim</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->ongkos_kirim) }}</td></tr>
    <tr><td>Total</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->total) }}</td></tr>
    <tr><td>DP</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->dp) }}</td></tr>
    <tr><td>Sisa</td><td>{{ \App\Helpers\NumberGenerator::rupiah((float) $header->sisa) }}</td></tr>
</table>
</body>
</html>
