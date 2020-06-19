<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class mbr_list_m extends Model
{
    protected $table = "mbr_list";
    protected $primaryKey = 'mbr_code';
    public $incrementing = false;
    public $timestamps = false;
}
