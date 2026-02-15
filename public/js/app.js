$(function () {
    // DataTables init
    $('.datatable').each(function () {
        const placeholder = $(this).data('search-placeholder') || 'Cari data...';
        $(this).DataTable({
            pageLength: 25,
            order: [],
            dom: '<"dtop d-flex flex-wrap justify-content-between align-items-center gap-2"lf>' +
                 't' +
                 '<"dbot d-flex flex-wrap justify-content-between align-items-center gap-2"ip>',
            language: {
                search: '',
                searchPlaceholder: placeholder,
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total)',
                zeroRecords: 'Data tidak ditemukan',
                paginate: {
                    first: '«',
                    last: '»',
                    next: '›',
                    previous: '‹'
                }
            }
        });
    });

    // Enhanced delete confirmation
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const row = $(this).closest('tr');
        const itemName = row.find('td:first .fw-semibold').text()
                      || row.find('td:first').text().trim();

        Swal.fire({
            title: 'Hapus data?',
            html: itemName
                ? 'Anda akan menghapus <strong>' + itemName + '</strong>.<br>Data yang dihapus tidak dapat dikembalikan.'
                : 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash3"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
