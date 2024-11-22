<?php

namespace App\Http\Controllers;

use App\Models\Absensi\absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    private $data;
    public function index(){
        $this->data['show'] = Absensi::select('id_agenda')
        ->groupBy('id_agenda')
        ->get();

        $this->data['absensi'] = absensi::with('agenda')->get();
        return view('Absensi.index', $this->data);
    }
}
