{{-- <div class="table-responsive"> --}}
<table class="table rounded table-sm syarat-berkas"
       id="syarat-berkas-table">
    <thead>
        <tr class="d-none">
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
        onclick="tambahDataBerkas()"
        type="button">+ Tambah Syarat Berkas</button>
{{-- </div> --}}

@push('styles')
    <style>
        .syarat-berkas .row>* {
            padding: 0 !important;
        }

        .syarat-berkas label {
            font-size: 0.775rem !important;
        }

        .syarat-berkas input,
        .syarat-berkas select {
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
            $('#syarat-berkas-table').DataTable({
                lengthChange: false,
                searching: false,
                paging: false,
                info: false,
                // row reorder
                rowReorder: {
                    enable: true,
                }
            });

            $('#syarat-berkas-table tbody').on('click', '.delete-data', function() {
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
                        var table = $('#syarat-berkas-table').DataTable();
                        table.row($(this).parents('tr')).remove().draw();
                    }
                });
            });
        });

        function tambahDataBerkas(nama_berkas, is_required) {
            // get last counter
            var lastCounter = $('#syarat-berkas-table tbody tr:last-child .counter').text();
            // increment counter
            var newCounter = parseInt(lastCounter ? lastCounter : 0) + 1;
            var table = $('#syarat-berkas-table').DataTable();
            var row = table.row.add([
                '<td class="text-start w-5"><p class="d-none counter">' + newCounter +
                '</p><i class="isax isax-textalign-justifycenter fs-4"></i></td>',
                '<td class="text-start w-80"><div class="form-group mb-0 row align-items-center"><div class="col-2"><label class="col-form-label fw-bold text-sm" for="nama_berkas_' +
                newCounter +
                '">Nama Berkas</label></div><div class="col-9"><input class="form-control" id="nama_berkas_' +
                newCounter + '" name="syarat_berkas[' + newCounter +
                '][nama]" placeholder="Masukkan nama berkas" type="text" required></div></div></td>',
                '<td><div class="form-check form-switch"><input type="hidden" name="syarat_berkas[' + newCounter +
                '][is_required]" data-id="syarat_berkas_' + newCounter +
                '" value="0"><input class="form-check-input syarat-berkas-is-required-checkbox" data-id="' +
                newCounter +
                '" id="syarat_berkas_' + newCounter +
                '" type="checkbox"><label class="form-check-label" for="syarat_berkas_' + newCounter +
                '">Wajib</label></div></td>',
                '<td class="w-10"><i class="isax isax-trash fs-4 delete-data"></i></td>'
            ]).draw(false).node();
            $(row).addClass('bg-white');
            // add class and width every td
            $(row).find('td').eq(0).css('width', '5%').addClass('text-start');
            $(row).find('td').eq(1).css('width', '80%').addClass('text-start');
            $(row).find('td').eq(3).css('width', '10%');

            // set nama berkas
            if (nama_berkas) {
                $('#nama_berkas_' + newCounter).val(nama_berkas);
            }

            // set is required
            if (is_required == 1) {
                $('#syarat_berkas_' + newCounter).prop('checked', true);
                $('input[name="syarat_berkas[' + newCounter + '][is_required]"]').val(1);
            } else {
                $('#syarat_berkas_' + newCounter).prop('checked', false);
                $('input[name="syarat_berkas[' + newCounter + '][is_required]"]').val(0);
            }
        }

        // on change is required
        $(document).on('change', '.syarat-berkas-is-required-checkbox', function() {
            var id = $(this).data('id');
            if ($(this).is(':checked')) {
                $('input[name="syarat_berkas[' + id + '][is_required]"]').val(1);
            } else {
                $('input[name="syarat_berkas[' + id + '][is_required]"]').val(0);
            }
        });
    </script>
@endpush
