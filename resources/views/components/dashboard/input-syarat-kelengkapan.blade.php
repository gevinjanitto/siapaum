{{-- <div class="table-responsive"> --}}
<table class="table rounded table-sm syarat-kelengkapan"
       id="syarat-kelengkapan-table">
    <thead>
        <tr class="d-none">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<button class="btn btn-primary btn-sm"
        onclick="tambahDataKelengkapan()"
        type="button">+ Tambah Syarat Kelengkapan</button>
{{-- </div> --}}

@push('styles')
    <style>
        .syarat-kelengkapan .row>* {
            padding: 0 !important;
        }

        .syarat-kelengkapan label {
            font-size: 0.775rem !important;
        }

        .syarat-kelengkapan input,
        .syarat-kelengkapan select {
            width: 100% !important;
            font-size: 0.775rem !important;
        }

        .delete-data {
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#syarat-kelengkapan-table').DataTable({
                lengthChange: false,
                searching: false,
                paging: false,
                info: false,
                // row reorder
                rowReorder: {
                    enable: true,
                }
            });

            $('#syarat-kelengkapan-table tbody').on('click', '.delete-data', function() {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var table = $('#syarat-kelengkapan-table').DataTable();
                        table.row($(this).parents('tr')).remove().draw();
                    }
                });
            });
        });

        function tambahDataKelengkapan(nama_form, kode_isian, tipe_form) {
            if (tipe_form !== 'text' && tipe_form !== 'date') {
                tipe_form = '';
            }
            // get last counter
            var lastCounter = $('#syarat-kelengkapan-table tbody tr:last-child .counter').text();
            // increment counter
            var newCounter = parseInt(lastCounter ? lastCounter : 0) + 1;
            var table = $('#syarat-kelengkapan-table').DataTable();
            var row = table.row.add([
                '<p class="d-none counter">' + newCounter +
                '</p><i class="isax isax-textalign-justifycenter fs-4"></i>',
                '<div class="form-group mb-0 row align-items-center gap-2"><div class="col-auto"><label class="col-form-label fw-bold text-sm" for="nama_kelengkapan_' +
                newCounter +
                '">Nama Form</label></div><div class="col-8"><input class="form-control" id="nama_kelengkapan_' +
                newCounter +
                '" name="syarat_kelengkapan['+newCounter+'][nama]" placeholder="Masukkan nama form" type="text" required></div></div>',
                '<div class="form-group mb-0 row align-items-center gap-2"><div class="col-auto"><label class="col-form-label fw-bold text-sm" for="kode_isian_kelengkapan_' +
                newCounter +
                '">Kode Isian</label></div><div class="col-8"><input class="form-control" id="kode_isian_kelengkapan_' +
                newCounter +
                '" name="syarat_kelengkapan['+newCounter+'][kode_isian]" placeholder="contoh NO_SK" type="text" required></div></div>',
                '<div class="form-group mb-0 row align-items-center gap-2"><div class="col-auto"><label class="col-form-label fw-bold text-sm" for="tipe_kelengkapan_' +
                newCounter +
                '">Tipe Form</label></div><div class="col-8"><select class="form-select" id="tipe_kelengkapan_' +
                newCounter +
                '" name="syarat_kelengkapan['+newCounter+'][tipe_form]" required><option hidden value="">Pilih Tipe Form</option><option value="text">Text</option><option value="date">Date</option></select></div></div>',
                '<i class="isax isax-trash fs-4 delete-data"></i>'
            ]).draw(false).node();
            $(row).addClass('bg-white');
            $(row).find('td').eq(0).addClass('text-start').css('width', '5%');
            $(row).find('td').eq(1).addClass('text-center').css('width', '30%');
            $(row).find('td').eq(2).addClass('text-center').css('width', '30%');
            $(row).find('td').eq(3).addClass('text-center').css('width', '30%');

            // set value
            $('#nama_kelengkapan_' + newCounter).val(nama_form);
            $('#kode_isian_kelengkapan_' + newCounter).val(kode_isian);
            $('#tipe_kelengkapan_' + newCounter).val(tipe_form);
        }
    </script>
@endpush
