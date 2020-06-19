<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Session;
use App\Http\Libraries\AuthLibrary;

use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function index(){
        return view('auth/login');
    }

    public function ceklogin(Request $request){
        if (Auth::attempt($request->only('email','password'))){

            date_default_timezone_set("Asia/Jakarta");
            $id = Auth::id();
            $date = DATE("Y/m/d H:i:s");
            $token = md5($date.$request->password);

            $user = DB::table('crm_user')->where('id', $id)->first();


            $agent =  new Agent();
            $browser = $agent->browser();
            $version_browser = $agent->version($browser);

            $platform = $agent->platform();
            $version_platform = $agent->version($platform);

            $log_login = [
                'id_user' => $id,
                'ip_address' => request()->ip(),
                'user_agent' => $browser.' '.$version_browser.' - '.$platform.' '.$version_platform,
                'waktu' => DATE("Y/m/d H:i:s")
            ];

            // Insert Log Login
            DB::table('crm_log_login')->insert($log_login);

            if(!$user->key){
                DB::table('crm_user')->where('id', $id)->update(['token' =>$token]);
                $secretKey = (new AuthLibrary())->generateRandomSecret();
                $QR_Image = (new AuthLibrary())->getQR(
                    Auth::user()->email,
                    $secretKey,
                    config('app.name')
                    
                );
                Session::put('key', $secretKey);
                return json_encode(array(
                    'status' => 'newuser',
                    'QR_Image' => $QR_Image,
                    'secret' => $secretKey
                ));
            }else{
                Session::put('key', Auth::user()->key);
                    DB::table('crm_user')->where('id', $id)->update(['token' =>$token]);
                return json_encode(array(
                    'status' =>'olduser'
                ));
            }
        }else{
            return json_encode(array(
                'status' => FALSE, 'message' => 'Username atau password anda salah!'
            ));
        }

    }


    // Function Logout
    public function logout(){
        Session::flush();
        Auth::logout();
        $res = $this->middleware('guest')->except('logout');
        if($res){
            // return response()->json([
            //     'status' => TRUE,
            //     'message' => 'Data telah terhapus'
            // ]);

            return redirect('/');
        }
    }

    // Function Logout
    public function logoutBaru(){
        Session::flush();
        Auth::logout();
        $res = $this->middleware('guest')->except('logout');
        if($res){
            return response()->json([
                'status' => TRUE,
                'message' => 'Data telah terhapus'
            ]);
        }

    }

}
