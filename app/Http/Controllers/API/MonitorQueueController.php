<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function GuzzleHttp\Promise\queue;

class MonitorQueueController extends Controller
{
    public function index(Request $request) 
    {
        $queues = DB::table('queue_monitor')->get();

        $response = [];
        foreach ($queues as $data) {
            $filename = json_decode($data->data);
            $status = "success";
            if($data->failed == 1) {
                $status = "failed";
            }else if($data->finished_at != null) {
                $status = "completed";
            }else{
                $status = "processing";
            }
            $transformData = array("time" => Carbon::createFromTimeStamp(strtotime($data->started_at))->diffForHumans(), "filename" => $filename ? $filename->fileName : '', "status"=>$status);
            array_push($response, $transformData);
        }
        return $response;
    }
}
