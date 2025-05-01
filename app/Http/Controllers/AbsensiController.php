<?php

namespace App\Http\Controllers;

use App\Models\guru\Guru;
use App\Models\Kelas\Kelas;
use Auth;
use Illuminate\Http\Request;
use App\Models\Absensi\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    private $data;

    // Refactor this query logic to a separate method to avoid duplication
    private function getAbsensiData($id_guru = null, $id_kelas = null)
    {
        $query = Absensi::selectRaw(
            'absensi.id_agenda, 
            agenda.time_start, 
            agenda.time_end, 
            COUNT(CASE WHEN izin IS NOT NULL THEN 1 END) as izin,
            COUNT(CASE WHEN sakit IS NOT NULL THEN 1 END) as sakit,
            COUNT(CASE WHEN tidak_hadir IS NOT NULL THEN 1 END) as tidak_hadir'
        )
        ->join('agenda', 'agenda.id', '=', 'absensi.id_agenda') // Menggabungkan tabel 'absensi' dan 'agenda'
        ->leftJoin('schedule', 'agenda.id_schedule', '=', 'schedule.id')
        ->groupBy('absensi.id_agenda', 'agenda.time_start', 'agenda.time_end') // Mengelompokkan berdasarkan id_agenda dan kolom yang ingin dipilih dari 'agenda'
        ->with('agenda');
        if ($id_kelas) {
            $query->where('agenda.id_kelas',$id_kelas);
        }
        // dd($query->get());
        if ($id_guru) {
            $query->where('schedule.id_guru',$id_guru);
        }
        return $query->get();
    }

    public function index()
    {
        $id_kelas = request()->get('id_kelas');
        $id_guru = Auth::user()->id_guru;
        // dd($this->getAbsensiData());
        if (Auth::user()->role->nama === "admin") {
            $id_guru = request()->get('id_guru');
            $results = $this->getAbsensiData($id_guru,$id_kelas); // For admin, fetch all absensi data
        } else {
            $results = $this->getAbsensiData($id_guru,$id_kelas); // For teacher, fetch absensi data for their specific id_guru
        }
        $this->data['kelasList'] = Kelas::get();
        $this->data['guruList'] = Guru::get();

        $this->data['absensi'] = $results;
        
        return view('Absensi.index', $this->data);
    }

    public function generatePdf()
    {
        $id_guru = Auth::user()->id_guru;
        
        if (Auth::user()->role->nama === "admin") {
            $results = $this->getAbsensiData(); // For admin, fetch all absensi data
        } else {
            $results = $this->getAbsensiData($id_guru); // For teacher, fetch absensi data for their specific id_guru
        }
        $this->data['absensi'] = $results;
        // Pass the results directly to the PDF view
        $pdf = Pdf::loadView('pdf.pdfabsensi',$this->data);

        // Download the PDF
        return $pdf->download('absensi.pdf');
    }
}
