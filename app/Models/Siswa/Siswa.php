<?php

namespace App\Models\Siswa;

use App\Models\Kelas\Kelas;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id';
    public function kelas(){
        return $this->belongsTo(Kelas::class, 'Id_Kelas', 'Id');
    }
}