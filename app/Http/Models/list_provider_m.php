<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class list_provider_m extends Model
{
    protected $table = "provider";
    public $timestamps = false;
    protected $primaryKey = 'provider_id';
    protected $fillable = [
        'provider_id', 'provider_name', 'provider_pref', 'provaider_type', 'provaider_group','provaider_status'
    ];

}
