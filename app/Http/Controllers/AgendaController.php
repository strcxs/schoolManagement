<?php

namespace App\Http\Controllers;

use App\Models\agenda\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    private $data;
    public function index(){
        $this->data['agenda'] = Agenda::with('kelas')
                                ->with('guru')
                                ->get();
        return view('agenda.index', $this->data);
    }
}
