<?php

namespace App\Http\Controllers\member;

use Illuminate\Http\Request;
use App\Http\Models\log_login_m;
use App\Http\Models\mbr_list_m;

use App\Repositories\HelperInterface as Helper;

use DB;

class LogLoginMemberController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.member.log_login_member');
    }

    public function getAllLogLoginMember(Request $request){
        // print_r($request->all());
        // Columns
        $columns = array(
            0 => 'mbr_code',
            1 => 'mbr_name',
            2 => 'mbr_mobile',
            3 => 'last_login'
        );

        $custom_search = $request->input('data_search');
        // Total Data
        $sub = log_login_m::from('log_login AS A')
            ->select('A.'.$columns[0])
            ->join('mbr_list AS B', 'A.'.$columns[0], '=', 'B.'.$columns[0])
            ->groupBy('A.'.$columns[0]);
        $totaldata = DB::table( DB::raw("({$sub->toSql()}) as pg_sucks") )
            ->mergeBindings($sub->getQuery())
            ->count($columns[0]);
        // if($custom_search === 'default'){
        //     $totaldata = log_login_m::distinct('mbr_code')->count('mbr_code');
        // }
        // else{
        //     //SEARCH BY MBR CODE
        //     if($custom_search['custom_search'] === '1'){
        //         $totaldata = DB::table('log_login AS A')
        //         ->select('A.mbr_code')
        //         ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code')
        //         ->where('A.mbr_code', 'ilike', "%{$custom_search['keyword']}%")
        //         ->count();
        //     }
        //     //SEARCH BY NAMA MEMBER
        //     else if($custom_search['custom_search'] === '2'){
        //         $totaldata = DB::table('log_login AS A')
        //         ->select('A.mbr_name')
        //         ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code')
        //         ->where('A.mbr_name', 'ilike', "%{$custom_search['keyword']}%")
        //         ->count();
        //     }
        //     //SEARCH BY NO HP
        //     else{
        //         $totaldata = DB::table('log_login AS A')
        //         ->select('A.mbr_mobile')
        //         ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code')
        //         ->where('A.mbr_mobile', 'ilike', "%{$custom_search['keyword']}%")
        //         ->count();
        //     }
        // }
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            $posts = log_login_m::from('log_login AS A')->select(
                'B.'.$columns[0],
                'B.'.$columns[1],
                'B.'.$columns[2],
                DB::raw("MAX(log_date) AS " . $columns[3])
            )
            ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code')
            ->groupBy('B.'.$columns[0]);
            if($custom_search === 'default'){
                $posts = $posts->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
                $totalFiltered = $totaldata;
            }
            else{
                //SEARCH BY MBR CODE
                if($custom_search['custom_search'] === '1'){
                    $data_search = $posts->where('A.mbr_code', 'ilike', "%{$custom_search['keyword']}%");
                    $totalFiltered = count($data_search->get());
                }
                //SEARCH BY NAMA MEMBER
                else if($custom_search['custom_search'] === '2'){
                    $data_search = $posts->where('B.mbr_name', 'ilike', "%{$custom_search['keyword']}%");
                    $totalFiltered = count($data_search->get());
                }
                //SEARCH BY NO HP
                else{
                    $data_search = $posts->where('B.mbr_mobile', 'ilike', "%{$custom_search['keyword']}%");
                    $totalFiltered = count($data_search->get());
                }
                $posts = $data_search->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            }
        } else {
            $search = $request->input('search.value');
            $data_search = log_login_m::table('log_login AS A')->select(
                'B.'.$columns[0],
                'B.'.$columns[1],
                'B.'.$columns[2],
                DB::raw("MAX(log_date) AS " . $columns[3])
            )
            ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code')
            ->groupBy('B.'.$columns[0]);

            if($custom_search != 'default'){
                //SEARCH BY MBR CODE
                if($custom_search['custom_search'] === '1'){
                    $data_search = $data_search->where('A.mbr_code', 'ilike', "%{$custom_search['keyword']}%");
                    $totalFiltered = count($data_search->get());
                }
                //SEARCH BY NAMA MEMBER
                else if($custom_search['custom_search'] === '2'){
                    $data_search = $data_search->where('B.mbr_name', 'ilike', "%{$custom_search['keyword']}%");
                    $totalFiltered = count($data_search->get());
                }
                //SEARCH BY NO HP
                else{
                    $data_search = $data_search->where('B.mbr_mobile', 'ilike', "%{$custom_search['keyword']}%");
                    $totalFiltered = count($data_search->get());
                }
            }

            $data_search = $data_search->where(function($query) use ($columns, $search)
            {
                $query->orWhere('A.'.$columns[0], 'ilike', "%{$search}%")
                ->orWhere('B.'.$columns[1], 'ilike', "%{$search}%")
                ->orWhere('B.'.$columns[2], 'ilike', "%{$search}%");
                // ->orWhere(DB::raw('MAX(log_date)'), 'ilike', "%{$search}%");
            })
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
                $nestedData['mbr_code'] = $row->mbr_code;
                $nestedData['mbr_name'] = $row->mbr_name;
                $nestedData['mbr_mobile'] = $row->mbr_mobile;
                $nestedData['last_login'] = $row->last_login;
                $nestedData['action'] = '<button class="btn btn-info btn-sm" onClick="detailLogLoginMember(\'' . $row->mbr_code . '\')" id="detail"><i class="mdi mr-2 mdi-eye"></i>Lihat Detail</button>';

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

    public function getDetailLogLoginMember(Request $request){
        // print_r($request->all());
        // Columns
        $columns = array(
            0 => 'log_date',
            1 => 'ip',
            2 => 'log_desc',
            3 => 'device'
        );

        $mbr_code = $request->input('mbr_code');

        $custom_search = $request->input('data_search');
        // Total Data
        $totaldata = log_login_m::where('mbr_code', '=', $mbr_code)->count('mbr_code');

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            $posts = log_login_m::select($columns[0], $columns[1], $columns[2], $columns[3])
                ->where('mbr_code', '=', $mbr_code)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $totaldata;

        } else {
            $search = $request->input('search.value');
            $data_search = log_login_m::select($columns[0], $columns[1], $columns[2], $columns[3])
                ->where('mbr_code', '=', $mbr_code);
            $data_search = $data_search->where(function($query) use ($columns, $search)
            {
                $query->orWhere($columns[0], 'ilike', "%{$search}%")
                ->orWhere($columns[1], 'ilike', "%{$search}%")
                ->orWhere($columns[2], 'ilike', "%{$search}%")
                ->orWhere($columns[3], 'ilike', "%{$search}%");
            })
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
                $nestedData['log_date'] = $row->log_date;
                $nestedData['ip'] = $row->ip;
                $nestedData['log_desc'] = $row->log_desc;
                $nestedData['device'] = $row->device;

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
