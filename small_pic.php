<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');


//要有可以輸入input路徑跟output路徑

//路徑 預設E:/var/www/html/cute_pic/
$mypath = !empty($_POST['filePath'])?$_POST['filePath']:'/var/www/html/cute_pic/';
//因為有可能有中文路徑，需要轉碼讀的到檔案
$mypath = iconv("utf-8","big5",$mypath);

//var_dump($mypath);exit;
//抓取路徑下的所有(jpg)檔名，並放入陣列
$file_name = glob($mypath.'*.*');
//var_dump($file_name);exit;

$a=1;
$aa=0;
 foreach ($file_name as $image_key => $name) {
 	//去除路徑，取得此檔案名稱
 	$path_parts = pathinfo($name);
 	$file_name_one = $path_parts['basename']; //檔名+附檔名
	$file_only_name = $path_parts['filename']; //檔名

    $aa++;

	//本機連線測試
	if(file_exists($mypath.$file_name_one)){
		$file_path = $mypath.$file_name_one;
	}else{
		$file_path = $mypath.$file_name_one;
	}
     //var_dump(file_exists("E:/pic_claifai_test/".$file_name_one));exit;



 	//開始切縮圖
	if($file_path != ""){

        build_o_p($mypath,$file_name_one,$file_name_one);
//var_dump("success");exit;
	}else{
		echo 'error,path wrong!';
	}
		
 }



var_dump("完成切割");

function build_o_p($file_path, $file_name, $file_rename) {
    //p
    $target_p_path = 'lib_storage_146/p';
    exec('convert -strip -density 72 -geometry 500x500 "' . $file_path . $file_name . '" "' . $target_p_path . '/' . $file_rename . '"');
    // var_dump('convert -strip -density 72 -geometry 150x150 "' . $file_path . '/' . $file_name . '" "' . $target_p_path . '/' . $file_rename . '"');
    // exit();
    //o
    $target_o_path = 'lib_storage_146/o';
    exec('convert -strip -density 72 -geometry 500x500 "' . $file_path . $file_name . '" "' . $target_o_path . '/' . $file_rename . '"');
    //var_dump('convert -strip -density 72 -geometry 500x500 ' . $file_path . '/' . $file_name . ' ' . $target_o_path . '/' . $file_rename . '');
    //exit();
    $WATERMARK_FILE = 'imagedj.png';
    exec('composite -dissolve 50% -gravity center "' . $WATERMARK_FILE . '" "' . $target_o_path . '/' . $file_rename . '" "' . $target_o_path . '/' . $file_rename . '"');
//var_dump('composite -dissolve 50% -gravity center "' . $WATERMARK_FILE . '" "' . $target_o_path . '/' . $file_rename . '" "' . $target_o_path . '/' . $file_rename . '"');exit;
    //return array('p_path' => $target_p_path, 'o_path' => $target_o_path);
}
exit();

?>
