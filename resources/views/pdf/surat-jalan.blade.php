<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Surat Jalan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        .no-border td { border: none; padding: 2px 0; }
    </style>
</head>
<body>
<h2 style="text-align:center;margin:0;">SURAT JALAN</h2>
<table class="no-border">
    <tr><td width="20%">Nomor</td><td>: {{ $header->nomor }}</td></tr>
    <tr><td>Tanggal</td><td>: {{ $header->tanggal }}</td></tr>
    <tr><td>Customer</td><td>: {{ $header->customer->nama }}</td></tr>
    <tr><td>Alamat Kirim</td><td>: {{ $header->alamat_kirim ?: $header->customer->alamat }}</td></tr>
    <tr><td>Tipe Pajak</td><td>: {{ $header->tipe_pajak }}</td></tr>
    <tr><td>Status</td><td>: {{ $header->status }}</td></tr>
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
</body>
</html>
