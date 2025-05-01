<?php

namespace App\Models\Schedule;

use App\Models\agenda\Agenda;
use App\Models\guru\Guru;
use App\Models\mapel\Mapel;
use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    protected $table = 'schedule';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function mapel(){
        return $this->belongsTo(Mapel::class,'id_mapel','id');
    }
    public function guru(){
        return $this->belongsTo(Guru::class,'id_guru','id');
    }
    public function agenda(){
        return $this->hasMany(Agenda::class,'id_schedule','id');
    }
}
