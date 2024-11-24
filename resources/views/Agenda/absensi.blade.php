@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Absensi Kelas</h1>
    <!-- Tabel Daftar Kelas dengan DataTables -->
    <div class="table-responsive">
        <table id="kelasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Tidak Hadir</th>
                </tr>
            </thead>
            <tbody id="kelasList">
                @foreach ($siswa as $x)
                <tr>
                    <td>{{$x->NIS}}</td>
                    <td>{{$x->nama}}</td>
                    <td>{{$x->kelas->nama}}</td>
                    <td>
                        <input class="form-check-input kehadiran-checkbox" type="checkbox" name="kehadiran{{$x->NIS}}" id="izin{{$x->NIS}}" value="izin" @if ($absensi->contains('izin', $x->id)) checked @endif @if(Auth::user()->role->nama == "admin") disabled @endif>
                        <input hidden type="text" id="id{{$x->NIS}}" value="{{$x->id}}">
                    </td>
                    
                    <td>
                        <input class="form-check-input kehadiran-checkbox" type="checkbox" name="kehadiran{{$x->NIS}}" id="sakit{{$x->NIS}}" value="sakit" @if ($absensi->contains('sakit', $x->id)) checked @endif @if(Auth::user()->role->nama == "admin") disabled @endif>
                    </td>
                    <td>
                        <input class="form-check-input kehadiran-checkbox" type="checkbox" name="kehadiran{{$x->NIS}}" id="tidak_hadir{{$x->NIS}}" value="tidak_hadir" @if ($absensi->contains('tidak_hadir', $x->id)) checked @endif @if(Auth::user()->role->nama == "admin") disabled @endif>
                    </td>
                    <script>
                        document.querySelectorAll('.kehadiran-checkbox').forEach(function(checkbox) {
                            checkbox.addEventListener('change', function() {
                                if (this.checked) {
                                    document.querySelectorAll('.kehadiran-checkbox[name="' + this.name + '"]').forEach(function(otherCheckbox) {
                                        if (otherCheckbox !== checkbox) {
                                            otherCheckbox.checked = false;
                                        }
                                    });
                                }
                            });
                        });
                    </script>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(Auth::user()->role->nama != "admin")
    <button id="saveChanges" class="btn btn-primary float-end">Simpan Perubahan</button>
    @endif
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

                if(namaKelas) {
                    // Add a new row to the DataTable
                    var rowCount = table.rows().count();
                    table.row.add([
                        rowCount + 1,
                        namaKelas
                    ]).draw();

                    // Clear the input field
                    $('#namaKelas').val('');
                }
            });

            // Handle save changes button click
            $('#saveChanges').click(function() {
                var kehadiranData = [];
                $('#kelasTable tbody tr').each(function() {
                    var row = $(this);
                    
                    var NIS = row.find('td:first-child').text().trim();
                    var id = row.find('#id'+NIS).val();
                    var izin = row.find('input[name="kehadiran' + NIS + '"][value="izin"]:checked').val();
                    var sakit = row.find('input[name="kehadiran' + NIS + '"][value="sakit"]:checked').val();
                    var tidak_hadir = row.find('input[name="kehadiran' + NIS + '"][value="tidak_hadir"]:checked').val();

                    kehadiranData.push({
                        NIS: id,
                        izin: izin,
                        sakit: sakit,
                        tidak_hadir: tidak_hadir
                    });
                });
                
                var actionUrl = "{{ route('agenda.save') }}";
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: {
                        _token : csrfToken,
                        id_agenda: {{ $agenda->id }}, // Mendapatkan id_agenda dari variabel $agenda
                        kehadiranData: kehadiranData
                    },
                    success: function(response) {
                        if(response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Perubahan berhasil disimpan!',
                                showConfirmButton: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('agenda.index') }}";
                                }
                            });
                        } else {
                            alert('Gagal menyimpan perubahan.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            });
        });
    </script>
@endsection