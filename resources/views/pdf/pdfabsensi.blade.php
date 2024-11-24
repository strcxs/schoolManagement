<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
            padding-bottom: 20px;
            text-align: center;
            position: relative;
        }
        .header img {
            width:70px;
        }
        .header p {
            margin-top: 25px;
            font-size: medium;
        }
        .logo {
            position: absolute;
            left: 10px;
            width: 100px;
            height: auto;
        }
        .company-name {
            font-size: 24px;
            margin: 0;
            z-index: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .table-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Gambar logo perusahaan -->
        <img src="{{ public_path() . '/images/pusdikhubad.jpg' }}" alt="Company Logo" class="logo">
        <p class="company-name">Sekolah Menengah Kejuruan <br>PUSDIKHUBAD CIMAHI</p>
    </div> 
    <hr style="border-top: 1px solid black;">
    <!-- <h2>Abensi </h2> -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="border: 1px solid #000;">Kelas</th>
                    <th style="border: 1px solid #000;">time start</th>
                    <th style="border: 1px solid #000;">time end</th>
                    <th style="border: 1px solid #000;">Mata Pelajaran</th>
                    <th style="border: 1px solid #000;">Guru</th>
                    <th style="border: 1px solid #000;">Izin</th>
                    <th style="border: 1px solid #000;">Sakit</th>
                    <th style="border: 1px solid #000;">Tidak Hadir</th>
                </tr>
            <tbody>
                @foreach ($absensi as $x)
                <tr>
                    <td style="border: 1px solid #000;">{{$x->agenda->kelas->nama}}</td>
                    <td style="border: 1px solid #000;">{{$x->time_start}}</td>
                    <td style="border: 1px solid #000;">{{$x->time_end}}</td>
                    <td style="border: 1px solid #000;">{{$x->agenda->guru->mapel->nama}}</td>
                    <td style="border: 1px solid #000;">{{$x->agenda->guru->nama}}</td>
                    <td style="border: 1px solid #000;">{{$x->izin}}</td>
                    <td style="border: 1px solid #000;">{{$x->sakit}}</td>
                    <td style="border: 1px solid #000;">{{$x->tidak_hadir}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <table align='right' style="width: 25%;">
        <tr align='center'>
            <td>Cimahi, {{ date('d M Y', time()) }}</td>
        </tr>
        <tr align='center'>
            <td>Megetahui, </td>
        </tr>
        <tr align='center' valign='bottom'>
            <td height='100'>Kepala Sekolah</td>
        </tr>
    </table>
</body>
</html>
