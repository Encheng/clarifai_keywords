<?php
class clarifai{
	//測試帳號
	private $pubKey = "Your clarifai API KEY";
	//model ID
	private $GENERAL = 'aaa03c23b3724a16a56b629203edc62c';
	private $FOOD = 'bd367be194cf45149e75f01d59f77ba7';
	private $TRAVEL = 'eee28c313d69466f836ab83287a54ed9';
	private $NSFW = 'e9576d86d2004ed1a38ba0cf39ecb4b1';
	private $WEDDINGS = 'c386b7a870114f4a87477c0824499348';
	private $COLOR = 'eeed0b6733a644cea07cf4c60f87ebb7';
	private $FACE = 'a403429f2ddf4b49b307e318f00e528b';
	private $APPAREL = 'e0be3b9d6a454f0493ac3a30784001ff';
	private $CELEBRITY = 'e466caa0619f444ab97497640cefc4dc';
	private $Demographics = 'c0c0ac362b03416da06ab3fa36fb58e3';
	private $Moderation = 'd16f390eb32cad478c7ae150069bd2c6';
	public function post_data($url,$language) {
		$post_data = '{
			"inputs": [
				{
					"data": {
						"image": {
							"url": "'.$url.'"
						}
					}
				}
			],
			"model":{
				"output_info":{
					"output_config":{
						"language":"'.$language.'"
					}
				}
			}
		}';
		return $post_data;
	}
	public function post_data_bytes($url,$language) {
		$post_data = '{
			"inputs": [
				{
					"data": {
						"image": {
							"base64": "'.$url.'"
						}
					}
				}
			],
			"model":{
				"output_info":{
					"output_config":{
						"language":"'.$language.'"
					}
				}
			}
		}';
		return $post_data;
	}
	public function post_data_nolanguage($url) {
		$post_data = '{
			"inputs": [
				{
					"data": {
						"image": {
							"url": "'.$url.'"
						}
					}
				}
			]
		}';
		return $post_data;
	}
	public function request($model_id,$post_data){
		$ch = curl_init('https://api.clarifai.com/v2/models/' . $model_id. '/outputs'); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Key ".$this->pubKey, "Content-Type: application/json"));      
        $output = curl_exec($ch); 
        curl_close($ch);
		return $output;
	}
	public function GENERAL($url,$language) {
		$post_data = $this->post_data_bytes($url,$language);
		$model_id = $this->GENERAL;
		return $this->request($model_id,$post_data);
	}
	public function FOOD($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->FOOD;
		return $this->request($model_id,$post_data);
	}
	public function TRAVEL($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->TRAVEL;
		return $this->request($model_id,$post_data);
	}
	public function NSFW($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->NSFW;
		return $this->request($model_id,$post_data);
	}
	public function WEDDINGS($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->WEDDINGS;
		return $this->request($model_id,$post_data);
	}
	public function COLOR($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->COLOR;
		return $this->request($model_id,$post_data);
	}
	public function FACE($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->FACE;
		return $this->request($model_id,$post_data);
	}
	public function APPAREL($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->APPAREL;
		return $this->request($model_id,$post_data);
	}
	public function CELEBRITY($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->CELEBRITY;
		return $this->request($model_id,$post_data);
	}
	public function Demographics($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->Demographics;
		return $this->request($model_id,$post_data);
	}
	public function Moderation($url,$language) {
		$post_data = $this->post_data_nolanguage($url);
		$model_id = $this->Moderation;
		return $this->request($model_id,$post_data);
	}
	public function Cache($filename,$data) {
		$data_cache = "<?php			
		\$".$filename."  = " . var_export($data, true) . ";			
		?>";
		$fp = fopen('data/'.$filename.'.php', 'wb');
		fwrite($fp, $data_cache);
		fclose($fp);
	}
}
?>
