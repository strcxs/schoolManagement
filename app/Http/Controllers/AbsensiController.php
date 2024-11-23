<?php

namespace App\Http\Controllers;

use App\Models\Absensi\absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    private $data;
    public function index(){
        $results = Absensi::selectRaw(
            'absensi.id_agenda, 
             agenda.time_start, 
             agenda.time_end, 
             COUNT(CASE WHEN izin IS NOT NULL THEN 1 END) as izin,
             COUNT(CASE WHEN sakit IS NOT NULL THEN 1 END) as sakit,
             COUNT(CASE WHEN tidak_hadir IS NOT NULL THEN 1 END) as tidak_hadir'
        )
        ->join('agenda', 'agenda.id', '=', 'absensi.id_agenda') // Menggabungkan tabel 'absensi' dan 'agenda'
        ->groupBy('absensi.id_agenda', 'agenda.time_start', 'agenda.time_end') // Mengelompokkan berdasarkan id_agenda dan kolom yang ingin dipilih dari 'agenda'
        ->get();
        
        $this->data['absensi'] = $results;
        // $this->data['absensi'] = absensi::with('agenda')->get();
        return view('Absensi.index', $this->data);
    }
}
