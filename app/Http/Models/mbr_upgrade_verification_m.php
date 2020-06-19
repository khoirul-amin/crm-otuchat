<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class mbr_upgrade_verification_m extends Model
{
    protected $table = "mbr_upgrade_verification";
    protected $primaryKey = 'mbr_code';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'mbr_code', 'tempat_lahir', 'tgl_lahir', 'alamat', 'kota','tipe_identitas','no_ktp','rekening','norek', 'nm_pemilik', 'status', 'date_request'
    ];

}
