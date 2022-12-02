<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lampiran extends Model
{
    use HasFactory;

    protected $table = "tb_aju_lampiran";
    protected $guarded = [];
    protected $hidden = [];
    public $timestamps = false;

}
