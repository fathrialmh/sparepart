@extends('layouts.app')

@section('title', 'Pembelian Barang (PO)')
@section('page_title', 'Pembelian Barang (PO)')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Pembelian</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Purchase Order</span>
@endsection

@section('content')

@php
    $statusFilter = request('status');
    $filteredRows = $rows;
    if ($statusFilter) {
        $filteredRows = $rows->where('status', $statusFilter);
    }
    $totalCount     = $rows->count();
    $pendingCount   = $rows->where('status', 'pending')->count();
    $confirmedCount = $rows->where('status', 'confirmed')->count();
    $completedCount = $rows->where('status', 'completed')->count();
    $cancelledCount = $rows->where('status', 'cancelled')->count();
@endphp

{{-- Stats --}}
<div class="stats-grid-4" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total PO</span>
            <span class="stat-icon"><i class="bi bi-clipboard-data"></i></span>
        </div>
        <div class="stat-value">{{ $totalCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Total purchase orders</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Pending</span>
            <span class="stat-icon"><i class="bi bi-clock"></i></span>
        </div>
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Menunggu konfirmasi</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">Confirmed</span>
            <span class="stat-icon"><i class="bi bi-check2-circle"></i></span>
        </div>
        <div class="stat-value">{{ $confirmedCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Dikonfirmasi</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Completed</span>
            <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
        </div>
        <div class="stat-value">{{ $completedCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Selesai</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Purchase Order</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalPO">
            <i class="bi bi-plus-lg"></i> Tambah PO
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('purchase-order.index') }}" class="tab-button {{ !$statusFilter ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-clipboard-data"></i> Semua
            </a>
            <a href="{{ route('purchase-order.index', ['status' => 'pending']) }}" class="tab-button {{ $statusFilter === 'pending' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-clock"></i> Pending
                @if($pendingCount > 0) <span class="badge warning" style="font-size:0.7rem;">{{ $pendingCount }}</span> @endif
            </a>
            <a href="{{ route('purchase-order.index', ['status' => 'confirmed']) }}" class="tab-button {{ $statusFilter === 'confirmed' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-check2-circle"></i> Confirmed
                @if($confirmedCount > 0) <span class="badge info" style="font-size:0.7rem;">{{ $confirmedCount }}</span> @endif
            </a>
            <a href="{{ route('purchase-order.index', ['status' => 'completed']) }}" class="tab-button {{ $statusFilter === 'completed' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-box-seam"></i> Completed
                @if($completedCount > 0) <span class="badge success" style="font-size:0.7rem;">{{ $completedCount }}</span> @endif
            </a>
            <a href="{{ route('purchase-order.index', ['status' => 'cancelled']) }}" class="tab-button {{ $statusFilter === 'cancelled' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-x-circle"></i> Cancelled
                @if($cancelledCount > 0) <span class="badge danger" style="font-size:0.7rem;">{{ $cancelledCount }}</span> @endif
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($filteredRows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-clipboard-data" style="font-size:3rem;"></i>
                <h6>Belum ada purchase order</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Buat purchase order pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalPO">
                    <i class="bi bi-plus-lg"></i> Tambah PO
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredRows as $row)
                        <tr>
                            <td><strong>{{ $row->nomor }}</strong></td>
                            <td>{{ $row->tanggal->format('d M Y') }}</td>
                            <td>{{ $row->supplier->nama ?? '—' }}</td>
                            <td>
                                @if($row->tipe === 'impor')
                                    <span class="badge info"><i class="bi bi-globe"></i> Impor</span>
                                @else
                                    <span class="badge gray"><i class="bi bi-house"></i> Lokal</span>
                                @endif
                            </td>
                            <td>
                                @switch($row->status)
                                    @case('pending')
                                        <span class="badge warning"><i class="bi bi-clock"></i> Pending</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge info"><i class="bi bi-check2-circle"></i> Confirmed</span>
                                        @break
                                    @case('completed')
                                        <span class="badge success"><i class="bi bi-box-seam"></i> Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge danger"><i class="bi bi-x-circle"></i> Cancelled</span>
                                        @break
                                @endswitch
                            </td>
                            <td style="font-family:monospace;">@rupiah($row->total)</td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end; flex-wrap:wrap;">
                                    {{-- Status update buttons --}}
                                    @if($row->status !== 'completed' && $row->status !== 'cancelled')
                                        <form method="post" action="{{ route('purchase-order.status', $row) }}" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width:auto; display:inline-block; font-size:0.75rem; padding:0.15rem 1.5rem 0.15rem 0.4rem;">
                                                <option value="" disabled selected>Status</option>
                                                @if($row->status !== 'pending')
                                                    <option value="pending">Pending</option>
                                                @endif
                                                @if($row->status !== 'confirmed')
                                                    <option value="confirmed">Confirmed</option>
                                                @endif
                                                <option value="completed">Completed</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </form>
                                    @endif

                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                                            data-supplier_id="{{ $row->supplier_id }}"
                                            data-tipe="{{ $row->tipe }}"
                                            data-tipe_pajak="{{ $row->tipe_pajak }}"
                                            data-expected_date="{{ $row->expected_date ? $row->expected_date->format('Y-m-d') : '' }}"
                                            data-catatan="{{ $row->catatan }}"
                                            data-status="{{ $row->status }}"
                                            data-details='@json($row->details->map(fn($d) => ["barang_id" => $d->barang_id, "qty" => $d->qty, "harga" => $d->harga]))'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('purchase-order.destroy', $row) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete btn-delete" title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus PO {{ $row->nomor }}?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ========== MODAL TAMBAH / EDIT PO ========== --}}
<div class="modal fade" id="modalPO" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-cart-plus text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Tambah Purchase Order</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPO" method="post" action="{{ route('purchase-order.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="po_id" id="poId" value="">
                <div class="modal-body">
                    {{-- Informasi PO --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi PO</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="inputTanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="inputSupplier" class="form-select" required>
                                    <option value="">— Pilih Supplier —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe <span class="text-danger">*</span></label>
                                <select name="tipe" id="inputTipe" class="form-select" required>
                                    <option value="lokal">Lokal</option>
                                    <option value="impor">Impor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe Pajak <span class="text-danger">*</span></label>
                                <select name="tipe_pajak" id="inputTipePajak" class="form-select" required>
                                    <option value="ppn">PPN</option>
                                    <option value="non-ppn">Non-PPN</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-3">
                                <label class="form-label">Expected Date</label>
                                <input type="date" name="expected_date" id="inputExpectedDate" class="form-control">
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="catatan" id="inputCatatan" class="form-control" placeholder="Catatan tambahan...">
                            </div>
                        </div>
                    </div>

                    {{-- Detail Barang --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-list-check"></i> Items PO</span>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="po-add-row">
                                <i class="bi bi-plus-lg"></i> Tambah Baris
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="po-detail-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:250px">Barang</th>
                                        <th style="width:100px" class="text-center">Qty</th>
                                        <th style="width:170px" class="text-end">Harga Satuan</th>
                                        <th style="width:170px" class="text-end">Subtotal</th>
                                        <th style="width:60px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="po-items-body">
                                    <tr>
                                        <td>
                                            <select name="barang_id[]" class="form-select form-select-sm po-barang" required>
                                                <option value="">— Pilih Barang —</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_beli }}">{{ $barang->kode }} - {{ $barang->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="qty[]" class="form-control form-control-sm text-center po-qty" min="1" value="1" required></td>
                                        <td><input type="number" name="harga[]" class="form-control form-control-sm text-end po-harga" min="0" value="0" required></td>
                                        <td><input type="text" class="form-control form-control-sm text-end po-subtotal" readonly></td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm po-remove-row" type="button" title="Hapus">
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
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="bi bi-save" id="btnSimpanIcon"></i>
                        <span id="btnSimpanText">Simpan</span>
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
    const modal      = document.getElementById('modalPO');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formPO');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("purchase-order.store") }}';
    const updateUrl  = '{{ route("purchase-order.update", ":id") }}';
    const tbody      = document.getElementById('po-items-body');

    const barangOptions = `{!! collect($barangs)->map(fn($b) => '<option value="'.$b->id.'" data-harga="'.$b->harga_beli.'">'.$b->kode.' - '.$b->nama.'</option>')->implode('') !!}`;

    function formatRupiah(n) {
        return new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 }).format(n);
    }

    function recalcRow(tr) {
        const qty   = Number(tr.querySelector('.po-qty').value) || 0;
        const harga = Number(tr.querySelector('.po-harga').value) || 0;
        tr.querySelector('.po-subtotal').value = formatRupiah(qty * harga);
    }

    function bindRows() {
        document.querySelectorAll('.po-barang').forEach(el => {
            el.onchange = function () {
                const tr    = this.closest('tr');
                const opt   = this.options[this.selectedIndex];
                const harga = Number(opt?.dataset.harga || 0);
                tr.querySelector('.po-harga').value = harga;
                recalcRow(tr);
            };
        });
        document.querySelectorAll('.po-qty, .po-harga').forEach(el => {
            el.oninput = function () { recalcRow(this.closest('tr')); };
        });
        document.querySelectorAll('.po-remove-row').forEach(btn => {
            btn.onclick = function () {
                if (tbody.querySelectorAll('tr').length > 1) this.closest('tr').remove();
            };
        });
        tbody.querySelectorAll('tr').forEach(recalcRow);
    }

    document.getElementById('po-add-row').addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="barang_id[]" class="form-select form-select-sm po-barang" required><option value="">— Pilih Barang —</option>${barangOptions}</select></td>
            <td><input type="number" name="qty[]" class="form-control form-control-sm text-center po-qty" min="1" value="1" required></td>
            <td><input type="number" name="harga[]" class="form-control form-control-sm text-end po-harga" min="0" value="0" required></td>
            <td><input type="text" class="form-control form-control-sm text-end po-subtotal" readonly></td>
            <td class="text-center"><button class="btn btn-outline-danger btn-sm po-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
        tbody.appendChild(tr);
        bindRows();
    });

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        document.getElementById('poId').value = '';
        $('#modalIcon').attr('class', 'bi bi-cart-plus text-primary');
        $('#modalTitleText').text('Tambah Purchase Order');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        $('#inputTanggal').val('{{ date("Y-m-d") }}');
        $('#inputSupplier').val('');
        $('#inputTipe').val('lokal');
        $('#inputTipePajak').val('ppn');
        $('#inputExpectedDate').val('');
        $('#inputCatatan').val('');
        tbody.innerHTML = `
            <tr>
                <td><select name="barang_id[]" class="form-select form-select-sm po-barang" required><option value="">— Pilih Barang —</option>${barangOptions}</select></td>
                <td><input type="number" name="qty[]" class="form-control form-control-sm text-center po-qty" min="1" value="1" required></td>
                <td><input type="number" name="harga[]" class="form-control form-control-sm text-end po-harga" min="0" value="0" required></td>
                <td><input type="text" class="form-control form-control-sm text-end po-subtotal" readonly></td>
                <td class="text-center"><button class="btn btn-outline-danger btn-sm po-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>
            </tr>`;
        bindRows();
    }

    function setEditMode(data) {
        form.action = updateUrl.replace(':id', data.id);
        formMethod.value = 'PUT';
        document.getElementById('poId').value = data.id;
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Purchase Order');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputTanggal').val(data.tanggal);
        $('#inputSupplier').val(data.supplier_id);
        $('#inputTipe').val(data.tipe);
        $('#inputTipePajak').val(data.tipe_pajak);
        $('#inputExpectedDate').val(data.expected_date || '');
        $('#inputCatatan').val(data.catatan || '');

        tbody.innerHTML = '';
        const details = typeof data.details === 'string' ? JSON.parse(data.details) : data.details;
        if (details && details.length > 0) {
            details.forEach(function (item) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><select name="barang_id[]" class="form-select form-select-sm po-barang" required><option value="">— Pilih Barang —</option>${barangOptions}</select></td>
                    <td><input type="number" name="qty[]" class="form-control form-control-sm text-center po-qty" min="1" value="${item.qty}" required></td>
                    <td><input type="number" name="harga[]" class="form-control form-control-sm text-end po-harga" min="0" value="${item.harga}" required></td>
                    <td><input type="text" class="form-control form-control-sm text-end po-subtotal" readonly></td>
                    <td class="text-center"><button class="btn btn-outline-danger btn-sm po-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
                tbody.appendChild(tr);
                tr.querySelector('.po-barang').value = item.barang_id;
            });
        } else {
            document.getElementById('po-add-row').click();
        }
        bindRows();
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const btn = $(this);
        setEditMode({
            id:            btn.data('id'),
            tanggal:       btn.data('tanggal'),
            supplier_id:   btn.data('supplier_id'),
            tipe:          btn.data('tipe'),
            tipe_pajak:    btn.data('tipe_pajak'),
            expected_date: btn.data('expected_date'),
            catatan:       btn.data('catatan'),
            status:        btn.data('status'),
            details:       btn.data('details'),
        });
        bsModal.show();
    });

    bindRows();

    @if($errors->any()) bsModal.show(); @endif
});
</script>
@endpush
