<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class list_supplier_m extends Model
{
    protected $table = "supliyer";
    public $timestamps = false;
    protected $primaryKey = 'supliyer_id';
    protected $fillable = [
        'supliyer_id', 'supliyer_name', 'user_url', 'paswd_url', 'supliyer_status','supliyer_amount','supliyer_date','url_topup','url_report','usr_report','pswd_report',''
    ];

}
