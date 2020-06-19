<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Crm_user;
// use PragmaRX\Google2FA\Google2FA;

class CreateUserController extends Controller
{    
    
    
    protected function validator(array $data) 
    {
        return Validator::make($data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'id_role' => ['required', 'string', ]
        ]);
    }


    public function index(){


        $google2fa = app('pragmarx.google2fa');
        $secret_key = $google2fa->generateSecretKey();
        // $secret_key = "CG3bgh5FDTR2";

        return view('create_user');
    }
    public function create(Request $request){

    }
}
