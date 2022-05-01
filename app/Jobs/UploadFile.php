<?php

namespace App\Jobs;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UploadFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;
    protected $fileName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function readCSV($csvFile, $array)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        fclose($file_handle);
        return $line_of_text;
    }

    public function removeNonUTF($string)
    {
        return iconv("UTF-8","UTF-8//IGNORE",$string);
        // return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        $this->queueData(['fileName' => $this->fileName]);
        $csvFile = public_path('files/' . $this->fileName);
        $text = $this->readCSV($csvFile,array('delimiter' => ','));
        // 0, 1, 2, 3, 28, 18, 14, 21
        //UNIQUE KEY
        //PRODUCT TITLE
        //PRODUCT DESC
        //STYLE
        //SANMAR
        //SIZE
        //COLOR_NAME
        //PRICE
        for ($i=1; $i < count($text) ; $i++) { 
            $file = File::where("unique_key", (string)$this->removeNonUTF($text[$i][0]))->first();
            if ($file) {
                $file->updated_at = Carbon::now();
                Log::channel('test')->info("UPDATED");
            }else{
                $file = new File();
                Log::channel('test')->info("NEW");
            }
            $file->unique_key = (string)$this->removeNonUTF($text[$i][0]);
            $file->product_title = (string)$this->removeNonUTF($text[$i][1]);
            $file->product_description = (string)$this->removeNonUTF($text[$i][2]);
            $file->style = (string)$this->removeNonUTF($text[$i][3]);
            $file->sanmar_mainframe_color = (string)$this->removeNonUTF($text[$i][28]);
            $file->size = (string)$this->removeNonUTF($text[$i][18]);
            $file->color_name = (string)$this->removeNonUTF($text[$i][14]);
            $file->piece_price = (string)$this->removeNonUTF($text[$i][21]);
            $file->save();
        }
    }
}
