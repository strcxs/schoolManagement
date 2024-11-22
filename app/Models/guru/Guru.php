<?php

namespace App\Models\guru;

use App\Models\mapel\Mapel;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function mapel(){
        return $this->belongsTo(Mapel::class,'id_mapel','id');
    }
}