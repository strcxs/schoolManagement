@extends('app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">Manajemen Kelas</h1>
    <!-- Tabel Daftar Kelas dengan DataTables -->
    <div class="table-responsive">
        <table id="kelasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Time Start</th>
                    <th>Time End</th>
                </tr>
            </thead>
            <tbody id="kelasList">
                @foreach ($agenda as $x)
                    <tr>
                        <td>{{$x->guru->NIP}}</td>
                        <td>{{$x->guru->nama}}</td>
                        <td>{{$x->guru->mapel->nama}}</td>
                        <td>{{$x->kelas->nama}}</td>
                        <td>{{$x->time_start}}</td>
                        <td>{{$x->time_end}}</td>
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