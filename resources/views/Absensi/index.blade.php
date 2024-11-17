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
                    <th>Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Tidak Hadir</th>
                </tr>
            </thead>
            <tbody id="kelasList">
                @foreach ($show as $x)
                    @foreach ($x->agenda->kelas->siswa as $siswa)
                        <tr>
                            <td>{{$siswa->NIS}}</td>
                            <td>{{$siswa->Nama}}</td>
                            <td>{{$siswa->kelas->Nama}}</td>
                            <td>{{$x->agenda->guru->Nama}}</td>
                            <td>{{$x->agenda->guru->mapel->Nama}}</td>
                            @php
                                $izin = "";
                                $sakit = "";
                                $tidak_hadir = "";
                            @endphp
                            <!-- izin -->
                            @foreach ($absensi as $absen)
                                @if ($absen->Izin == $siswa->Id)
                                    @php
                                    $izin = "✔";
                                    @endphp
                                    @break
                                @endif
                            @endforeach
                            <td>{{$izin}}</td>
                            <!-- sakit -->
                            @foreach ($absensi as $absen)
                                @if ($absen->Sakit == $siswa->Id)
                                    @php
                                    $sakit = "✔";
                                    @endphp
                                    @break
                                @endif
                            @endforeach
                            <td>{{$sakit}}</td>
                            <!-- tidak hadir -->
                            @foreach ($absensi as $absen)
                                @if ($absen->Tidak_Hadir == $siswa->Id)
                                    @php
                                    $tidak_hadir = "✔";
                                    @endphp
                                    @break
                                @endif
                            @endforeach
                            <td>{{$tidak_hadir}}</td>
                        </tr>
                    @endforeach
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
                <button type="submit" class="btn btn-success w-100">Tambah Mata Pelajaran</button>
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