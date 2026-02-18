@extends('layouts.app')

@section('title', 'Surat Penawaran')
@section('page_title', 'Surat Penawaran')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Surat Penawaran</span>
@endsection

@section('content')

@php
    $statusFilter = request('status');
    $filteredRows = $rows;
    if ($statusFilter) {
        $filteredRows = $rows->where('status', $statusFilter);
    }
    $totalCount    = $rows->count();
    $draftCount    = $rows->where('status', 'draft')->count();
    $sentCount     = $rows->where('status', 'sent')->count();
    $approvedCount = $rows->where('status', 'approved')->count();
    $rejectedCount = $rows->where('status', 'rejected')->count();
@endphp

{{-- Stats --}}
<div class="stats-grid-4" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Quotations</span>
            <span class="stat-icon"><i class="bi bi-clipboard-data"></i></span>
        </div>
        <div class="stat-value">{{ $totalCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Surat penawaran</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Draft</span>
            <span class="stat-icon"><i class="bi bi-pencil-square"></i></span>
        </div>
        <div class="stat-value">{{ $draftCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Belum dikirim</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">Sent</span>
            <span class="stat-icon"><i class="bi bi-send"></i></span>
        </div>
        <div class="stat-value">{{ $sentCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Sudah dikirim</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Approved</span>
            <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $approvedCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Disetujui</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Surat Penawaran</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalQuotation">
            <i class="bi bi-plus-lg"></i> Buat Penawaran
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('price-quotation.index') }}" class="tab-button {{ !$statusFilter ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-clipboard-data"></i> Semua
            </a>
            <a href="{{ route('price-quotation.index', ['status' => 'draft']) }}" class="tab-button {{ $statusFilter === 'draft' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-pencil-square"></i> Draft
                @if($draftCount > 0) <span class="badge warning" style="font-size:0.7rem;">{{ $draftCount }}</span> @endif
            </a>
            <a href="{{ route('price-quotation.index', ['status' => 'sent']) }}" class="tab-button {{ $statusFilter === 'sent' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-send"></i> Sent
                @if($sentCount > 0) <span class="badge info" style="font-size:0.7rem;">{{ $sentCount }}</span> @endif
            </a>
            <a href="{{ route('price-quotation.index', ['status' => 'approved']) }}" class="tab-button {{ $statusFilter === 'approved' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-check-circle"></i> Approved
                @if($approvedCount > 0) <span class="badge success" style="font-size:0.7rem;">{{ $approvedCount }}</span> @endif
            </a>
            <a href="{{ route('price-quotation.index', ['status' => 'rejected']) }}" class="tab-button {{ $statusFilter === 'rejected' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-x-circle"></i> Rejected
                @if($rejectedCount > 0) <span class="badge danger" style="font-size:0.7rem;">{{ $rejectedCount }}</span> @endif
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($filteredRows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-clipboard-data" style="font-size:3rem;"></i>
                <h6>Belum ada surat penawaran</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Buat surat penawaran pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalQuotation">
                    <i class="bi bi-plus-lg"></i> Buat Penawaran
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Tipe Pajak</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredRows as $row)
                        <tr>
                            <td><strong>{{ $row->nomor }}</strong></td>
                            <td>{{ $row->tanggal->format('d M Y') }}</td>
                            <td>{{ $row->customer->nama ?? '—' }}</td>
                            <td>
                                @if($row->tipe_pajak === 'ppn')
                                    <span class="badge info">PPN</span>
                                @else
                                    <span class="badge gray">Non-PPN</span>
                                @endif
                            </td>
                            <td style="font-family:monospace;">@rupiah($row->total)</td>
                            <td>
                                @switch($row->status)
                                    @case('draft')
                                        <span class="badge gray"><i class="bi bi-pencil-square"></i> Draft</span>
                                        @break
                                    @case('sent')
                                        <span class="badge warning"><i class="bi bi-send"></i> Sent</span>
                                        @break
                                    @case('approved')
                                        <span class="badge success"><i class="bi bi-check-circle"></i> Approved</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                                            data-customer_id="{{ $row->customer_id }}"
                                            data-tipe_pajak="{{ $row->tipe_pajak }}"
                                            data-catatan="{{ $row->catatan }}"
                                            data-status="{{ $row->status }}"
                                            data-details='@json($row->details->map(fn($d) => ["barang_id" => $d->barang_id, "qty" => $d->qty, "harga" => $d->harga]))'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('price-quotation.destroy', $row) }}" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete btn-delete" title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus penawaran {{ $row->nomor }}?')">
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

{{-- ========== MODAL TAMBAH / EDIT QUOTATION ========== --}}
<div class="modal fade" id="modalQuotation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-plus text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Buat Surat Penawaran</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formQuotation" method="post" action="{{ route('price-quotation.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="quotation_id" id="quotationId" value="">
                <div class="modal-body">
                    {{-- Informasi Penawaran --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Penawaran</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="inputTanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="inputCustomer" class="form-select" required>
                                    <option value="">— Pilih Customer —</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe Pajak <span class="text-danger">*</span></label>
                                <select name="tipe_pajak" id="inputTipePajak" class="form-select" required>
                                    <option value="ppn">PPN</option>
                                    <option value="non-ppn">Non-PPN</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" id="inputStatus" class="form-select">
                                    <option value="draft">Draft</option>
                                    <option value="sent">Sent</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Barang --}}
                    <div class="form-section">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-list-check"></i> Items Penawaran</span>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="pq-add-row">
                                <i class="bi bi-plus-lg"></i> Tambah Baris
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="pq-detail-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:250px">Barang</th>
                                        <th style="width:100px" class="text-center">Qty</th>
                                        <th style="width:170px" class="text-end">Harga Satuan</th>
                                        <th style="width:170px" class="text-end">Subtotal</th>
                                        <th style="width:60px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="pq-items-body">
                                    <tr>
                                        <td>
                                            <select name="barang_id[]" class="form-select form-select-sm pq-barang" required>
                                                <option value="">— Pilih Barang —</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}">{{ $barang->kode }} - {{ $barang->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="qty[]" class="form-control form-control-sm text-center pq-qty" min="1" value="1" required></td>
                                        <td><input type="number" name="harga[]" class="form-control form-control-sm text-end pq-harga" min="0" value="0" required></td>
                                        <td><input type="text" class="form-control form-control-sm text-end pq-subtotal" readonly></td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm pq-remove-row" type="button" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-chat-left-text"></i> Catatan</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <textarea name="catatan" id="inputCatatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                            </div>
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
    const modal      = document.getElementById('modalQuotation');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formQuotation');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("price-quotation.store") }}';
    const updateUrl  = '{{ route("price-quotation.update", ":id") }}';
    const tbody      = document.getElementById('pq-items-body');

    const barangOptions = `{!! collect($barangs)->map(fn($b) => '<option value="'.$b->id.'" data-harga="'.$b->harga_jual.'">'.$b->kode.' - '.$b->nama.'</option>')->implode('') !!}`;

    function formatRupiah(n) {
        return new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 }).format(n);
    }

    function recalcRow(tr) {
        const qty   = Number(tr.querySelector('.pq-qty').value) || 0;
        const harga = Number(tr.querySelector('.pq-harga').value) || 0;
        tr.querySelector('.pq-subtotal').value = formatRupiah(qty * harga);
    }

    function bindRows() {
        document.querySelectorAll('.pq-barang').forEach(el => {
            el.onchange = function () {
                const tr    = this.closest('tr');
                const opt   = this.options[this.selectedIndex];
                const harga = Number(opt?.dataset.harga || 0);
                tr.querySelector('.pq-harga').value = harga;
                recalcRow(tr);
            };
        });
        document.querySelectorAll('.pq-qty, .pq-harga').forEach(el => {
            el.oninput = function () { recalcRow(this.closest('tr')); };
        });
        document.querySelectorAll('.pq-remove-row').forEach(btn => {
            btn.onclick = function () {
                if (tbody.querySelectorAll('tr').length > 1) this.closest('tr').remove();
            };
        });
        tbody.querySelectorAll('tr').forEach(recalcRow);
    }

    document.getElementById('pq-add-row').addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="barang_id[]" class="form-select form-select-sm pq-barang" required><option value="">— Pilih Barang —</option>${barangOptions}</select></td>
            <td><input type="number" name="qty[]" class="form-control form-control-sm text-center pq-qty" min="1" value="1" required></td>
            <td><input type="number" name="harga[]" class="form-control form-control-sm text-end pq-harga" min="0" value="0" required></td>
            <td><input type="text" class="form-control form-control-sm text-end pq-subtotal" readonly></td>
            <td class="text-center"><button class="btn btn-outline-danger btn-sm pq-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
        tbody.appendChild(tr);
        bindRows();
    });

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        document.getElementById('quotationId').value = '';
        $('#modalIcon').attr('class', 'bi bi-clipboard-plus text-primary');
        $('#modalTitleText').text('Buat Surat Penawaran');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        $('#inputTanggal').val('{{ date("Y-m-d") }}');
        $('#inputCustomer').val('');
        $('#inputTipePajak').val('ppn');
        $('#inputStatus').val('draft');
        $('#inputCatatan').val('');
        tbody.innerHTML = `
            <tr>
                <td><select name="barang_id[]" class="form-select form-select-sm pq-barang" required><option value="">— Pilih Barang —</option>${barangOptions}</select></td>
                <td><input type="number" name="qty[]" class="form-control form-control-sm text-center pq-qty" min="1" value="1" required></td>
                <td><input type="number" name="harga[]" class="form-control form-control-sm text-end pq-harga" min="0" value="0" required></td>
                <td><input type="text" class="form-control form-control-sm text-end pq-subtotal" readonly></td>
                <td class="text-center"><button class="btn btn-outline-danger btn-sm pq-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>
            </tr>`;
        bindRows();
    }

    function setEditMode(data) {
        form.action = updateUrl.replace(':id', data.id);
        formMethod.value = 'PUT';
        document.getElementById('quotationId').value = data.id;
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Surat Penawaran');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputTanggal').val(data.tanggal);
        $('#inputCustomer').val(data.customer_id);
        $('#inputTipePajak').val(data.tipe_pajak);
        $('#inputStatus').val(data.status);
        $('#inputCatatan').val(data.catatan || '');

        tbody.innerHTML = '';
        const details = typeof data.details === 'string' ? JSON.parse(data.details) : data.details;
        if (details && details.length > 0) {
            details.forEach(function (item) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><select name="barang_id[]" class="form-select form-select-sm pq-barang" required><option value="">— Pilih Barang —</option>${barangOptions}</select></td>
                    <td><input type="number" name="qty[]" class="form-control form-control-sm text-center pq-qty" min="1" value="${item.qty}" required></td>
                    <td><input type="number" name="harga[]" class="form-control form-control-sm text-end pq-harga" min="0" value="${item.harga}" required></td>
                    <td><input type="text" class="form-control form-control-sm text-end pq-subtotal" readonly></td>
                    <td class="text-center"><button class="btn btn-outline-danger btn-sm pq-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
                tbody.appendChild(tr);
                tr.querySelector('.pq-barang').value = item.barang_id;
            });
        } else {
            document.getElementById('pq-add-row').click();
        }
        bindRows();
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const btn = $(this);
        setEditMode({
            id:          btn.data('id'),
            tanggal:     btn.data('tanggal'),
            customer_id: btn.data('customer_id'),
            tipe_pajak:  btn.data('tipe_pajak'),
            catatan:     btn.data('catatan'),
            status:      btn.data('status'),
            details:     btn.data('details'),
        });
        bsModal.show();
    });

    bindRows();

    @if($errors->any()) bsModal.show(); @endif
});
</script>
@endpush
