<?php

namespace App\Http\Controllers;

use App\Models\Siswa\Siswa;
use IlluminaAte\Http\Request;

class SiswaController extends Controller
{
    private $data;
    public function index(){
        $this->data['siswa'] = Siswa::with('kelas')->get();
        return view('siswa.index', $this->data);
    }
}
