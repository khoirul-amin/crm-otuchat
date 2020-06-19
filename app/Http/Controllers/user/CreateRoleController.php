<?php

namespace App\Http\Controllers\user;

// use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\HelperInterface as Helper;
// use Illuminate\Support\Facades\Hash;
use DB;


class CreateRoleController{
    
    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    function index(){

        $divisi = DB::table('crm_divisi')->get();
        $divisi1 = DB::table('crm_divisi')->get();
        $menu = DB::table('crm_menu')->get();
        $sub_menu = DB::table('crm_sub_menu')->get();

        return view('page.user.create_role', compact('divisi', 'divisi1', 'menu', 'sub_menu'));
    }

    function getDataTable(Request $request){
        // dd($request->column);
        $columns = array(
            0 =>'id',
            1 =>'nama_role',
            2 =>'id_divisi'
        );

        // Total Data
        $totaldata = count(DB::table('crm_role')->get());

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');


        if (empty($request->input('search.value'))){
            $count_sub_menu = count(DB::table('crm_sub_menu')->get());
            $posts = DB::table('crm_role')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;
        } else {
            $count_sub_menu = count(DB::table('crm_sub_menu')->get());
            $search = $request->input('search.value');
            $data_search = DB::table('crm_role')
                    ->where('id', 'like', "%{$search}%")
                    ->orWhere('nama_role', 'like', "%{$search}%")
                    ->orWhere('id_divisi', 'like', "%{$search}%")
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
                $divisi = DB::table('crm_divisi')->where('id', $row->id_divisi)->first();
                $permission = DB::table('crm_permission')->where('id_role', $row->id)->first();

                if($permission){
                    $user_permission = $permission->id_submenu;
                }else{
                    $user_permission = '';
                }
                $nestedData['nama'] = $row->nama_role;
                $nestedData['divisi'] = $divisi->nama_divisi;
                $nestedData['action'] = "<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#updateRole' onClick=\"getRoleID('$row->id')\"><i class='mdi mr-2 mdi-settings' ></i>Ubah</button>
                                <button class='btn ml-3 btn-danger btn-sm' onClick=\"deleteRole('$row->id')\"><i class='mdi mr-2 mdi-close'></i>Hapus</button>
                                <button class='btn ml-3 btn-info btn-sm' data-toggle='modal' data-target='#kelolaMenu' onClick=\"getSubMenu('$user_permission','$row->id','$count_sub_menu')\"><i class='mdi mr-2 mdi-plus'></i>Kelola Menu</button>";
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

    function getRoleID($id){
        $res = DB::table('crm_role')->where('id', $id)->first();

        return response()->json($res);
    }
    function getPermissionByID($id){


        // $id_role = Auth::user()->id_role;
        // $count_sub_menu = count(DB::table('crm_sub_menu')->get());
        $permission = explode(",",$id);
        $sub_menu_cari = json_decode(json_encode(DB::table('crm_sub_menu')->get(), true));
        $jumlah_sub_menu = count($sub_menu_cari);
        $jumlah_id = count($permission);


        $sub_menu = [];

        for($id_sub_menu=0; $id_sub_menu <= $jumlah_sub_menu; $id_sub_menu++){
            if(!empty($permission[$id_sub_menu])){
                $res = array_search($permission[$id_sub_menu], array_column($sub_menu_cari, 'id'));
                if($res !== false){
                    $sub_menu[$id_sub_menu] = $sub_menu_cari[$res];
                }else{
                    $sub_menu[$id_sub_menu] = null;
                }
            }else{
                $sub_menu[$id_sub_menu] = null;
            }
            
        }

        // for($sub_id = 0 ; $sub_id <= $count_sub_menu+1 ; $sub_id++){
        //     if(empty($permission_id[$sub_id])){
        //         $sub_menu[$sub_id] = null;
        //     }else{
        //         $sub_menu[$sub_id] = DB::table('crm_sub_menu')->where('id', $permission_id[$sub_id])->first();
        //     }
        // }

        // dd($sub_menu);
        return response($sub_menu);
    }

    function addRole(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'divisi' => 'required',
        ]);
        $idprev = DB::table('crm_role')->max('id');
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $res = DB::table('crm_role')->insert(['id' => $idprev+1,'nama_role' => $request->nama, 'id_divisi' => $request->divisi]);

            if($res){
                $this->helper->logAction(7, $request->nama, 'insert');
            }
            return json_encode(array(
                'status' => TRUE, 'message' => 'Data berhasil ditambah'
            ));
        }
    }

    function deleteRole($id){
        $role = DB::table('crm_role')->where('id', $id)->first();
        $res = DB::table('crm_role')->where('id', $id)->delete();

        if($res){
            $this->helper->logAction(7, $role->nama_role, 'delete');
            return response()->json([
                'status' => TRUE,
                'message' => 'Data telah terhapus'
            ]);
        }else{
            return response()->json([
                'status' => FALSE,
                'message' => 'Data gagal terhapus'
            ]);
        }
    }

    function addPermission(Request $request){
        $count_sub_menu = count(DB::table('crm_sub_menu')->get());
        $permission = false;
        for($i=1; $i <= $count_sub_menu+1; $i++ ){
            if(!$permission){
                $koma = '';
            }else{
                $koma = ',';
            }
            if($request->$i){
                $permission = $permission.$koma.$request->$i;
            }
        }
        $role = DB::table('crm_permission')->where('id_role', $request->id)->first();
        if($role){
            $res = DB::table('crm_permission')->where('id_role', $request->id)->update(['id_submenu' => $permission]);
            if($res){
                $this->helper->logAction(7, $request->nama, 'update permission');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data berhasil diubah'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data gagal diubah'
                ));
            }
        }else{
            $max = DB::table('crm_permission')->max('id');
            $res = DB::table('crm_permission')->insert(['id' => $max+1,'id_role' => $request->id,'id_submenu' => $permission]);
            if($res){
                $this->helper->logAction(7, $request->nama, 'update permission');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data berhasil diubah'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data gagal diubah'
                ));
            }
        } 
    }

    function updateRole(Request $request){
        // dd($request->all());
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'divisi' => 'required',
        ]);
        // $idprev = DB::table('crm_role')->max('id');
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $res = DB::table('crm_role')->where('id', $request->id)->update(['nama_role' => $request->nama, 'id_divisi' => $request->divisi]);

            if($res){
                $this->helper->logAction(7, $request->nama, 'update');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data berhasil diubah'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data gagal diubah'
                ));
            }
        }
    }
}

