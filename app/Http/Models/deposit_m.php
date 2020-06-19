<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class deposit_m extends Model
{
    protected $table = "deposit";
    protected $primaryKey = 'deposit_id';
    public $incrementing = false;
    public $timestamps = false;
}
