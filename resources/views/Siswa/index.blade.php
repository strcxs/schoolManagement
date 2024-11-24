@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Siswa</h1>
    <form id="addSiswaForm" class="mt-4 mb-2">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" id="nisSiswa" name="nis" class="form-control" placeholder="NIS" required>
            </div>
            <div class="col-md-4">
                <input type="text" id="namaSiswa" name="nama" class="form-control" placeholder="Nama Siswa" required>
            </div>
            <div class="col-md-4">
                <select id="kelasSiswa" name="kelas" class="form-control" required>
                    <option value="" disabled selected>--select--</option>
                    @foreach ($kelass as $kelas)
                        <option value="{{$kelas->id}}">{{$kelas->nama}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Tambah Siswa</button>
            </div>
        </div>
    </form>
    <!-- Tabel Daftar Siswa dengan DataTables -->
    <div class="table-responsive">
        <table id="siswaTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="siswaList">
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
                        <select id="siswaKelas" name="kelas" class="form-control" required>
                            <option value="" disabled selected>--select--</option>
                            @foreach ($kelass as $kelas)
                                <option value="{{$kelas->id}}">{{$kelas->nama}}</option>
                            @endforeach
                        </select>
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
        var table = $('#siswaTable').DataTable({
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

        // Handle form submit for adding a new student
        $('#addSiswaForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var actionUrl = "{{ route('siswa.add') }}"; // URL untuk menambah siswa
            var formData = form.serialize(); // Serialize data form menjadi string
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            if ($('#nisSiswa').val() && $('#namaSiswa').val() && $('#kelasSiswa').val()) {
                // Show SweetAlert confirmation before adding new student
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menambahkan siswa baru.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tambah!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: actionUrl,
                            type: 'POST',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken  // Menambahkan CSRF token ke header
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    // Add a new row to the DataTable
                                    var newRow = `<tr>
                                        <td>${response.data.NIS}</td>
                                        <td>${response.data.nama}</td>
                                        <td>${response.data.kelas.nama}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="${response.data.id}" data-nama="${response.data.nama}" data-kelas="${response.data.kelas.id}">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>`;
                                    table.row.add($(newRow)).draw();

                                    // Clear the input fields
                                    $('#nisSiswa').val('');
                                    $('#namaSiswa').val('');
                                    $('#kelasSiswa').val('');
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Siswa berhasil ditambahkan!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan!',
                                    text: 'Silakan coba lagi nanti.'
                                });
                            }
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
                            if (response.status == 200) {
                                // Show success alert and update UI
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data berhasil diperbarui!',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    // Close the modal
                                    $('#close').click();

                                    // Update the row in the table
                                    var updatedRow = `
                                        <tr>
                                            <td>${response.data.NIS}</td>
                                            <td>${response.data.nama}</td>
                                            <td>${response.data.kelas.nama}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="${response.data.id}" data-nama="${response.data.nama}" data-kelas="${response.data.kelas.id}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                    
                                    // Find and replace the old row with the updated one
                                    $('#siswaTable tbody tr').each(function() {
                                        if ($(this).find('td').first().text() == siswaId) {
                                            $(this).replaceWith(updatedRow);
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
                        error: function(xhr) {
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

        // Handle delete button with SweetAlert confirmation
        $('#siswaTable').on('click', '.delete-btn', function() {
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
                    $.ajax({
                        url: "{{ route('siswa.delete') }}",
                        type: 'POST',
                        data: { id: siswaId },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                // Delete the row from the DataTable
                                table.row(row).remove().draw();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Siswa berhasil dihapus!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan!',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
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
    });
</script>
@endsection