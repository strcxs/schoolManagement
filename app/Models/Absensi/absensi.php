<?php

namespace App\Models\Absensi;

use App\Models\agenda\Agenda;
use Illuminate\Database\Eloquent\Model;

class absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function agenda(){
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id')->with('kelas')->with('guru');
    }
}