<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengemudi extends Model
{
    use HasFactory;

    protected $table = "tb_pengemudi";
    protected $guarded = [];
    protected $hidden = [];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    
}
