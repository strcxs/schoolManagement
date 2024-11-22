@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Mapel</h1>
    <form id="addMapelForm" class="mt-4 mb-2">
        <div class="row g-3">
            <div class="col-md-8">
                <input type="text" id="namaMapel" name="nama" class="form-control" placeholder="Nama Mapel" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Tambah Mapel</button>
            </div>
        </div>
    </form>
    <!-- Tabel Daftar Mapel dengan DataTables -->
    <div class="table-responsive">
        <table id="mapelTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mapel</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="mapelList">
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
                    <h5 class="modal-title" id="editModalLabel">Edit Mapel</h5>
                    <button id="close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('mapel.edit', 0) }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group m-2">
                            <label for="mapelNama">Nama Mapel</label>
                            <input type="text" class="form-control" id="mapelNama" name="nama" required>
                        </div>
                        <input type="hidden" id="mapelId" name="id">
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
        $('#mapelTable').DataTable({
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
        var mapelId = button.data('id');
        var mapelNama = button.data('nama');
        
        var modal = $(this);
        modal.find('#mapelId').val(mapelId);
        modal.find('#mapelNama').val(mapelNama);
        
        var actionUrl = "{{ route('mapel.edit', ':id') }}";
        actionUrl = actionUrl.replace(':id', mapelId);
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
                        title: 'Data mapel berhasil diperbarui!',
                        showConfirmButton: true,
                    }).then((result) => {
                        response = response.data
                        var updatedRow = `<tr>
                            <td>${response.id}</td>
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
                        $('#mapelTable tbody tr').each(function() {
                            if ($(this).find('td').first().text() == response.id) {
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
    $(document).on('click', '.delete-btn', function () {
        var mapelId = $(this).data('id');
        
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
                    url: "{{ route('mapel.delete') }}",
                    type: 'POST',
                    data: { id: mapelId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data mapel berhasil dihapus!',
                                showConfirmButton: true,
                            }).then((result) => {
                                // Hapus row dari tabel
                                $('#mapelTable tbody tr').each(function() {
                                    if ($(this).find('td').first().text() == mapelId) {
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

    // Submit add form using Ajax
    $('#addMapelForm').on('submit', function (e) {
        e.preventDefault(); // Prevent normal form submission

        var form = $(this);
        var actionUrl = "{{ route('mapel.add') }}"; // URL untuk menambah mapel
        var formData = form.serialize(); 
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken  // Menambahkan CSRF token ke header
            },
            success: function (response) {
                if (response.status == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data mapel berhasil ditambahkan!',
                        showConfirmButton: true,
                    }).then((result) => {
                        response = response.data;
                        var newRow = `<tr>
                            <td>${response.id}</td>
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
                        $('#mapelTable tbody').append(newRow);
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
