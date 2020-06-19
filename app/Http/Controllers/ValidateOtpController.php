<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
// use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Libraries\AuthLibrary;

class ValidateOtpController extends Controller
{

    public function resetpassword(Request $request){
        
        $password = Hash::make($request->password);
        $secret = $request->key;
        // $google2Fa = new Google2FA();

        $valid = (new AuthLibrary())->verifyCode(Session::get('key'), $secret, 2);

        if($valid){

            // Add Profile User to session
            $id = Auth::user()->id;
            $role = DB::table('crm_user')->where('crm_user.id', $id)
            ->join('crm_role', 'crm_role.id', '=' ,'crm_user.id_role')
            ->join('crm_divisi', 'crm_divisi.id', '=' ,'crm_role.id_divisi')->first();


            $id_role = Auth::user()->id_role;
            $permission = DB::table('crm_permission')->where('id_role', $id_role)->first();

            $menu = DB::table('crm_menu')->get();

            if($permission->id_submenu){
                $permission_id = explode(",",$permission->id_submenu);
                $jumlah_id = count($permission_id);
                $sub_menu = [];
                for($id_sub_menu=0; $id_sub_menu <= $jumlah_id-1; $id_sub_menu++){
                    $permisi = $permission_id[$id_sub_menu];
                    $res = DB::table('crm_sub_menu')->where('id',$permisi)->first();
                    if($res){
                        $sub_menu[$id_sub_menu] = $res;
                    }
                }
            }else{
                $sub_menu = null;
            }

            DB::table('crm_user')->where('id', $id)->update(['password' =>$password, 'key' => Session::get('key')]);

            // dd($sub_menu);
            Session::forget('key');
            Session::put('menu', $menu);
            Session::put('sub_menu', $sub_menu);
            Session::put('permission', $permission);
            Session::put('dataUser', $role);
            Session::put('otp', $secret);
            return json_encode(array(
                'status' => TRUE, 'message' => 'Login berhasil silahkan lanjutkan!'
            ));
        }else{
            return json_encode(array(
                'status' => FALSE, 'message' => 'OTP salah silahkan masukkan ulang!'
            ));
        }

    }

    public function firstlogin(){
        
        
        $secretKey = (new AuthLibrary())->generateRandomSecret();
        $QR_Image = (new AuthLibrary())->getQR(
            Auth::user()->email,
            $secretKey,
            config('app.name')
            
        );
        Session::put('key', $secretKey);
        return view('auth.scankey', ['QR_Image' => $QR_Image, 'secret' => $secretKey]);

    }

    // Jika OTP Salah Atau Redirect jika belum memasukkan key
    public function authenticate(){
        $id = Auth::id();
        $user = DB::table('crm_user')->where('id', $id)->first();
        if(!$user->key){
            return view('auth.verify', ['baru'=>'userbaru']);
        }else{
            return view('auth.verify', ['baru'=> null]);
        }
    }


    // Cek OTP 
    public function otpvalidate(Request $request){
        // dd($request->key);
        $secret = $request->key;
        $valid = (new AuthLibrary())->verifyCode(Session::get('key'), $secret, 2);
        
        if($valid){
            // Add Profile User to session
            $id = Auth::user()->id;
            $role = DB::table('crm_user')->where('crm_user.id', $id)
            ->join('crm_role', 'crm_role.id', '=' ,'crm_user.id_role')
            ->join('crm_divisi', 'crm_divisi.id', '=' ,'crm_role.id_divisi')->first();

            $id_role = Auth::user()->id_role;
            $permission = DB::table('crm_permission')->where('id_role', $id_role)->first();

            //get all menu and submenu
            $menu_cari = json_decode(json_encode(DB::table('crm_menu')->get(), true));
            $sub_menu_cari = json_decode(json_encode(DB::table('crm_sub_menu')->get(), true));

            //filter submenu
            if($permission->id_submenu){
                $permission_id = explode(",",$permission->id_submenu);
                $jumlah_id = count($permission_id);
                $sub_menu = [];
                for($id_sub_menu=0; $id_sub_menu <= $jumlah_id-1; $id_sub_menu++){
                    $res = array_search($permission_id[$id_sub_menu], array_column($sub_menu_cari, 'id'));
                    if($res !== false){
                        $sub_menu[$id_sub_menu] = $sub_menu_cari[$res];
                    }
                }
            }else{
                $sub_menu = null;
            }
            
            //filter menu
            $array = array_unique(array_column($sub_menu, 'id_menu'));
            sort($array);
            $menu = array();
            for($i = 0; $i < count($array); $i++){
                $index = array_search($array[$i], array_column($menu_cari, 'id'));
                array_push($menu, $menu_cari[$index]);
            }

            Session::forget('key');
            Session::put('menu', $menu);
            Session::put('sub_menu', $sub_menu);
            Session::put('permission', $permission);
            Session::put('dataUser', $role);
            Session::put('otp', $secret);
            return json_encode(array(
                'status' => TRUE, 'message' => 'Login berhasil silahkan lanjutkan!'
            ));
        }else{
            return json_encode(array(
                'status' => FALSE, 'message' => 'OTP salah silahkan masukkan ulang!'
            ));
        }
    }
}
