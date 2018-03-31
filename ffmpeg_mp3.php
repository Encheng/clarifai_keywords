<!--
目的:轉檔(wav轉mp3)並壓浮水音
步驟:
1.第一層foreach 讀取路徑下的所有資料夾{
    2.第二層foreach 一一讀去資料夾中的檔案{
        3.將wav轉mp3
        4.用轉好的mp3去壓浮水音，每10秒一個
    }
}
-->
<?php
include_once('ffprobe.php');
include_once ('ffprobe_ext.php');
set_time_limit(0);
ini_set('memory_limit', '2048M');


//要有可以輸入input路徑跟output路徑

//路徑 預設E:/var/www/html/cute_pic/
$mypath_out = !empty($_POST['filePath'])?$_POST['filePath']:'/var/www/html/ffmpeg_mp3/';
//因為有可能有中文路徑，需要轉碼讀的到檔案
$mypath_out = iconv("utf-8","big5",$mypath_out);

//只獲取資料夾下的子資料夾，不包含檔案
$file_dir = glob($mypath_out.'*',GLOB_ONLYDIR);
foreach($file_dir as $directory)
{
    echo 'Directory name: ' . $directory . '<br />';
    $mypath = $directory.'/';

//var_dump($mypath);exit;
//抓取路徑下的所有(wav)檔名，並放入陣列
$file_name = glob($mypath.'*.{wav,mp3}', GLOB_BRACE);
//var_dump($file_name);exit;


$a=1;
$aa=0;
 foreach ($file_name as $image_key => $name) {
 	//去除路徑，取得此檔案名稱
 	$path_parts = pathinfo($name);
 	$file_name_one = $path_parts['basename']; //檔名+附檔名
	$file_only_name = $path_parts['filename']; //檔名
	$file_only_path = $path_parts['dirname']; //只有路徑
    $file_only_ext = $path_parts['extension']; //只有副檔名
    $aa++;

	//本機連線測試
	if(file_exists($mypath.$file_name_one)){
		$file_path = $mypath.$file_name_one;
	}else{
		$file_path = $mypath.$file_name_one;
	}


//    $arr1 = explode('/',$directory);
//    $lose = $arr1[5];

    //生成mp3,(有可能誤抓資料夾檔名是.mp3)
    if($file_only_ext === 'wav'){
     exec('ffmpeg -i "'.$file_only_path.'/'.$file_name_one.'" -f wav - | lame - "ffmpeg_output/mp3_no_water/'.$file_only_name.'.mp3"');
    }elseif($file_only_ext === 'mp3'){
     exec('cp "'.$file_only_path.'/'.$file_name_one.'" "ffmpeg_output/mp3_no_water/'.$file_only_name.'.mp3"');
    }

 	//開始切縮圖
	if($file_path != ""){
		//取得影片資訊
        $info = ffprobe_ext::getVideoInfo($file_path);
        $file_length = round($info->duration,2);

        //加入浮水音
        //get_graph_convert(檔案路徑(www/html),路徑+檔名,輸出的檔名,檔案長度)
        $file_source_mp3 = 'ffmpeg_output/mp3_no_water';
        $file_path_mp3 = 'ffmpeg_output/mp3_no_water/'.$file_only_name.'.mp3';
        $arr = explode('/',$directory);
        $mp3_with_watermark_name = $arr[5];
//var_dump($file_source_mp3);var_dump($file_path_mp3);var_dump($mp3_with_watermark_name);exit;
        get_graph_convert($file_source_mp3,$file_path_mp3,$mp3_with_watermark_name,$file_length);

        var_dump($name);
	}else{
		echo 'error,path wrong!';
	}


 }

}

var_dump("完成切割");



function get_graph_convert($path,$file_path,$single_id,$file_length) {
    $file_name = basename($file_path);
    $ex_file_name = explode('.', $file_name);

    $STORAGE_DIR = 'ffmpeg_output';
    $WATERMARK_SOUND = 'WATERMARK_SOUND.mp3';
    $WATERMARK_FILE = 'WATERMARK1.png';

    if(isset($ex_file_name[0]) && isset($ex_file_name[1])){
        //var_dump($file_path);var_dump($path);exit;
        if($ex_file_name[1]==='avi' || $ex_file_name[1]==='mp4' || $ex_file_name[1]==='AVI'){
            //exec('chmod 777 ' . $file_path);
            $target_mp4_path = $STORAGE_DIR . '/mp4';
            exec('ffmpeg -i "'.$path.'/'.$file_name.'" -i '. $WATERMARK_FILE .' -filter_complex "overlay=(W-w)/2:(H-h)/2" '.$target_mp4_path.'/'.$single_id.'.mp4');
            //exec($exec);
            exec('ffmpeg -ss 0.5 -i "'.$path.'/'.$file_name.'" -t 1 '.$path.'/'.$ex_file_name[0].'.jpg');
            //exec($exec);

            //將PPT封面切圖、浮水印
            $o_p_filename=$ex_file_name[0].'.jpg';
            $o_p_rename=$single_id.'.jpg';
            $target_p_path = $STORAGE_DIR . '/p';
            exec('convert -strip -density 72 -geometry 150x150 "' . $path . '/' . $o_p_filename . '" "' . $target_p_path . '/' . $o_p_rename . '"');
            //o
            $target_o_path = $STORAGE_DIR . '/o';
            exec('convert -strip -density 72 -geometry 500x500 "' . $path . '/' . $o_p_filename . '" "' . $target_o_path . '/' . $o_p_rename . '"');
            exec('composite -dissolve 50% -gravity center "' . $WATERMARK_FILE . '" "' . $target_o_path . '/' . $o_p_rename . '" "' . $target_o_path . '/' . $o_p_rename . '"');
            //$this->build_o_p($path, $o_p_filename, $o_p_rename);
        }else if($ex_file_name[1] === 'wav' || $ex_file_name[1] === 'mp3'){
            $target_mp3_path = $STORAGE_DIR . '/mp3';
            $target_o_path = $STORAGE_DIR . '/o';
            $file_total=$file_length;

            if($file_total>10){
                //檔案原始長度
                $file_length_original = $file_length;
                //計算秒數(floor無條件捨去)
                $file_length=floor($file_length/10);
                //實際除以10後的長度，如果剛好整除，最後一段就不需再壓浮水音
                $file_length_10 = $file_length_original%10;


                for($i=0;$i<=$file_length;$i++){
                    $I=$i*10;
                    $II=$I-10;

                    //切割原始音樂檔 $I從0開始($I是影片總共要切幾個檔案) 將檔案以10秒分成一個檔案
                    exec('ffmpeg -i "'.$path.'/'.$file_name.'" -ss ' .$I. ' -t 10 -y -f wav - | lame - '.$path.'/'.$I.'_out.mp3');
                    //將切出來的檔案附上浮水音，0_out.mp3為上行指令切出來的10秒檔案，合成浮水音後變成remix0.mp3
                    exec('ffmpeg -i '.$path.'/'.$I.'_out.mp3 -i ' . $WATERMARK_SOUND . ' -filter_complex amix=inputs=2:duration=first:dropout_transition=2 -f wav - | lame - '.$path.'/remix'.$I.'.mp3');
                    //當$i==1時候，代表有兩個10秒浮水的檔案，此時將remix0.mp3跟remix10.mp3合成為success10.mp3
                    if($i==1){
                        exec('ffmpeg -i "concat:'.$path.'/remix'.$II.'.mp3|'.$path.'/remix'.$I.'.mp3" -acodec copy -y '.$path.'/success'.$I.'.mp3');
                        if($i==$file_length){
                            exec('cp '.$path.'/success'.$I.'.mp3 '.$target_mp3_path.'/'.$single_id.'.'.$ex_file_name[1]);
                            exec('rm -f '.$path.'/success'.$I.'.mp3');
                        }
                        exec('rm -f '.$path.'/remix'.$I.'.mp3');
                        exec('rm -f '.$path.'/remix'.$II.'.mp3');
                    }//當$i>1時候，代表有1個合成20秒的浮水音檔案success10.mp3，此時要跟remix20.mp3(10秒的)合成為success20.mp3
                    else if($i>1 && $i<$file_length){
//                        exec('ffmpeg -i "concat:'.$path.'/success'.$II.'.mp3|'.$path.'/remix'.$I.'.mp3" -acodec copy -y '.$path.'/success'.$I.'.mp3');
//                        exec('rm -f '.$path.'/remix'.$I.'.mp3');
//                        exec('rm -f '.$path.'/success'.$II.'.mp3');
                        //判斷檔案長度是否剛好被10整除(E.G. 50秒)(整除10的檔案，長度30秒以上會在這裡處理)
                        if($file_length_10 == 0 && $i == (int)$file_length-1){
                            exec('ffmpeg -i "concat:'.$path.'/success'.$II.'.mp3|'.$path.'/remix'.$I.'.mp3" -acodec copy -y "'.$target_mp3_path.'/'.$single_id.'.'.$ex_file_name[1].'"');
                            exec('rm -f '.$path.'/remix'.$I.'.mp3');
                            exec('rm -f '.$path.'/success'.$II.'.mp3');
                        }else{
                            exec('ffmpeg -i "concat:'.$path.'/success'.$II.'.mp3|'.$path.'/remix'.$I.'.mp3" -acodec copy -y '.$path.'/success'.$I.'.mp3');
                            exec('rm -f '.$path.'/remix'.$I.'.mp3');
                            exec('rm -f '.$path.'/success'.$II.'.mp3');
                        }
                    }
                    else if($i>1 && $i==$file_length){
                        if($i>2 && $file_length_10 == 0){
                            //當最後一段檔案被10整除時，不動作，因為已經在上一個判斷輸出結果(不包括剛好為20秒的檔案)
                        }else{
                            exec('ffmpeg -i "concat:'.$path.'/success'.$II.'.mp3|'.$path.'/remix'.$I.'.mp3" -acodec copy -y "'.$target_mp3_path.'/'.$single_id.'.'.$ex_file_name[1].'"');
                            exec('rm -f '.$path.'/remix'.$I.'.mp3');
                            exec('rm -f '.$path.'/success'.$II.'.mp3');
                        }

                    }

                    exec('rm -f '.$path.'/'.$I.'_out.mp3');
                }

            }else if($file_total<=10){
                $file_length=floor($file_length/5);
                for($i=0;$i<=$file_length;$i++){
                    $I=$i*5;
                    $II=$I-5;

                    //切割原始音樂檔 $i從0開始($i是影片總共要切幾個檔案) 將檔案以5秒分成一個檔案
                    exec('ffmpeg -i "'.$path.'/'.$file_name.'" -ss ' .$I. ' -t 5 -y -f wav - | lame - '.$path.'/'.$I.'_out.mp3');
                    //判斷影片總長度是小於5秒，是的話就不用合併，壓完浮水音就完成
                    if($i==0 && $file_total<5){
                        exec('ffmpeg -i '.$path.'/'.$I.'_out.mp3 -i ' . $WATERMARK_SOUND . ' -filter_complex amix=inputs=2:duration=first:dropout_transition=2 -y -f wav - | lame - '.$target_mp3_path.'/'.$single_id.'.'.$ex_file_name[1]);
                    }else{
                        exec('ffmpeg -i '.$path.'/'.$I.'_out.mp3 -i ' . $WATERMARK_SOUND . ' -filter_complex amix=inputs=2:duration=first:dropout_transition=2 -f wav - | lame - '.$path.'/remix'.$I.'.mp3');
                    }
                    if($i==1 && 5<=$file_total && $file_total<=10){
                        exec('ffmpeg -i "concat:'.$path.'/remix'.$II.'.mp3|'.$path.'/remix'.$I.'.mp3" -acodec copy -y '.$target_mp3_path.'/'.$single_id.'.'.$ex_file_name[1]);
                        exec('rm -f '.$path.'/remix'.$I.'.mp3');
                        exec('rm -f '.$path.'/remix'.$II.'.mp3');
                    }

                    exec('rm -f '.$path.'/'.$I.'_out.mp3');
                }
            }
            exec('ffmpeg -i ' . $target_mp3_path.'/'.$single_id.'.'.$ex_file_name[1] . ' -filter_complex "[0:a]compand=gain=-6,showwavespic=s=600x120:colors=#ffffff[fg]; color=s=600x120:color=#259ae8,drawgrid=width=iw/10:height=ih/5:color=#ffffff@0.1[bg];[bg][fg]overlay=format=rgb,drawbox=x=(iw-w)/2:y=(ih-h)/2:w=iw:h=1:color=#ffffff" -vframes 1 ' . $target_o_path.'/'.$single_id.'.jpg');
        }else{
            $exec = "convert ".$file_path."[0]  -units PixelsPerInch ".$path."/".$ex_file_name[0].".jpg";
            exec($exec);
        }
    }
    return;
}
exit();

?>
