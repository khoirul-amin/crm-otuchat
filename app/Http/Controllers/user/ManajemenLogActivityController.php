<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;

use App\Http\Models\crm_activity_detail_m;

class ManajemenLogActivityController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.user.manajemen_log_activity');
    }

    public function getActivityDetail(Request $request){
        // Columns
        $columns = array(
            0 => 'id',
            1 => 'keterangan'
        );

        // Total Data
        $totaldata = crm_activity_detail_m::count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            $totalFiltered = $totaldata;

            $posts = crm_activity_detail_m::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');
            $data_search = crm_activity_detail_m::where('id', 'ilike', "%{$search}%")
                ->orWhere('keterangan', 'ilike', "%{$search}%")
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
                $nestedData['id'] = $row->id;
                $nestedData['keterangan'] = $row->keterangan;
                $nestedData['action'] = '<button class="btn btn-danger btn-sm" onClick="ubah(\'' . $row->id . '\', \'' . $row->keterangan . '\')"><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                    <button class="btn ml-2 btn-warning btn-sm" onClick="hapus(\'' . $row->id . '\')"><i class="mdi mr-2 mdi-close"></i>Hapus</button>
                ';

                $data[] = $nestedData;
            }
        }

        return response()->json(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totaldata),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data
        ));

    }

    public function tambahActivityDetail(Request $request){
        $keterangan = $request->input('keterangan');

        $data = new crm_activity_detail_m;
        $data->keterangan = $keterangan;
        $data->save();

        if($data){
            $this->helper->logAction(25, $keterangan, 'add');
            return response()->json(array(
                'status' => 'success',
                'reason' => 'berhasil tambah data'
            ));
        }
        return response()->json(array(
            'status' => 'failed',
            'reason' => 'kesalahan sistem, silahkan coba lagi'
        ));
    }

    public function ubahActivityDetail(Request $request){
        $id = $request->input('id');
        $keterangan = $request->input('keterangan');
        $status = crm_activity_detail_m::where('id', '=', $id)
            ->update(['keterangan' => $keterangan]);

        if($status){
            $this->helper->logAction(25, $keterangan, 'update');
            return response()->json(array(
                'status' => 'success',
                'reason' => 'data berhasil diubah'
            ));
        }
        return response()->json(array(
            'status' => 'failed',
            'reason' => 'kesalahan sistem, silahkan coba lagi'
        ));
    }

    public function hapusActivityDetail(Request $request){
        $id = $request->input('id');

        $status = crm_activity_detail_m::where('id', '=', $id)->delete();

        if($status){
            $this->helper->logAction(25, $keterangan, 'delete');
            return response()->json(array(
                'status' => 'success',
                'reason' => 'data berhasil dihapus'
            ));
        }
        return response()->json(array(
            'status' => 'failed',
            'reason' => 'kesalahan sistem, silahkan coba lagi'
        ));
    }

    public function getLogActivity(Request $request){
        // Columns
        $columns = array(
            0 => 'id_user',
            1 => 'ip_address',
            2 => 'waktu',
            3 => 'activity_detail',
            4 => 'mbr_code',
            5 => 'description',
            6 => 'user_agent'
        );

        // Total Data
        $totaldata = DB::table('crm_log_activity')->count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            $totalFiltered = $totaldata;

            $posts = DB::table('crm_log_activity AS A')
                ->select('B.nama', 'A.ip_address', 'A.waktu', 'C.keterangan', 'A.mbr_code', 'A.description', 'A.user_agent')
                ->join('crm_user AS B', DB::raw('"A"."id_user"::integer'), '=', 'B.id')
                ->join('crm_activity_detail AS C', 'A.activity_detail', '=', 'C.id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $data_search = DB::table('crm_log_activity AS A')
                ->select('B.nama', 'A.ip_address', 'A.waktu', 'C.keterangan', 'A.mbr_code', 'A.description', 'A.user_agent')
                ->join('crm_user AS B', DB::raw('"A"."id_user"::integer'), '=', 'B.id')
                ->join('crm_activity_detail AS C', 'A.activity_detail', '=', 'C.id')
                ->where('B.nama', 'ilike', "%{$search}%")
                ->orwhere('A.ip_address', 'ilike', "%{$search}%")
                ->orwhere('A.waktu', 'ilike', "%{$search}%")
                ->orwhere('C.keterangan', 'ilike', "%{$search}%")
                ->orwhere('A.mbr_code', 'ilike', "%{$search}%")
                ->orwhere('A.description', 'ilike', "%{$search}%")
                ->orwhere('A.user_agent', 'ilike', "%{$search}%");

            $totalFiltered = count($data_search->get());
            $posts = $data_search
                ->offset($start)
                ->limit($limit)
                ->get();
        }

        $data = array();
        if($posts){
            foreach($posts as $row){
                $nestedData['nama'] = $row->nama;
                $nestedData['ip_address'] = $row->ip_address;
                $nestedData['waktu'] = $row->waktu;
                $nestedData['keterangan'] = $row->keterangan;
                $nestedData['mbr_code'] = $row->mbr_code;
                $nestedData['description'] = $row->description;
                $nestedData['user_agent'] = $row->user_agent;

                $data[] = $nestedData;
            }
        }

        return response()->json(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totaldata),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data
        ));
    }
}
