{{-- Start of Selection --}}
@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Role</h1>
    <!-- Form Tambah Role -->
    <form id="addRoleForm" class="mt-4 mb-2">
        <div class="row g-3">
            <div class="col-md-8">
                <input type="text" id="namaRole" name="nama" class="form-control" placeholder="Nama Role" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Tambah Role</button>
            </div>
        </div>
    </form>
    <!-- Tabel Daftar Role dengan DataTables -->
    <div class="table-responsive">
        <table id="roleTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nama Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="roleList">
                @foreach ($role as $x)
                    <tr>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Role</h5>
                    <button id="close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('role.edit', 0) }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group m-2">
                            <label for="roleNama">Nama Role</label>
                            <input type="text" class="form-control" id="roleNama" name="nama" required>
                        </div>
                        <input type="hidden" id="roleId" name="id">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Inisialisasi DataTable dengan pengaturan bahasa Indonesia
    $(document).ready(function() {
        $('#roleTable').DataTable({
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
        var roleId = button.data('id');
        var roleNama = button.data('nama');
        
        // Update nilai form dengan data yang sesuai
        var modal = $(this);
        modal.find('#roleId').val(roleId);
        modal.find('#roleNama').val(roleNama);
        
        // Ubah action form ke URL yang sesuai untuk update
        var actionUrl = "{{ route('role.edit', ':id') }}"; // URL edit
        actionUrl = actionUrl.replace(':id', roleId);
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
                        title: 'Data role berhasil diperbarui!',
                        showConfirmButton: true,
                    }).then((result) => {
                        var updatedRow = `<tr>
                            <td>${response.data.id}</td>
                            <td>${response.data.nama}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="${response.data.id}" data-nama="${response.data.nama}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                    Hapus
                                </button>
                            </td>
                        </tr>`;
                        // Temukan row yang sesuai dan update
                        $('#roleTable tbody tr').each(function() {
                            if ($(this).find('td').first().text() == response.data.id) {
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

    // Menggunakan SweetAlert untuk konfirmasi hapus
    $(document).on('click', '.delete-btn', function () {
        var roleId = $(this).data('id');
        
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
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('role.delete') }}",
                    type: 'POST',
                    data: { id: roleId },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken  
                    },
                    success: function (response) {
                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data role berhasil dihapus!',
                                showConfirmButton: true,
                            }).then((result) => {
                                // Hapus row dari tabel
                                $('#roleTable tbody tr').each(function() {
                                    if ($(this).find('td').first().text() == roleId) {
                                        $(this).remove();
                                    }
                                });
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
            }
        });
    });

    // Menambahkan role baru dengan AJAX dan SweetAlert
    $('#addRoleForm').on('submit', function (e) {
        e.preventDefault(); // Mencegah form submit secara normal

        var form = $(this);
        var actionUrl = "{{ route('role.add') }}"; // URL untuk menambah role
        var formData = form.serialize(); // Serialize data form menjadi string
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken  // Menambahkan CSRF token ke header
            },
            success: function (response) {
                // Tampilkan pesan sukses atau error menggunakan SweetAlert
                if (response.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Role berhasil ditambahkan!',
                        showConfirmButton: true,
                    }).then((result) => {
                        response = response.data
                        var newRow = `<tr>
                            <td>${response.nama}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="${response.id}" data-nama="${response.nama}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.id}">
                                    Hapus
                                </button>
                            </td>
                        </tr>`;
                        // Tambahkan row baru ke tabel
                        $('#roleTable tbody').append(newRow);
                        form.trigger("reset"); // Reset form
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
</script>
@endsection
{{-- End of Selection --}}