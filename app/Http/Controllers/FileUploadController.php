<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\UploadFile as UploadFileJob;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
   
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('files'),$fileName);
        
        Log::channel('test')->info("starting job");
        dispatch(new UploadFileJob($fileName));
        Log::channel('test')->info("finish job");
   
        return response()->json(['success'=>$fileName]);
    }
}
