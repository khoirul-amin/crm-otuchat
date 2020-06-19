<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = "crm_user";
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'email', 'password','id_role','token','key'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'key','token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];



    // public function setGoogle2faSecretAttribute($value)
    // {
    //      $this->attributes['key'] = encrypt($value);
    // }

    // /**
    //  * Decrypt the user's google_2fa secret.
    //  *
    //  * @param  string  $value
    //  * @return string
    //  */
    // public function getGoogle2faSecretAttribute($value)
    // {
    //     return decrypt($value);
    // }
}
