@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Guru</h1>
    <form id="addGuruForm" class="mt-4 mb-2">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" id="nip" name="nip" class="form-control" placeholder="NIP" required>
            </div>
            <div class="col-md-4">
                <input type="text" id="namaGuru" name="nama" class="form-control" placeholder="Nama Guru" required>
            </div>
            <div class="col-md-4">
                <select id="mapel" name="mapel" class="form-control" required>
                    <option value="" disabled selected>--select--</option>
                    @foreach ($mapels as $mapel)
                        <option value="{{$mapel->id}}">{{$mapel->nama}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Tambah Guru</button>
            </div>
        </div>
    </form>
    <hr>
    <!-- Tabel Daftar Guru dengan DataTables -->
    <div class="table-responsive">
        <table id="guruTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Mata Pelajaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="guruList">
                @foreach ($guru as $x)
                    <tr>
                        <td>{{$x->NIP}}</td>
                        <td>{{$x->nama}}</td>
                        <td>{{$x->mapel->nama}}</td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                data-id="{{ $x->id }}" data-nama="{{ $x->nama }}" data-mapel="{{ $x->mapel->id }}">
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

<!-- Modal Edit Guru -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Guru</h5>
                <button id="close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{ route('guru.edit', 0) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="form-group m-2">
                        <label for="guruNama">Nama Guru</label>
                        <input type="text" class="form-control" id="guruNama" name="nama" required>
                    </div>
                    <div class="form-group m-2">
                        <label for="guruMapel">Mata Pelajaran</label>
                        <select id="guruMapel" name="mapel" class="form-control" required>
                            <option value="" disabled selected>--select--</option>
                            @foreach ($mapels as $mapel)
                                <option value="{{$mapel->id}}">{{$mapel->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="guruId" name="id">
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
        var table = $('#guruTable').DataTable({
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

        // Handle form submit for adding a new guru
        $('#addGuruForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var actionUrl = "{{ route('guru.add') }}"; // URL untuk menambah guru
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
                            title: 'Guru berhasil ditambahkan!',
                            showConfirmButton: true,
                        }).then((result) => {
                            response = response.data
                            console.log(response);
                            
                            var newRow = `<tr>
                                <td>${response.NIP}</td>
                                <td>${response.nama}</td>
                                <td>${response.mapel.nama}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="${response.id}" data-nama="${response.nama}" data-mapel="${response.mapel.id}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.id}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>`;
                            // Tambahkan row baru ke tabel
                            $('#guruTable tbody').append(newRow);
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

        // Handle delete action
        $(document).on('click', '.delete-btn', function () {
            var guruId = $(this).data('id');
            
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
                    $.ajax({
                        url: "{{ route('guru.delete') }}",
                        type: 'POST',
                        data: { id: guruId },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.status == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data guru berhasil dihapus!',
                                    showConfirmButton: true,
                                }).then((result) => {
                                    // Hapus baris dari tabel tanpa refresh halaman
                                    $('#guruTable tbody').find('button[data-id="' + guruId + '"]').closest('tr').remove();
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

        // Handle modal edit action
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var guruId = button.data('id');
            var guruNama = button.data('nama');
            var guruMapel = button.data('mapel');
            
            var modal = $(this);
            modal.find('#guruId').val(guruId);
            modal.find('#guruNama').val(guruNama);
            modal.find('#guruMapel').val(guruMapel);

            var actionUrl = "{{ route('guru.edit', ':id') }}";
            actionUrl = actionUrl.replace(':id', guruId);
            modal.find('#editForm').attr('action', actionUrl);
        });
        $('#editForm').on('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            var form = $(this);
            var guruId = $('#guruId').val();
            var guruNama = $('#guruNama').val();
            var guruMapel = $('#guruMapel').val();

            // Show SweetAlert confirmation before submitting
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Perubahan akan diterapkan ke data guru ini.",
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
                                console.log(response);
                                
                                // Update the row in the table (for example)
                                var updatedRow = `
                                    <tr>
                                        <td>${response.data.NIP}</td>
                                        <td>${response.data.nama}</td>
                                        <td>${response.data.mapel.nama}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="${response.data.id}" data-nama="${response.data.nama}" data-mapel="${response.data.mapel.nama}">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                
                                // Find and replace the old row with the updated one
                                table.row($(`#guruTable tbody tr:has(td:contains(${guruId}))`)).remove().draw();
                                table.row.add($(updatedRow)).draw();
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
    });
</script>
@endsection
