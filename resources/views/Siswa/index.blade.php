@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Kelas</h1>

    <!-- Tabel Daftar Kelas dengan DataTables -->
    <div class="table-responsive">
        <table id="kelasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="kelasList">
                @foreach ($siswa as $x)
                    <tr>
                        <td>{{$x->NIS}}</td>
                        <td>{{$x->nama}}</td>
                        <td>{{$x->kelas->nama}}</td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                data-id="{{ $x->id }}" data-nama="{{ $x->nama }}" data-kelas="{{ $x->kelas->id }}">
                                Edit
                            </button>

                            <!-- Delete Button -->
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $x->id }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Form Tambah Kelas -->
    <form id="addKelasForm" class="mt-4">
        <div class="row g-3">
            <div class="col-md-8">
                <input type="text" id="namaKelas" name="namaKelas" class="form-control" placeholder="Nama Kelas" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Tambah Kelas</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Edit Siswa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Siswa</h5>
                <button id="close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{ route('siswa.edit', 0) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="form-group m-2">
                        <label for="siswaNama">Nama Siswa</label>
                        <input type="text" class="form-control" id="siswaNama" name="nama" required>
                    </div>
                    <div class="form-group m-2">
                        <label for="siswaKelas">Kelas</label>
                        <input type="text" class="form-control" id="siswaKelas" name="kelas" required>
                    </div>
                    <input type="hidden" id="siswaId" name="id">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#kelasTable').DataTable({
            paging: true,  // Enable pagination
            searching: true,
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

        // Handle form submit for adding a new class
        $('#addKelasForm').on('submit', function(e) {
            e.preventDefault();

            var namaKelas = $('#namaKelas').val();

            if (namaKelas) {
                // Show SweetAlert confirmation before adding new class
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menambahkan kelas baru.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tambah!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add a new row to the DataTable
                        var rowCount = table.rows().count();
                        table.row.add([
                            rowCount + 1, // Example: Auto-incrementing row number
                            namaKelas,
                            // Add additional data for Kelas
                            '<button class="btn btn-warning btn-sm edit-btn" data-id="' + (rowCount + 1) + '" data-nama="' + namaKelas + '" data-kelas="' + namaKelas + '">Edit</button>' +
                            '<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' + (rowCount + 1) + '">Hapus</button>'
                        ]).draw();

                        // Clear the input field
                        $('#namaKelas').val('');
                        Swal.fire({
                            icon: 'success',
                            title: 'Kelas berhasil ditambahkan!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });

        // Handle modal edit action
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var siswaId = button.data('id');
            var siswaNama = button.data('nama');
            var siswaKelas = button.data('kelas');
            
            var modal = $(this);
            modal.find('#siswaId').val(siswaId);
            modal.find('#siswaNama').val(siswaNama);
            modal.find('#siswaKelas').val(siswaKelas);

            var actionUrl = "{{ route('siswa.edit', ':id') }}";
            actionUrl = actionUrl.replace(':id', siswaId);
            modal.find('#editForm').attr('action', actionUrl);
        });

        $('#editForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            var form = $(this);
            var siswaId = $('#siswaId').val();
            var siswaNama = $('#siswaNama').val();
            var siswaKelas = $('#siswaKelas').val();

            // Show SweetAlert confirmation before submitting
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Perubahan akan diterapkan ke data siswa ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, perbarui!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the form submission via AJAX if confirmed
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(), // Serialize form data
                        success: function(response) {
                            // Show success alert and update UI
                            Swal.fire({
                                icon: 'success',
                                title: 'Data berhasil diperbarui!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Close the modal
                                $('#close').click();

                                // Update the row in the table (for example)
                                var updatedRow = `
                                    <tr>
                                        <td>${response.data.NIS}</td>
                                        <td>${response.data.nama}</td>
                                        <td>${response.data.kelas_nama}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="${response.data.id}" data-nama="${response.data.nama}" data-kelas="${response.data.kelas_nama}">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                
                                // Find and replace the old row with the updated one
                                $('#kelasTable tbody tr').each(function() {
                                    if ($(this).find('td').first().text() == siswaId) {
                                        $(this).replaceWith(updatedRow);
                                    }
                                });
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan!',
                                text: xhr.responseJSON.message || 'Silakan coba lagi nanti.'
                            });
                        }
                    });
                }
            });
        });

        // Handle delete button with SweetAlert confirmation
        $('#kelasTable').on('click', '.delete-btn', function() {
            var row = $(this).closest('tr');
            var siswaId = $(this).data('id');

            // Show SweetAlert confirmation before deletion
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Siswa ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Delete the row from the DataTable
                    table.row(row).remove().draw();
                    Swal.fire({
                        icon: 'success',
                        title: 'Siswa berhasil dihapus!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    });
</script>
@endsection