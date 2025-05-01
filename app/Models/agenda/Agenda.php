<?php

namespace App\Models\agenda;

use App\Models\guru\Guru;
use App\Models\Kelas\Kelas;
use App\Models\mapel\Mapel;
use App\Models\Schedule\Scheduler;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
    public function schedule(){
        return $this->hasOne(Scheduler::class, 'id', 'id_schedule')->with(['mapel','guru']);
    }
    public function mapel(){
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id');
    }
}