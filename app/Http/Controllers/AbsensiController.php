<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Absensi\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    private $data;

    // Refactor this query logic to a separate method to avoid duplication
    private function getAbsensiData($id_guru = null)
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
        ->groupBy('absensi.id_agenda', 'agenda.time_start', 'agenda.time_end') // Mengelompokkan berdasarkan id_agenda dan kolom yang ingin dipilih dari 'agenda'
        ->with('agenda');

        if ($id_guru) {
            $query->where('id_guru', '=', $id_guru);
        }

        return $query->get();
    }

    public function index()
    {
        $id_guru = Auth::user()->id_guru;
        
        if (Auth::user()->role->nama === "admin") {
            $results = $this->getAbsensiData(); // For admin, fetch all absensi data
        } else {
            $results = $this->getAbsensiData($id_guru); // For teacher, fetch absensi data for their specific id_guru
        }

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
