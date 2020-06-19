<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;

use App\Http\Models\crm_log_login_m;

class ManajemenLogLoginController{

    public function index(){
        return view('page.user.manajemen_log_login');
    }


    public function getData(Request $request){
        // Columns
        $columns = array(
            0 => 'A.id',
            1 => 'A.ip_address',
            2 => 'A.waktu',
            3 => 'A.user_agent'
        );

        // Total Data
        $totaldata = crm_log_login_m::count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){

            $totalFiltered = $totaldata;
            $posts = DB::table('crm_log_login AS A')
                    ->select('B.nama', 'A.ip_address', 'A.waktu', 'A.user_agent')
                    ->leftJoin('crm_user AS B', DB::raw('"A"."id_user"::integer'), '=', 'B.id')
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($limit)
                    ->get();
        }else{
            $search = $request->input('search.value');
            $data_search = DB::table('crm_log_login AS A')
                    ->select('B.nama', 'A.ip_address', 'A.waktu', 'A.user_agent')
                    ->leftJoin('crm_user AS B', DB::raw('"A"."id_user"::integer'), '=', 'B.id')
                    ->where('B.nama', 'ilike', "%{$search}%")
                    ->orwhere('A.ip_address', 'ilike', "%{$search}%")
                    ->orwhere('A.waktu', 'ilike', "%{$search}%")
                    ->orwhere('A.user_agent', 'ilike', "%{$search}%")
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
                $nestedData['nama'] = $row->nama;
                $nestedData['ip_address'] = $row->ip_address;
                $nestedData['waktu'] = $row->waktu;
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