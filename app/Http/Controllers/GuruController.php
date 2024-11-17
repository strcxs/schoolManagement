<?php

namespace App\Http\Controllers;

use App\Models\guru\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    private $data;
    public function index(){
        $this->data['guru'] = Guru::with('mapel')->get();
        return view('guru.index',$this->data);
    }
}
