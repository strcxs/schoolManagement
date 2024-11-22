<?php

namespace App\Models\Kelas;

use App\Models\Siswa\Siswa;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $guarded = ['Id'];
    public $timestamps = false;
    public function siswa(){
        return $this->hasMany(Siswa::class, 'id_kelas', 'id');
    }
}