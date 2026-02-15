@extends('layouts.app')

@section('title', 'Invoice')
@section('page_title', 'Invoice')
@section('use_toast', true)

@section('content')

{{-- ========== DAFTAR INVOICE ========== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-primary"></i> Daftar Invoice
                <span class="badge bg-secondary rounded-pill">{{ $rows->count() }}</span>
            </span>
            @if($eligibleSuratJalan->isNotEmpty())
                <button type="button" class="btn btn-primary btn-sm" id="btnTambah"
                        data-bs-toggle="modal" data-bs-target="#modalInvoice">
                    <i class="bi bi-plus-lg"></i> Generate Invoice
                </button>
            @else
                <span class="text-muted small"><i class="bi bi-info-circle"></i> Tidak ada surat jalan yang siap di-invoice</span>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-receipt"></i>
                <h6>Belum ada invoice</h6>
                <p class="text-muted mb-2">Invoice dibuat dari surat jalan yang sudah berstatus Completed.</p>
                @if($eligibleSuratJalan->isNotEmpty())
                    <button type="button" class="btn btn-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#modalInvoice">
                        <i class="bi bi-plus-lg"></i> Generate Invoice
                    </button>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-start" style="min-width:140px">No Invoice</th>
                            <th class="text-center" style="width:120px">Tanggal</th>
                            <th class="text-start" style="min-width:120px">No SJ</th>
                            <th class="text-start" style="min-width:150px">Customer</th>
                            <th class="text-end" style="min-width:130px">Total</th>
                            <th class="text-end" style="min-width:130px">Sisa</th>
                            <th class="text-center" style="width:80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td><span class="fw-semibold">{{ $row->nomor }}</span></td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td><small class="text-muted">{{ $row->suratJalan->nomor }}</small></td>
                                <td>{{ $row->customer->nama }}</td>
                                <td class="text-end font-monospace">@rupiah($row->total)</td>
                                <td class="text-end font-monospace">
                                    @if($row->sisa <= 0)
                                        <span class="text-success fw-medium">Lunas</span>
                                    @else
                                        <span class="text-danger">@rupiah($row->sisa)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-icon btn-outline-dark btn-sm" target="_blank"
                                       href="{{ route('invoice.print', $row) }}" title="Cetak">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ========== MODAL GENERATE INVOICE ========== --}}
@if($eligibleSuratJalan->isNotEmpty())
<div class="modal fade" id="modalInvoice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i>
                    <span>Generate Invoice</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('invoice.store') }}" id="formInvoice">
                @csrf
                <div class="modal-body">
                    {{-- Surat Jalan & Tanggal --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-file-earmark-text"></i> Sumber Data</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Surat Jalan (Completed) <span class="text-danger">*</span></label>
                                <select name="surat_jalan_id" class="form-select" required>
                                    <option value="">— Pilih Surat Jalan —</option>
                                    @foreach($eligibleSuratJalan as $sj)
                                        <option value="{{ $sj->id }}">{{ $sj->nomor }} | {{ $sj->tanggal }} | {{ $sj->customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- Pengaturan Harga --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-calculator"></i> Pengaturan Harga</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Diskon (%)</label>
                                <div class="input-group">
                                    <input type="number" name="diskon_persen" class="form-control" placeholder="0" value="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ongkos Kirim</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="ongkos_kirim" class="form-control" placeholder="0" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">DP (Uang Muka)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="dp" class="form-control" placeholder="0" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pembayaran --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-credit-card"></i> Pembayaran</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Metode Pembayaran</label>
                                <input type="text" name="pembayaran" class="form-control" value="Transfer">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" value="BCA / Rekening perusahaan">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(function () {
    @if(session('success'))
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'{{ session("success") }}', showConfirmButton:false, timer:3000, timerProgressBar:true });
    @endif
    @if(session('error'))
        Swal.fire({ toast:true, position:'top-end', icon:'error', title:'{{ session("error") }}', showConfirmButton:false, timer:4000, timerProgressBar:true });
    @endif
});
</script>
@endpush
