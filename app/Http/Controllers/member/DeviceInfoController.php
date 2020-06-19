<?php

namespace App\Http\Controllers\member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\HelperInterface as Helper;
use DB;

// Model
use App\Http\Models\device_info_m;

class DeviceInfoController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){

        return view('page.member.device_info');
    }

    // Get Product
    public function getAllDeviceInfo(Request $request){

        $columns = array(
            0 =>'a.mbr_code',
            1 =>'a.mbr_name',
            2 =>'a.mbr_mobile'
        );


        // Total Data
        $data = DB::table('device_member')
                ->rightJoin('mbr_list','mbr_list.mbr_code','=','device_member.mbr_code')
                ->max('device_id');
        $totaldata = $data;
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->column)){


            $data = DB::select("SELECT count(*), a.mbr_code, b.mbr_mobile, b.mbr_name FROM device_member as a JOIN mbr_list as b ON b.mbr_code = a.mbr_code GROUP BY a.mbr_code, b.mbr_mobile, b.mbr_name");

            $posts = DB::select("SELECT count(*), a.mbr_code, b.mbr_mobile, b.mbr_name FROM device_member as a JOIN mbr_list as b ON b.mbr_code = a.mbr_code GROUP BY a.mbr_code, b.mbr_mobile, b.mbr_name LIMIT $limit OFFSET $start");


            $totaldata = count($data);
            $totalFiltered = $totaldata;

        } else {

            $kolom_search = $request->column;
            $kolom_value = $request->value;


            $data = DB::select("SELECT count(*), a.mbr_code, b.mbr_mobile, b.mbr_name FROM device_member as a JOIN mbr_list as b ON b.mbr_code = a.mbr_code
                    WHERE b.$kolom_search ilike '%$kolom_value%' GROUP BY a.mbr_code, b.mbr_mobile, b.mbr_name");
            $posts = DB::select("SELECT count(*), a.mbr_code, b.mbr_mobile, b.mbr_name FROM device_member as a JOIN mbr_list as b ON b.mbr_code = a.mbr_code
                    WHERE b.$kolom_search ilike '%$kolom_value%' GROUP BY a.mbr_code, b.mbr_mobile, b.mbr_name LIMIT $limit OFFSET $start");


            $totaldata = count($data);
            $totalFiltered = $totaldata;
        }

        //   $data_device = device_info_m::where('mbr_code',$row->mbr_code)->get();

          $data = array();

          if($posts){
              foreach($posts as $row){

                $data_device = device_info_m::where('mbr_code',$row->mbr_code)->orderBy('device_last_use', 'DESC')->get();
                $no = 1;
                $device='';
                foreach($data_device as $r){

                    if($no == 1){
                        $sts = '<span class="label label-success">Active</span>';
                    } else {
                        $sts = '<span class="label label-danger">Non Active</span>';
                    }


                    $result =
                    '
                        <table>
                        <tr>
                        <th><span style="font-weight:bold" class="mr-2 text-center">'.$no++.'.</span></th>
                        <th>
                        <span style="font-weight:bold" class="mr-2">Device Nama :</span>'.$r->device_name.'<br>
                        <span style="font-weight:bold" class="mr-2">Versi Aplikasi :</span>'.$r->devisi_versi.'<br>
                        <span style="font-weight:bold" class="mr-2">Versi OS :</span>'.$r->device_os.'<br>
                        <span style="font-weight:bold" class="mr-2">Last Use :</span>'.$r->device_last_use.'<br>
                        </th>
                        <th class="text-center">
                        '.$sts.'
                        </th>
                        </tr>
                        </table>
                    ';

                    $device .= $result;
                }

                $nestedData['mbr_code'] = '<span style="font-weight:bold" class="mr-2">ID EKL:</span>'.$row->mbr_code;

                $nestedData['detail'] = '<span style="font-weight:bold" class="mr-2">Nama :</span>'.$row->mbr_name.'<br>
                                        <span style="font-weight:bold" class="mr-2">Telepon :</span>'.$row->mbr_mobile;

                $nestedData['device'] = '<div class="table-wrapper-scroll-y my-custom-scrollbar">
                '.$device.'
            </div>';

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
}
