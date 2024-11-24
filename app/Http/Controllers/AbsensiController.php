<?php

namespace App\Http\Controllers;

use App\Models\Absensi\absensi;
use Auth;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    private $data;
    public function index(){
        
        $id_guru = Auth::user()->id_guru; 
        if (Auth::user()->role->nama === "admin") {
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
            ->with('agenda')
            ->get();
            
            $this->data['absensi'] = $results;
        } else{
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
            ->with('agenda')
            ->where('id_guru','=',$id_guru)
            ->get();
            
            $this->data['absensi'] = $results;
        }

        return view('Absensi.index', $this->data);
    }
}
