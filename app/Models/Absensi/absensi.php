<?php

namespace App\Models\Absensi;

use App\Models\agenda\Agenda;
use Illuminate\Database\Eloquent\Model;

class absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    public function agenda(){
        return $this->belongsTo(Agenda::class, 'Id_Agenda', 'Id');
    }
}