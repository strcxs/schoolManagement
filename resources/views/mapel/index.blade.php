@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Kelas</h1>

    <!-- Tabel Daftar Kelas dengan DataTables -->
    <div class="table-responsive">
        <table id="kelasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="kelasList">
                @foreach ($mapel as $x)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$x->nama}}</td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                data-id="{{ $x->id }}" data-nama="{{ $x->nama }}">
                                Edit
                            </button>

                            <!-- Tombol Hapus -->
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $x->id }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Kelas</h5>
                    <button id="close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('mapel.edit', 0) }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group m-2">
                            <label for="kelasNama">Nama Kelas</label>
                            <input type="text" class="form-control" id="kelasNama" name="nama" required>
                        </div>
                        <input type="hidden" id="kelasId" name="id">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#kelasTable').DataTable({
            paging: false,
            searching: false,
            "language": {
                "sProcessing":   "Sedang memproses...",
                "sLengthMenu":   "Tampilkan _MENU_ entri",
                "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "sSearch":       "Cari:",
                "oPaginate": {
                    "sFirst":    "Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext":     "Berikutnya",
                    "sLast":     "Terakhir"
                }
            }
        });
    });

    // Setup modal data from the button click
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var kelasId = button.data('id');
        var kelasNama = button.data('nama');
        
        var modal = $(this);
        modal.find('#kelasId').val(kelasId);
        modal.find('#kelasNama').val(kelasNama);
        
        var actionUrl = "{{ route('mapel.edit', ':id') }}";
        actionUrl = actionUrl.replace(':id', kelasId);
        modal.find('#editForm').attr('action', actionUrl);
    });

    // Submit edit form using Ajax
    $('#editForm').on('submit', function (e) {
        e.preventDefault(); // Prevent normal form submission

        var form = $(this);
        var actionUrl = form.attr('action');
        var formData = form.serialize(); 

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data kelas berhasil diperbarui!',
                        showConfirmButton: true,
                    }).then((result) => {
                        var updatedRow = `<tr>
                            <td>${response.kelas_id}</td>
                            <td>${response.kelas_nama}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="${response.kelas_id}" data-nama="${response.kelas_nama}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.kelas_id}">
                                    Hapus
                                </button>
                            </td>
                        </tr>`;
                        $('#kelasTable tbody tr').each(function() {
                            if ($(this).find('td').first().text() == response.kelas_id) {
                                $(this).replaceWith(updatedRow);
                            }
                        });
                        $('#close').click(); 
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan!',
                        text: response.message
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan!',
                    text: 'Silakan coba lagi nanti.'
                });
            }
        });
    });

    // SweetAlert for delete confirmation
    $('.delete-btn').on('click', function () {
        var kelasId = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak dapat mengembalikan data ini setelah dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform delete operation (you need to send a delete request here)
                // Example of a delete AJAX request (or a form submit for delete):
                $.ajax({
                    url: `/kelas/${kelasId}`,  // Adjust the URL to your route
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status == 200) {
                            Swal.fire(
                                'Dihapus!',
                                'Data kelas telah dihapus.',
                                'success'
                            );
                            $(`button[data-id="${kelasId}"]`).closest('tr').remove();
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
