<?php
set_time_limit(0);
ini_set('memory_limit', '2048M');

require_once('PHPExcel/Classes/PHPExcel.php');
require_once('PHPExcel/Classes/PHPExcel/Writer/Excel5.php');
include_once ('clarifai.php');
//接收資料庫訊息
$dbhost = !empty($_POST['mongodbHost'])?$_POST['mongodbHost']:'127.0.0.1';
$dbname = !empty($_POST['mongodbDBname'])?$_POST['mongodbDBname']:'test';
$collection = !empty($_POST['mongodbCollection'])?$_POST['mongodbCollection']:'idjcute_keywords';

//$dbhost = '192.168.1.149';
//$dbname = 'test';
$mongoClient = new MongoClient('mongodb://' . $dbhost);
$mongo_db = $mongoClient->$dbname;
$language = 'en';
$clarifai = new clarifai();

/*try {
	$dsn = "mysql:host=192.168.1.135;dbname=imagedj_library;";
	$db = new PDO($dsn,"root","imagedj0135)!#%",array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch( PDOException $e ) {
	//die( $e->getMessage() );
	die("<p><b>error connection</b></p>");
}*/

//mongoDB資料表連結
$mongo_keywords = $mongo_db->$collection;

//輸出方式，excel或mongoDB或excel+mongodb
$select_type = $_POST['selectType'];

//路徑 預設E:/pic_claifai_test/
$mypath = !empty($_POST['filePath'])?$_POST['filePath']:'E:/pic_claifai_test/';
//因為有可能有中文路徑，需要轉碼讀的到檔案
$mypath = iconv("utf-8","big5",$mypath);

//var_dump($mypath);exit;
//抓取路徑下的所有(jpg)檔名，並放入陣列
$file_name = glob($mypath.'*.{jpg,JPG}',GLOB_BRACE);
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


     //判斷有沒有存在資料表裡面，有就不進去if(用來判斷不重複解析圖片)
     $lastKeywordsQuery = array('dangMing' => $file_only_name);
     $last_keywords_repeat = $mongo_keywords->findOne($lastKeywordsQuery);

 	//Clarifai解析開始
	if($file_path != "" && count($last_keywords_repeat)==0){
		$im = file_get_contents($file_path);
		$imdata = base64_encode($im);
		$data_general = $clarifai->GENERAL($imdata ,$language);
		$data_general = json_decode($data_general,true);
		#echo json_encode($data_general);exit();
		//圖片解析失敗
		if($data_general['status']['code'] != 10000){
			echo $data_general['status']['code']."<br/>";
			echo $data_general['status']['description']."<br/>";
			//break;
            if($select_type == 'mongodb'){
                $insert_data = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $data_general['status']['code'],
                    'keyword_string' => $data_general['status']['description'],
                );
                $mongo_keywords -> save($insert_data);
            }

            if($select_type == 'excel'){
                $insert_data_excel[] = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $data_general['status']['code'],
                    'keyword_string' => $data_general['status']['description'],
                );
            }

            if($select_type == 'ExcelMongodb'){
                $insert_data = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $data_general['status']['code'],
                    'keyword_string' => $data_general['status']['description'],
                );
                $mongo_keywords -> save($insert_data);

                $insert_data_excel[] = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $data_general['status']['code'],
                    'keyword_string' => $data_general['status']['description'],
                );
            }

            echo "第".$a."筆 ".$file_only_name."<br/>";
            $a++;
            //var_dump($insert_data);
		}else{
            $data_general = $data_general['outputs'][0]['data']['concepts'];
            $keywords = array();
            $keyword = array();
            foreach ($data_general as $general_key => $general_value) {
                $keyword[] = array(
                    'keyword_en' => $general_value['name'],
                    'percent' => $general_value['value']
                );
                array_push($keywords, $general_value['name']);
            }

            $keyword_string = implode(',',$keywords);

            if($select_type == 'mongodb'){
                $insert_data = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $keyword,
                    'keyword_string' => $keyword_string,
                );
                $mongo_keywords -> save($insert_data);
            }

            if($select_type == 'excel'){
                $insert_data_excel[] = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $keyword,
                    'keyword_string' => $keyword_string,
                );
            }

            if($select_type == 'ExcelMongodb'){
                $insert_data = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $keyword,
                    'keyword_string' => $keyword_string,
                );
                $mongo_keywords -> save($insert_data);

                $insert_data_excel[] = array(
                    'dangMing' => $file_only_name,
                    'keyword_array' => $keyword,
                    'keyword_string' => $keyword_string,
                );
            }

            echo "第".$a."筆 ".$file_only_name."<br/>";
            $a++;
            var_dump($insert_data_excel);
		}

	}else{
		echo 'error,path wrong!';
	}
		
 }

 //要匯出excel條件
 if($select_type == 'excel' || $select_type == 'ExcelMongodb'){
 	 excel_output($insert_data_excel);
 }

var_dump("完成解析".$aa);


 function excel_output($insert_data){
     $objPHPExcel = new PHPExcel();
     // Set document properties
     $objPHPExcel -> getProperties() -> setCreator("Maarten Balliauw") -> setLastModifiedBy("Maarten Balliauw") -> setTitle("Office 2007 XLSX Test Document") -> setSubject("Office 2007 XLSX Test Document") -> setDescription("Test document for Office 2007 XLSX, generated using PHP classes.") -> setKeywords("office 2007 openxml php") -> setCategory("Test result file");
     // Add some data
     $objPHPExcel -> getActiveSheet() -> setCellValue('A1', 'imageid');
     $objPHPExcel -> getActiveSheet() -> setCellValue('B1', 'keywords');

     // Miscellaneous glyphs, UTF-8
     $total = count($insert_data);
     for ($i = 2; $i < $total + 2; $i++) {
         $objPHPExcel -> getActiveSheet() -> setCellValueExplicit('A' . $i, $insert_data[$i-2]['dangMing'], PHPExcel_Cell_DataType::TYPE_STRING2);
         $objPHPExcel -> getActiveSheet() -> setCellValueExplicit('B' . $i, $insert_data[$i-2]['keyword_string'], PHPExcel_Cell_DataType::TYPE_STRING2);

     }
     $objPHPExcel -> getActiveSheet() -> getColumnDimension('A') -> setWidth(25);
     $objPHPExcel -> getActiveSheet() -> getColumnDimension('B') -> setWidth(25);

     $objPHPExcel -> getActiveSheet() -> setTitle('clarifai關鍵字');
     // Set active sheet index to the first sheet, so Excel opens this as the first sheet
     $objPHPExcel -> setActiveSheetIndex(0);
     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
     //$objWriter->save(str_replace('.php', '.xls', __FILE__));

	 $filename = date("ymdhms").'-clarifai_keywords.xls';
     $objWriter -> save($filename);
//     header("Pragma: public");
//     header("Expires: 0");
//     header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
//     header("Content-Type:application/force-download");
//     header("Content-Type:application/vnd.ms-execl");
//     header("Content-Type:application/octet-stream");
//     header("Content-Type:application/download");
//     header("Content-Disposition:attachment;filename=" . basename($filename));
//     header("Content-Transfer-Encoding:binary");
//     $objWriter -> save('php://output');
 }
exit();

?>
