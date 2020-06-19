<?php

namespace App\Http\Controllers\user;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\HelperInterface as Helper;
use Illuminate\Support\Facades\Hash;
use DB;

// use App\Http\Models\log_uang_m;

class ListUserController{
    
    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        $role = DB::table('crm_role')->get();
        $role1 = DB::table('crm_role')->get();
        return view('page.user.list_user', compact('role', 'role1'));
    }

    public function getDataUser(Request $request){


        $columns = array(
            0 => 'id',
            1 => 'nama',
            2 => 'email',
            3 => 'id_role'
        );

        // Total Data
        $totaldata = User::count();

         // Limit
         $limit = $request->input('length');
         $start = $request->input('start');
         $order = $columns[$request->input('order.0.column')];
         $dir   = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $posts = DB::table('crm_user')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;
        }else{
            $search = $request->input('search.value');
            $data_search = DB::table('crm_user')
                    ->where('id', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    // ->orWhere('id_role', 'like', "%{$search}%")
                    ->orderBy($order, $dir);
                    $totalFiltered = count($data_search->get());
                    $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
        }



        $data = array();
        if($posts){
            foreach($posts as $row){

                $role = DB::table('crm_role')->where('id', $row->id_role)->first();
                // $permission = DB::table('crm_permission')->where('id_role', $row->id_role)->first();

                $nestedData['nama'] = '<span style="font-weight:bold" class="mr-2">Nama :</span>'.$row->nama;
                $nestedData['email'] = '<span style="font-weight:bold" class="mr-2">Email :</span>'.$row->email;
                $nestedData['role'] = '<span style="font-weight:bold" class="mr-2">Id Role :</span>'.$role->id.
                                    '<br/><span style="font-weight:bold" class="mr-2">Nama Role :</span>'.$role->nama_role;
                                    // '<span style="font-weight:bold" class="mr-2">Nama Divisi :</span>'.$row->id;
                $nestedData['action'] = '<button class="btn btn-warning btn-sm"data-toggle="modal" data-target="#updateUser" onClick="getUserById(\'' . $row->id . '\')"><i class="mdi mr-2 mdi-settings" ></i>Ubah</button>
                                <button class="btn ml-3 btn-danger btn-sm" onClick="deleteUser(\'' . $row->id . '\')"><i class="mdi mr-2 mdi-close"></i>Hapus</button>
                                <button class="btn ml-3 btn-success btn-sm" onClick="resetAuth(\'' . $row->id . '\')"><i class="mdi mr-2 mdi-loop"></i>Reset Auth</button>
                                <button class="btn ml-3 btn-info btn-sm" onClick="resetPassword(\'' . $row->id . '\')"><i class="mdi mr-2 mdi-close"></i>Reset Password</button>';
                $data[] = $nestedData;
            }
        }

        echo json_encode(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totaldata),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data
        ));
    }



    function addDataUser(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required'
        ]);
        
        $max_id = DB::table('crm_user')->max('id');
        // dd($max_id+1);
        $password = Hash::make($request->password);

        $to = $request->email;
        $subject = 'WELCOME TO CRM OTUCHAT';
        $view = 'mails.mail_users';
        $param = array(
            "email" => $request->email,
            "password" => $request->password
        );

        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $insert = DB::table('crm_user')->insert([
                'id' => $max_id+1,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $password,
                'id_role' => $request->role
            ]);
            if($insert){
                $res = $this->helper->sendEmail($to, $subject, $view, $param);
                $this->helper->logAction(6, $request->nama, 'insert');
                if($res){
                    return json_encode(array(
                        'status' => TRUE, 'message' => 'Data Telah Ditambah!'
                    ));
                }else{
                    return json_encode(array(
                        'status' => TRUE, 'message' => 'Data Telah Ditambah, Email gagal terkirim!'
                    ));
                }
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Ditambah!'
                ));
            }

            // $this->helper->logAction(6, $request->nama, 'insert');
        }
    }

    function deleteDataUser(Request $request){
        $key = $this->helper->cekKeyAuthenticator($request->key);
        if($key){
            $data = DB::table('crm_user')->where('id',$request->id)->first();
            $res = DB::table('crm_user')->where('id',$request->id)->delete();
            
            if($res){
                $this->helper->logAction(6, $data->nama, 'delete');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data Telah Terhapus!'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Dihapus!'
                ));
            }
        }else{
            return json_encode(array(
                'status' => FALSE, 'message' => 'Key tidak valid !'
            ));
        }
        
    }

    function updateUser(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'email' => 'required',
            // 'password' => 'required',
            'role' => 'required'
        ]);

        // $user = DB::table('crm_user')->where('id', $request->id)->first();
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $update_user = DB::table('crm_user')->where('id', $request->id)->update([
                "nama" => $request->nama,
                "email" => $request->email,
                "id_role" => $request->role
            ]);
            if($update_user){
                $this->helper->logAction(6, $request->nama, 'update');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data Telah Diubah!'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Diubah!'
                ));
            }
        }
    }

    function getUserById($id){
        $user = DB::table('crm_user')->where('id', $id)->first();
        // dd($user);
        if($user){
            return response()->json($user);
        }
    }

    function resetAuth($id){
        $res = DB::table('crm_user')->where('id', $id)->update(['key' => '']);
        $user = DB::table('crm_user')->where('id', $id)->first();

        if($res){
            $this->helper->logAction(6, $user->nama, 'reset key');
            return response()->json([
                'status' => TRUE,
                'message' => 'Data Berhasil Direset!'
            ]);
        }else{
            return response()->json([
                'status' => FALSE,
                'message' => 'Data Gagal Direset!'
            ]);
        }
    }

    function resetPassword(Request $request){
        // dd($request->all());
        $password = Hash::make($request->password);

        $update_user = DB::table('crm_user')->where('id', $request->id)->update([ "password" => $password ]);

        if($update_user){
            $this->helper->logAction(6, $request->id, 'reset password');
            return response()->json([
                'status' => TRUE,
                'message' => 'Password Berhasil Dirubah!'
            ]);
        }else{
            // $this->helper->logAction(6, $user->nama, 'reset key');
            return response()->json([
                'status' => TRUE,
                'message' => 'Password gagal dirubah!'
            ]);
        }
    }
}
