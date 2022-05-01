<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function GuzzleHttp\Promise\queue;

class MonitorQueueController extends Controller
{
    public function getProtectedValue($obj, $name) {
        $array = (array)$obj;
        $prefix = chr(0).'*'.chr(0);
        return $array[$prefix.$name];
    }
    
    public function index(Request $request) 
    {
        $queues = DB::table('queue_monitor')->get();
        $pendings = DB::table('jobs')->get();

        $response = [];
        $not_pending_queue = [];
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
            array_push($not_pending_queue, $data->job_id);
        }
        
        Log::channel('test')->info(json_encode($not_pending_queue));
        foreach ($pendings as $pending) {
            
            if(in_array($pending->id, $not_pending_queue) == false) {
                $payload = json_decode( $pending->payload );
                $payload_pending = unserialize( $payload->data->command );

                $transformData = array("time" => Carbon::createFromTimeStamp($pending->created_at)->diffForHumans(), "filename" => $this->getProtectedValue($payload_pending, "fileName"), "status"=>"pending");
                array_push($response, $transformData);
            }
        }

        return $response;
    }
}
