@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Agenda</h1>
    <!-- Form Tambah Agenda -->
    @if (Auth::user()->role->nama === "admin")
    <form id="addAgendaForm" class="mt-4 mb-2">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="guruId" class="form-label">Guru</label>
                <select id="guruId" name="guru" class="form-control" required>
                    <option value="" disabled selected>--select--</option>
                    @foreach ($gurus as $guru)
                        <option value="{{$guru->id}}">{{$guru->mapel->nama}} - {{$guru->nama}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="kelasId" class="form-label">Kelas</label>
                <select id="kelasId" name="kelas" class="form-control" required>
                    <option value="" disabled selected>--select--</option>
                    @foreach ($kelass as $kelas)
                        <option value="{{$kelas->id}}">{{$kelas->nama}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="timeStart" class="form-label">Time Start</label>
                <input type="datetime-local" id="timeStart" name="time_start" class="form-control" placeholder="Time Start" required>
            </div>
            <div class="col-md-4">
                <label for="timeEnd" class="form-label">Time End</label>
                <input type="datetime-local" id="timeEnd" name="time_end" class="form-control" placeholder="Time End" required>
            </div>
            <div class="col-md-4 mt-5 ">
                <button type="submit" class="btn btn-success w-100">Tambah Agenda</button>
            </div>
        </div>
    </form>
    @endif
    <hr>
    <!-- Tabel Daftar Agenda dengan DataTables -->
    <div class="table-responsive">
        <table id="agendaTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Time Start</th>
                    <th>Time End</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="agendaList">
                @foreach ($agenda as $x)
                <tr style="background-color: {{ $x->status == 1 ? 'rgb(149, 255, 139)' : 'white' }}">
                    <td>{{$x->guru->NIP}}</td>
                    <td>{{$x->guru->nama}}</td>
                    <td>{{$x->guru->mapel->nama}}</td>
                    <td>{{$x->kelas->nama}}</td>
                    <td>{{$x->time_start}}</td>
                    <td>{{$x->time_end}}</td>
                    <td>
                        @if (Auth::user()->role->nama === "admin")
                        <!-- Tombol Edit -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                            data-id="{{ $x->id }}" data-guru="{{ $x->guru->id }}" data-kelas="{{ $x->kelas->id }}" data-time_start="{{ $x->time_start }}" data-time_end="{{ $x->time_end }}">
                            Edit
                        </button>

                        <!-- Tombol Hapus -->
                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $x->id }}">
                            Hapus
                        </button>
                        @endif
                        <!-- Tombol Mengajar -->
                        @if($x->status == 1 && Auth::user()->role->nama != "admin")
                            <a href="{{ route('agenda.absensi', base64_encode(json_encode(['id_kelas' => $x->kelas->id, 'id_agenda' => $x->id]))) }}" class="btn btn-light btn-sm">
                                Edit
                            </a>
                        @else
                            <a href="{{ route('agenda.absensi', base64_encode(json_encode(['id_kelas' => $x->kelas->id, 'id_agenda' => $x->id]))) }}" class="btn btn-primary btn-sm">
                                @if (Auth::user()->role->nama === "admin") lihat @else Mengajar @endif
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Agenda -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Agenda</h5>
                <button id="close" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="{{ route('agenda.edit', 0) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="form-group m-2">
                        <label for="guruId">Guru</label>
                        <input type="text" class="form-control" id="guruId" name="guru" required>
                    </div>
                    <div class="form-group m-2">
                        <label for="kelasId">Kelas</label>
                        <input type="text" class="form-control" id="kelasId" name="kelas" required>
                    </div>
                    <div class="form-group m-2">
                        <label for="timeStart">Time Start</label>
                        <input type="datetime-local"class="form-control" id="timeStart" name="time_start" required>
                    </div>
                    <div class="form-group m-2">
                        <label for="timeEnd">Time End</label>
                        <input type="datetime-local"class="form-control" id="timeEnd" name="time_end" required>
                    </div>
                    <input type="hidden" id="agendaId" name="id">
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
            var table = $('#agendaTable').DataTable({
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

            // Handle form submit for adding a new agenda
            $('#addAgendaForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var actionUrl = "{{ route('agenda.add') }}"; // URL untuk menambah agenda
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
                                title: 'Agenda berhasil ditambahkan!',
                                showConfirmButton: true,
                            }).then((result) => {
                                console.log(response.data);
                                
                                var newRow = `<tr style="background-color: ${response.data.status == 1 ? 'green' : 'white'}">
                                    <td>${response.data.guru.NIP}</td>
                                    <td>${response.data.guru.nama}</td>
                                    <td>${response.data.guru.mapel.nama}</td>
                                    <td>${response.data.kelas.nama}</td>
                                    <td>${response.data.time_start}</td>
                                    <td>${response.data.time_end}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="${response.data.id}" data-guru="${response.data.guru_id}" data-kelas="${response.data.kelas_id}" data-time_start="${response.data.time_start}" data-time_end="${response.data.time_end}">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>`;
                                // Tambahkan row baru ke tabel
                                $('#agendaTable tbody').append(newRow);
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

            // Handle modal edit action
            $('#editModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); 
                var agendaId = button.data('id');
                var guruId = button.data('guru');
                var kelasId = button.data('kelas');
                var timeStart = button.data('time_start');
                var timeEnd = button.data('time_end');
                
                var modal = $(this);
                modal.find('#agendaId').val(agendaId);
                modal.find('#guruId').val(guruId);
                modal.find('#kelasId').val(kelasId);

                // Format timeStart and timeEnd to remove seconds and convert to 'YYYY-MM-DDTHH:MM'
                var formattedTimeStart = timeStart.replace("/", "-").replace("/", "-").replace(" ", "T").substring(0, 16);
                var formattedTimeEnd = timeEnd.replace("/", "-").replace("/", "-").replace(" ", "T").substring(0, 16);
                modal.find('#timeStart').val(formattedTimeStart);
                modal.find('#timeEnd').val(formattedTimeEnd);

                var actionUrl = "{{ route('agenda.edit', ':id') }}";
                actionUrl = actionUrl.replace(':id', agendaId);
                modal.find('#editForm').attr('action', actionUrl);
            });

            $('#editForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                var form = $(this);
                var agendaId = $('#agendaId').val();
                var guruId = $('#guruId').val();
                var kelasId = $('#kelasId').val();
                var timeStart = $('#timeStart').val();
                var timeEnd = $('#timeEnd').val();

                // Show SweetAlert confirmation before submitting
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Perubahan akan diterapkan ke data agenda ini.",
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
                                        <tr style="background-color: ${response.data.status == 1 ? 'green' : 'white'}">
                                            <td>${response.data.guru.NIP}</td>
                                            <td>${response.data.guru.nama}</td>
                                            <td>${response.data.guru.mapel.nama}</td>
                                            <td>${response.data.kelas.nama}</td>
                                            <td>${response.data.time_start}</td>
                                            <td>${response.data.time_end}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="${response.data.id}" data-guru="${response.data.guru_id}" data-kelas="${response.data.kelas_id}" data-time_start="${response.data.time_start}" data-time_end="${response.data.time_end}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${response.data.id}">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                    
                                    // Find and replace the old row with the updated one
                                    $('#agendaTable tbody tr').each(function() {
                                        if ($(this).find('td').first().text() == agendaId) {
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

            // Menggunakan SweetAlert untuk konfirmasi hapus
            $(document).on('click', '.delete-btn', function () {
                var agendaId = $(this).data('id');
                
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
                            url: "{{ route('agenda.delete') }}",
                            type: 'POST',
                            data: { id: agendaId, _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.status == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Data agenda berhasil dihapus!',
                                        showConfirmButton: true,
                                    }).then((result) => {
                                        // Hapus row dari tabel
                                        $('#agendaTable tbody tr').each(function() {
                                            if ($(this).find('td').first().text() == agendaId) {
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
        });
    </script>
@endsection