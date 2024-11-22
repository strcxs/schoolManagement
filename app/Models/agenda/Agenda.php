<?php

namespace App\Models\agenda;

use App\Models\guru\Guru;
use App\Models\Kelas\Kelas;
use App\Models\mapel\Mapel;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
    public function guru(){
        return $this->belongsTo(Guru::class, 'id_guru', 'id')->with('mapel');
    }

}