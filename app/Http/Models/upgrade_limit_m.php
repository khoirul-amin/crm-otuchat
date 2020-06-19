<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class upgrade_limit_m extends Model
{
    protected $table = "log_upgrade_limit";
    protected $primaryKey = 'mbr_code';
    public $timestamps = false;
}
