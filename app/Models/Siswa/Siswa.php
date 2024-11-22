<?php

namespace App\Models\Siswa;

use App\Models\Kelas\Kelas;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
}