@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Kelas</h1>
    <!-- Form Tambah Kelas -->
    {{-- <form id="addKelasForm" class="mt-4">
        <div class="row g-3">
            <div class="col-md-8">
                <input type="text" id="namaKelas" name="namaKelas" class="form-control" placeholder="Nama Kelas" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Tambah Kelas</button>
            </div>
        </div>
    </form> --}}

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
                @foreach ($kelas as $x)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$x->nama}}</td>
                        <td> <!-- Kolom tombol aksi -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                data-id="{{ $x->id }}" data-nama="{{ $x->nama }}">
                                Edit
                            </button>

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
                    <form id="editForm" action="{{ route('kelas.edit', 0) }}" method="POST">
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
<!-- Bootstrap JS (Bundle) -->
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    // Inisialisasi DataTable dengan pengaturan bahasa Indonesia
    $(document).ready(function() {
        $('#kelasTable').DataTable({
            paging: false,  // Enable pagination
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

    // Mengatur modal dengan data dari tombol edit
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Tombol yang diklik
        var kelasId = button.data('id');
        var kelasNama = button.data('nama');
        
        // Update nilai form dengan data yang sesuai
        var modal = $(this);
        modal.find('#kelasId').val(kelasId);
        modal.find('#kelasNama').val(kelasNama);
        
        // Ubah action form ke URL yang sesuai untuk update
        var actionUrl = "{{ route('kelas.edit', ':id') }}"; // URL edit
        actionUrl = actionUrl.replace(':id', kelasId);
        modal.find('#editForm').attr('action', actionUrl);
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault(); // Mencegah form submit secara normal

        var form = $(this);
        var actionUrl = form.attr('action');
        var formData = form.serialize(); // Serialize data form menjadi string

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            success: function (response) {
                // Tampilkan pesan sukses atau error menggunakan SweetAlert
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
                        // Temukan row yang sesuai dan update
                        $('#kelasTable tbody tr').each(function() {
                            if ($(this).find('td').first().text() == response.kelas_id) {
                                $(this).replaceWith(updatedRow);
                            }
                        });
                        // $('#editModal').modal('hide'); 
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

    // Menggunakan SweetAlert untuk konfirmasi hapus
    $('.delete-btn').on('click', function () {
        var kelasId = $(this).data('id');
        var form = $(this).closest('form'); // Mengambil form terdekat (form hapus)

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
                // Jika konfirmasi, submit form
                form.submit();
            }
        });
    });
</script>
@endsection
