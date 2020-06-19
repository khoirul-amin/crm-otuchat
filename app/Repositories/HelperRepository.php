<?php namespace App\Repositories;

use App\Mail\Mailtrap;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Http\Libraries\AuthLibrary;

use Auth;

class HelperRepository implements HelperInterface
{

    // Send Email
    public function sendEmail($to, $subject, $view, $param){
        try{
            Mail::to($to)->send(new Mailtrap($subject, $view, $param));
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    // Cek Key Authenticator
    public function cekKeyAuthenticator($secret){
        $keySession = Session::get('dataUser')->key;
        $keyCecked = (new AuthLibrary())->verifyCode($keySession, $secret,2);
        
        return $keyCecked;
    }

    // Cek User Permission
    public function cekUserPermission($id_permission){
        $permission = Session::get('permission');
        $data = array_search($id_permission,explode(",",$permission->id_submenu), false);
        return $data;
    }

    // Log Action
    public function logAction($id_activity_detail = "", $to = "", $desc = ""){
        // Get User Agent
        $agent =  new Agent();
        $browser = $agent->browser();
        $version_browser = $agent->version($browser);
        $platform = $agent->platform();
        $version_platform = $agent->version($platform);

        // Data yang di insert ke database
        $id_user = Auth::user()->id;
        $ip = request()->ip();
        date_default_timezone_set("Asia/Jakarta");
        $time = DATE("Y/m/d H:i:s");
        $user_agent = $browser.' '.$version_browser.' - '.$platform.' '.$version_platform;

        DB::table('crm_log_activity')->insert([
            'id_user' => $id_user,
            'ip_address' => $ip,
            'waktu' => $time,
            'activity_detail' => $id_activity_detail,
            'mbr_code' => $to,
            'description' => $desc,
            'user_agent' => $user_agent
        ]);
    }
}
