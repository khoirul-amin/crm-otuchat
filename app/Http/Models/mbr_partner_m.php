<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class mbr_partner_m extends Model
{
    protected $table = "mbr_patner";
    protected $primaryKey = 'mbr_code';
    public $incrementing = false;
    public $timestamps = false;
}
