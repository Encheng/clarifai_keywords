<?php
exec('cd /var/www/html/ffmpeg_mp3; ls',$return_filename);
//var_dump($return_filename);exit();
	foreach ($return_filename as $key => $value) {
//		$new_name = str_replace('audiojungle-', '', $value);
//		$new_name = str_replace('.zip', '', $new_name);
//		exec('mkdir /var/www/html/ffmpeg_mp3/"' . $new_name.'"');
		exec('zip /var/www/html/ffmpeg_mp3/"' . $value . '.zip" -d /var/www/html/ffmpeg_mp3/"' . $value.'"');
		echo $value. "<br/>";
	}
	echo 'done';exit();
?>
