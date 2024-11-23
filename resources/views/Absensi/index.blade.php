@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Absensi Kelas</h1>

    <!-- Tabel Daftar Kelas dengan DataTables -->
    <div class="table-responsive">
        <table id="kelasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>time start</th>
                    <th>time end</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Tidak Hadir</th>
                </tr>
            </thead>
            <tbody id="kelasList">
                @foreach ($absensi as $x)
                <tr>
                    <td>{{$x->agenda->kelas->nama}}</td>
                    <td>{{$x->time_start}}</td>
                    <td>{{$x->time_end}}</td>
                    <td>{{$x->agenda->guru->mapel->nama}}</td>
                    <td>{{$x->agenda->guru->nama}}</td>

                    <td>{{$x->izin}}</td>
                    <td>{{$x->sakit}}</td>
                    <td>{{$x->tidak_hadir}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
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
        });
    </script>
@endsection