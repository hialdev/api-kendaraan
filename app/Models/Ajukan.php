<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajukan extends Model
{
    use HasFactory;

    protected $table = "tb_aju";
    protected $guarded = [];
    protected $hidden = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'id_aju';
    protected $keyType = 'string';
    
    public function lampiran()
    {
        return $this->hasOne(Lampiran::class, 'id_aju', 'id_aju');
    }

    public function pengemudi()
    {
        return $this->hasOne(Pengemudi::class,'id','pengemudi');
    }

    public function kendaraan()
    {
        return $this->hasOne(Kendaraan::class,'id_aju','id_aju');
    }
    
}
