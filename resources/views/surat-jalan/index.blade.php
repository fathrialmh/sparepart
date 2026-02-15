@extends('layouts.app')

@section('title', 'Surat Jalan')
@section('page_title', 'Surat Jalan')
@section('use_toast', true)

@section('content')

{{-- ========== DAFTAR SURAT JALAN ========== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-truck text-primary"></i> Daftar Surat Jalan
                <span class="badge bg-secondary rounded-pill">{{ $rows->count() }}</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambah"
                    data-bs-toggle="modal" data-bs-target="#modalSuratJalan">
                <i class="bi bi-plus-lg"></i> Buat Surat Jalan
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-truck"></i>
                <h6>Belum ada surat jalan</h6>
                <p class="text-muted mb-2">Buat surat jalan pertama Anda.</p>
                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalSuratJalan">
                    <i class="bi bi-plus-lg"></i> Buat Surat Jalan
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-start" style="min-width:140px">Nomor</th>
                            <th class="text-center" style="width:120px">Tanggal</th>
                            <th class="text-start" style="min-width:160px">Customer</th>
                            <th class="text-center" style="width:130px">Pajak</th>
                            <th class="text-center" style="width:120px">Status</th>
                            <th class="text-center" style="min-width:220px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td><span class="fw-semibold">{{ $row->nomor }}</span></td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td>{{ $row->customer->nama }}</td>
                                <td class="text-center">
                                    @if($row->tipe_pajak === 'kena_pajak')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">PPN 11%</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Non-PPN</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row->status === 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                    @elseif($row->status === 'in_progress')
                                        <span class="badge bg-info bg-opacity-10 text-info">In Progress</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success">Completed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        {{-- Status buttons --}}
                                        @if($row->status !== 'pending')
                                        <form method="post" action="{{ route('surat-jalan.status', $row) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="pending">
                                            <button class="btn btn-outline-warning btn-sm" title="Set Pending" style="font-size:.75rem;">Pending</button>
                                        </form>
                                        @endif
                                        @if($row->status !== 'in_progress')
                                        <form method="post" action="{{ route('surat-jalan.status', $row) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button class="btn btn-outline-info btn-sm" title="Set In Progress" style="font-size:.75rem;">Progress</button>
                                        </form>
                                        @endif
                                        @if($row->status !== 'completed')
                                        <form method="post" action="{{ route('surat-jalan.status', $row) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button class="btn btn-outline-success btn-sm" title="Set Completed" style="font-size:.75rem;">Complete</button>
                                        </form>
                                        @endif
                                        <a class="btn btn-icon btn-outline-dark btn-sm" target="_blank"
                                           href="{{ route('surat-jalan.print', $row) }}" title="Cetak">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ========== MODAL BUAT SURAT JALAN ========== --}}
<div class="modal fade" id="modalSuratJalan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-truck text-primary"></i>
                    <span>Buat Surat Jalan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('surat-jalan.store') }}" id="formSuratJalan">
                @csrf
                <div class="modal-body">
                    {{-- Info Pengiriman --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Pengiriman</div>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-select" id="sj-customer" required>
                                    <option value="">— Pilih Customer —</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-alamat="{{ $customer->alamat }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Alamat Kirim</label>
                                <input type="text" name="alamat_kirim" id="sj-alamat-kirim" class="form-control" placeholder="Alamat tujuan pengiriman">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No PO</label>
                                <input type="text" name="no_po" class="form-control" placeholder="Nomor Purchase Order">
                            </div>
                        </div>
                    </div>

                    {{-- Detail Kendaraan & Pembayaran --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-credit-card"></i> Kendaraan & Pembayaran</div>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">No Polisi</label>
                                <input type="text" name="no_polisi" class="form-control" placeholder="B 1234 XX">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sopir</label>
                                <input type="text" name="sopir" class="form-control" placeholder="Nama sopir">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Pembayaran</label>
                                <input type="text" name="pembayaran" class="form-control" value="C.O.D">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe Pajak</label>
                                <select name="tipe_pajak" class="form-select">
                                    <option value="tidak_kena_pajak">Tidak Kena Pajak</option>
                                    <option value="kena_pajak">Kena Pajak (PPN 11%)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Catatan tambahan">
                            </div>
                        </div>
                    </div>

                    {{-- Detail barang --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-list-check"></i> Detail Barang</span>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="sj-add-row">
                                <i class="bi bi-plus-lg"></i> Tambah Baris
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="sj-detail-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:250px">Barang</th>
                                        <th style="width:80px" class="text-center">Stok</th>
                                        <th style="width:90px" class="text-center">Qty</th>
                                        <th style="width:150px" class="text-end">Harga</th>
                                        <th style="width:60px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="barang_id[]" class="form-select form-select-sm sj-barang" required>
                                                <option value="">— Pilih Barang —</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}" data-harga="{{ $barang->harga_jual }}">{{ $barang->kode }} - {{ $barang->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control form-control-sm text-center sj-stok" readonly></td>
                                        <td><input type="number" name="qty[]" class="form-control form-control-sm text-center sj-qty" min="1" value="1" required></td>
                                        <td><input type="number" name="harga[]" class="form-control form-control-sm text-end sj-harga" min="0" value="0" readonly required></td>
                                        <td class="text-center">
                                            <button class="btn btn-icon btn-outline-danger btn-sm sj-remove-row" type="button" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Surat Jalan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    // Toast
    @if(session('success'))
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'{{ session("success") }}', showConfirmButton:false, timer:3000, timerProgressBar:true });
    @endif
    @if(session('error'))
        Swal.fire({ toast:true, position:'top-end', icon:'error', title:'{{ session("error") }}', showConfirmButton:false, timer:4000, timerProgressBar:true });
    @endif

    // Auto-fill alamat from customer
    const customerSelect = document.getElementById('sj-customer');
    const alamatInput = document.getElementById('sj-alamat-kirim');
    customerSelect.addEventListener('change', () => {
        const opt = customerSelect.options[customerSelect.selectedIndex];
        alamatInput.value = opt ? (opt.dataset.alamat || '') : '';
    });

    // Detail rows
    const tbody = document.querySelector('#sj-detail-table tbody');
    const optionHtml = `{!! collect($barangs)->map(fn($b) => '<option value="'.$b->id.'" data-stok="'.$b->stok.'" data-harga="'.$b->harga_jual.'">'.$b->kode.' - '.$b->nama.'</option>')->implode('') !!}`;

    const recalc = (tr) => {
        const sel = tr.querySelector('.sj-barang');
        const qty = Number(tr.querySelector('.sj-qty').value || 0);
        const opt = sel.options[sel.selectedIndex];
        const stok = Number(opt?.dataset.stok || 0);
        const harga = Number(opt?.dataset.harga || 0);
        tr.querySelector('.sj-stok').value = stok || '';
        tr.querySelector('.sj-harga').value = qty > 0 ? harga * qty : 0;
    };

    const bindRows = () => {
        document.querySelectorAll('.sj-barang').forEach(el => el.onchange = () => recalc(el.closest('tr')));
        document.querySelectorAll('.sj-qty').forEach(el => el.oninput = () => recalc(el.closest('tr')));
        document.querySelectorAll('.sj-remove-row').forEach(btn => {
            btn.onclick = function () { if (tbody.querySelectorAll('tr').length > 1) this.closest('tr').remove(); };
        });
        tbody.querySelectorAll('tr').forEach(recalc);
    };

    document.getElementById('sj-add-row').addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><select name="barang_id[]" class="form-select form-select-sm sj-barang" required><option value="">— Pilih Barang —</option>${optionHtml}</select></td><td><input type="text" class="form-control form-control-sm text-center sj-stok" readonly></td><td><input type="number" name="qty[]" class="form-control form-control-sm text-center sj-qty" min="1" value="1" required></td><td><input type="number" name="harga[]" class="form-control form-control-sm text-end sj-harga" min="0" value="0" readonly required></td><td class="text-center"><button class="btn btn-icon btn-outline-danger btn-sm sj-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
        tbody.appendChild(tr);
        bindRows();
    });

    bindRows();
});
</script>
@endpush
