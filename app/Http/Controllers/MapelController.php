<?php

namespace App\Http\Controllers;

use App\Models\mapel\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    private $data;
    public function index(){
        $this->data['mapel'] = Mapel::get();
        return view('mapel.index', $this->data);
    }
}
