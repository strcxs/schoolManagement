@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">{{ $mapel->nama_schedule . ' - ' . $mapel->mapel->nama }}</h1>
    <hr>
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <select id="id_guru" name="guru" class="form-control" required>
                <option value="" disabled selected>--select guru--</option>
                @foreach ($gurus as $guru)
                    <option value="{{$guru->id}}" @if ($guru->id == $id_guruSelected) selected @endif>{{$guru->nama}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <!-- Tabel Daftar Agenda dengan DataTables -->
    <div class="table-responsive">
        <table id="scheduleTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th style="display: none">ID</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Time Start</th>
                    <th>Time End</th>
                    <th style="text-align: center">Action</th>
                </tr>
            </thead>
            <tbody id="agendaList">
                @foreach ($agenda as $x)
                <tr>
                    <td style="display: none">{{$x->id}}</td>
                    <td>{{$x->mapel->nama}}</td>
                    <td>{{$x->kelas->nama}}</td>
                    <td>{{$x->time_start}}</td>
                    <td>{{$x->time_end}}</td>
                    <td style="text-align: center">
                        <input class="form-check-input agenda-checkbox" type="checkbox" name="agenda-{{$x->id}}" id="{{$x->id}}" value="check" @if ($x->id_schedule == $schedule_id) checked @endif>
                        <input hidden type="text" id="id-{{$x->id}}" value="{{$x->id}}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button id="saveChanges" type="submit" class="btn btn-primary">Save</button>
                <button type="submit" class="btn btn-danger">Cancel</button>
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
            var table = $('#scheduleTable').DataTable({
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
        });
        $('#saveChanges').click(function() {
                var kehadiranData = [];
                $('#scheduleTable tbody tr').each(function() {
                    var row = $(this);
                    
                    var id_agenda = row.find('td:first-child').text().trim();
                    var agenda = row.find('#id-'+id_agenda).val();
                    var scheduleAdd = row.find('input[name="agenda-' + agenda + '"][value="check"]:checked').val();
                    
                    kehadiranData.push({
                        id_agenda: id_agenda,
                        scheduleAdd: scheduleAdd
                    });
                });
                var actionUrl = "{{ route('schedule.save') }}";
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var id_guru = $('#id_guru').val();
                
                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: {
                        _token : csrfToken,
                        id_schedule: {{ $schedule_id }}, // Mendapatkan id_agenda dari variabel $agenda
                        kehadiranData: kehadiranData,
                        id_guru: id_guru
                    },
                    success: function(response) {
                        if(response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Perubahan berhasil disimpan!',
                                showConfirmButton: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
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
    </script>
@endsection