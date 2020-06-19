<?php

namespace App\Http\Controllers\user;

// use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\HelperInterface as Helper;
// use Illuminate\Support\Facades\Hash;
use DB;


class CreateMenuController{
    
    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    function index(){
        $menu = DB::table('crm_menu')->get();
        $menu1 = DB::table('crm_menu')->get();
        return view('page.user.create_menu', compact('menu','menu1'));
    }

    function getDataTable(Request $request){
        $columns = array(
            0 =>'crm_sub_menu.id',
            1 =>'crm_menu.id',
            2 =>'endpoint_sub_menu'
        );

        // Total Data
        $totaldata = count(DB::table('crm_sub_menu')->get());

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            // $count_sub_menu = count(DB::table('crm_sub_menu')->get());
            $posts = DB::table('crm_sub_menu')->select('crm_sub_menu.*','crm_menu.nama_menu','crm_menu.deskripsi')
                    ->rightJoin('crm_menu',  'crm_sub_menu.id_menu', '=','crm_menu.id' )
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;
        } else {
            // $count_sub_menu = count(DB::table('crm_sub_menu')->get());
            $search = $request->input('search.value');
            $data_search = DB::table('crm_sub_menu')->select('crm_sub_menu.*','crm_menu.nama_menu','crm_menu.deskripsi')
                    ->where('crm_sub_menu.id', 'like', "%{$search}%")
                    ->orWhere('crm_sub_menu.nama_submenu', 'like', "%{$search}%")
                    ->orWhere('crm_sub_menu.endpoint_sub_menu', 'like', "%{$search}%")
                    ->rightJoin('crm_menu',  'crm_sub_menu.id_menu', '=','crm_menu.id' )
                    ->orderBy($order, $dir);
                    $totalFiltered = count($data_search->get());
                    $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
        }
        // dd($posts);
        $data = array();
        if($posts){
            foreach($posts as $row){
                $nestedData['action'] ="<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#updateSubMenu' onClick=\"setSubMenu('$row->id','$row->nama_submenu','$row->id_menu', '$row->endpoint_sub_menu')\"><i class='mdi mr-2 mdi-settings' ></i>Ubah</button>
                                    <button class='btn ml-3 btn-danger btn-sm' onClick=\"deleteSubMenu('$row->id')\"><i class='mdi mr-2 mdi-close'></i>Hapus</button>";
                $nestedData['menu'] = '<span style="font-weight:bold" class="mr-2">Id Menu :</span>'.$row->id_menu.
                                    '<br/><span style="font-weight:bold" class="mr-2">Nama Menu :</span>'.$row->nama_menu.
                                    '<br/><span style="font-weight:bold" class="mr-2">Icon :</span>'.$row->deskripsi.'(<i class="'.$row->deskripsi.'"></i>)';
                $nestedData['sub_menu'] = '<span style="font-weight:bold" class="mr-2">Id Menu :</span>'.$row->id.
                                    '<br/><span style="font-weight:bold" class="mr-2">Nama Sub Menu :</span>'.$row->nama_submenu.
                                    '<br/><span style="font-weight:bold" class="mr-2">ROOT :</span>'.$row->endpoint_sub_menu;
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
    function getDataMenu(Request $request){
        $columns = array(
            0 =>'id',
            1 =>'nama_menu',
            2 =>'deskripsi'
        );


        // Total Data
        $totaldata = count(DB::table('crm_menu')->get());

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            // $count_sub_menu = count(DB::table('crm_sub_menu')->get());
            $posts = DB::table('crm_menu')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;
        } else {
            // $count_sub_menu = count(DB::table('crm_sub_menu')->get());
            $search = $request->input('search.value');
            $data_search = DB::table('crm_menu')
                    ->where('id', 'like', "%{$search}%")
                    ->orWhere('nama_menu', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
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
                $nestedData['action'] ="<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#updateMenu' onClick=\"setMenu('$row->id','$row->nama_menu','$row->deskripsi')\"><i class='mdi mr-2 mdi-settings' ></i>Ubah</button>
                                    <button class='btn ml-3 btn-danger btn-sm' onClick=\"deleteMenu('$row->id')\"><i class='mdi mr-2 mdi-close'></i>Hapus</button>";
                $nestedData['menu'] = $row->nama_menu;
                $nestedData['deskripsi'] = $row->deskripsi.'(<i class="'.$row->deskripsi.'"></i>)';
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

    function addMenu(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'deskripsi' => 'required'
        ]);
        $maxid = DB::table('crm_menu')->max('id');
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $res = DB::table('crm_menu')->insert([
                'id' => $maxid+1,
                'nama_menu' => $request->nama,
                'deskripsi' => $request->deskripsi
            ]);
            if($res){
                $this->helper->logAction(11,  $maxid+1, 'insert');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data Telah Ditambah!'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Ditambah!'
                ));
            }
        }    
    }

    function addSubMenu(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'menu' => 'required',
            'endpoint' => 'required'
        ]);

        $maxid = DB::table('crm_sub_menu')->max('id');
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $res = DB::table('crm_sub_menu')->insert([
                'id' => $maxid+1,
                'id_menu' => $request->menu,
                'nama_submenu' => $request->nama,
                'endpoint_sub_menu' => $request->endpoint
            ]);
            if($res){
                $this->helper->logAction(12,  $maxid+1, 'insert');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data Telah Ditambah!'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Ditambah!'
                ));
            }
        }  
    }
    function updateMenu(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'deskripsi' => 'required'
        ]);
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $res = DB::table('crm_menu')
            ->where('id', $request->id_menu)
            ->update([
                'nama_menu' => $request->nama,
                'deskripsi' => $request->deskripsi
            ]);
            if($res){
                $this->helper->logAction(11,  $request->id_menu, 'update');
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

    function updateSubMenu(Request $request){
        // dd($request->all());
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'menu' => 'required',
            'endpoint' => 'required'
        ]);
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $res = DB::table('crm_sub_menu')
            ->where('id', $request->id_submenu)
            ->update([
                'id_menu' => $request->menu,
                'nama_submenu' => $request->nama,
                'endpoint_sub_menu' => $request->endpoint
            ]);
            if($res){
                $this->helper->logAction(11,  $request->id_submenu, 'update');
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
    function deleteMenu($id){
        $menu = DB::table('crm_menu')->where('id', $id)->first();
        $res = DB::table('crm_menu')->where('id', $id)->delete();
        if($res){
            $this->helper->logAction(11, $id, 'delete');
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

    function deleteSubMenu($id){
        $sub_menu = DB::table('crm_sub_menu')->where('id', $id)->first();
        $res = DB::table('crm_sub_menu')->where('id', $id)->delete();
        if($res){
            $this->helper->logAction(12, $id, 'delete');
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
}