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
    public function kelas(){
        return $this->belongsTo(Kelas::class, 'Id_Kelas', 'Id');
    }
    public function guru(){
        return $this->belongsTo(Guru::class, 'Id_Guru', 'Id');
    }

}