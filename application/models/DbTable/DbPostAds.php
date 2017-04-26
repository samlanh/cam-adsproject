<?php

class Application_Model_DbTable_DbPostAds extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		return $session_lang->lang_id;
	}
	public function getNewAdsCode(){
		$this->_name='vd_ads';
		$db = $this->getAdapter();
		$sql=" SELECT ad.`id`  FROM $this->_name AS ad  ORDER BY ad.`id` DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = "ADS";
		for($i = $acc_no;$i<4;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	function replaceString(){
		
		
		
	}
	function addPostsAds($data){
		$client_session=new Zend_Session_Namespace('client');
		$client_id = $client_session->client_id;
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$adsid = $this->getLastAdsId();
			$adsidalias = $adsid.str_replace(" ",'', $data['title']);
			
			$dbs = new Application_Model_DbTable_DbGlobalselect();
			$rsstore = $dbs->getAllStoreByUser();
			if(count($rsstore)>1){
				$store_id=empty($data['store_id'])?'':$data['store_id'];//check if not real store
			}else{
				$store_id = $rsstore[0]['id'];
			}
			$arr = array(
					'store_id'   =>$store_id,
					"ads_code"   =>empty($data['ads_code'])?'':$data['ads_code'],
					"ads_title"  =>empty($data['title'])?'':$data['title'],
					"price"      =>empty($data['price'])?'':$data['price'],
					"old_price"  =>empty($data['price'])?'':$data['price'],
					'contact_name'=>empty($data['price'])?'':$data['price'],
					'is_whatapp'=>'',
					'is_discount'=>0,
					'is_showcontact'=>0,
					'email'=>empty($data['email'])?'':$data['email'],
					'link'=>empty($data['link'])?'':$data['link'],
					'address'=>empty($data['adress'])?'':$data['adress'],
					"category_id"=>empty($data['category'])?'':$data['category'],
					"date"       =>date("Y-m-d"),
					"description"=>empty($data['description'])?'':$data['description'],
					"province_id"=>empty($data['location'])?'':$data['location'],
					"district_id"=>empty($data['district'])?'':$data['district'],
					"commune_id"=>empty($data['commune'])?'':$data['commune'],
					"create_date"=>date("Y-m-d"),
					"date_modified"=>date("Y-m-d"),
					"publish_date" =>date("Y-m-d"),
					"expired_date"=>date("Y-m-d", strtotime(" date('Y-m-d') +15 day")),
// 					"meta_description"=>$data['description'],
// 					"meta_keyword"    =>$data['description'],
					"alias"           =>$adsidalias,//check later
					"phone1"          =>empty($data['phone1'])?'':$data['phone1'],
					"phone2"          =>empty($data['phone2'])?'':$data['phone2'],
					"status"          =>1,
					"user_id"          =>$client_id,
			);
			$str_img = '';
			$arr['image_feature']='noimagefound.jpg';
			
			$dbg = new Application_Model_DbTable_DbGlobalsetting();
			$rsallowimg  =  $dbg->getSystemSetting('allow_image',1);
			
		    for ($i=0; $i<=$rsallowimg; $i++){
				$set_feature=0;
				if(!empty($data['ads-image'.$i]) AND $set_feature==0){
					$data['ads-image'.$i] = empty($data['ads-image'.$i])?'noimagefound.jpg':$data['ads-image'.$i];
					$arr['image_feature']=$data['ads-image'.$i];
					$set_feature=1;
				}
				if(empty($data['ads-image'.$i])){continue;}
					if($i==0){
						$str_img.=$data['ads-image'.$i].',';
					}else{
						$arr['images']=$data['ads-image'.$i];
						$comma = ',';
						if($i==6){$comma='';}
						$str_img.=$data['ads-image'.$i].$comma;
					}
			}
			$arr['images']=$str_img;
			$this->_name='vd_ads';
			$ads_id = $this->insert($arr);
			
			$data_detail=array();
			$dbg = new Application_Model_DbTable_DbVdGlobal();
			$rscontrol = $dbg->getAllcontrollByCategoryId($data['category']);
				if(!empty($rscontrol)){
					$this->_name='vd_ads_detail';
					foreach ($rscontrol as $rscon){
						$data[$rscon['title']] = empty($data[$rscon['title']])?'':$data[$rscon['title']];
						$data[$rscon['label_name']] = empty($data[$rscon['label_name']])?'':$data[$rscon['label_name']];
						
						if(empty($data[$rscon['label_name']])){
							$data[$rscon['label_name']]='';//check here more
						}
							$data_detail=array(
									'ads_id'=>$ads_id,
									'control_name'=>$rscon['label_name'],
						            'control_id'=>$rscon['id'],
									'control_value'=>$data[$rscon['title']]);	
							$this->insert($data_detail);
					}
				}
			$db->commit();
			return 1;
		}catch(exception $e){
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function addUpdateAds($data){
		$client_session=new Zend_Session_Namespace('client');
		$client_id = $client_session->client_id;
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$ads_id = $data['adsid'];
			
			$rsreal = $this->getRealAdsByUser($ads_id,$client_id);
			$old_cate = $data['category'];if(!empty($rsreal)){$old_cate = $rsreal['category_id'];}
			
			$adsid = $this->getLastAdsId();
// 			$adsidalias = $adsid.str_replace("'",'', $data['title']);
			$arr = array(
					"ads_code"   =>$data['ads_code'],
					"ads_title"  =>$data['title'],
					"price"      =>$data['price'],
					"old_price"      =>$data['price'],
					'contact_name'=>$data['name'],
					'is_whatapp'=>'',
					'is_discount'=>0,
					'is_showcontact'=>0,
					'email'=>$data['name'],
					'link'=>$data['name'],
					'address'=>$data['adress'],
					"category_id"=>$data['category'],
					"date"       =>date("Y-m-d"),
					"description"=>$data['description'],
					"province_id"=>$data['location'],
					"district_id"=>$data['district'],
					"commune_id"=>$data['commune'],
					"create_date"=>date("Y-m-d"),
					"date_modified"=>date("Y-m-d"),
					"publish_date" =>date("Y-m-d"),
					"expired_date"=>date("Y-m-d", strtotime(" date('Y-m-d') +15 day")),
					// 					"meta_description"=>$data['description'],
			// 					"meta_keyword"    =>$data['description'],
// 					"alias"           =>$adsidalias,//check later
					"phone1"          =>$data['phone1'],
					"phone2"          =>$data['phone2'],
					"status"          =>1,
					"user_id"          =>$client_id,
			);
			$str_img = '';
			$arr['image_feature']='noimagefound.jpg';
			for ($i=0; $i<=6; $i++){
				$set_feature=0;
				if(!empty($data['ads-image'.$i]) AND $set_feature==0){
					$arr['image_feature']=$data['ads-image'.$i];
					$set_feature=1;
				}
				if(empty($data['ads-image'.$i])){
					continue;
				}
				if($i==0){
					$str_img.=$data['ads-image'.$i].',';
				}else{
					$arr['images']=$data['ads-image'.$i];
					$comma = ',';
					if($i==6){
						$comma='';
					}
					$str_img.=$data['ads-image'.$i].$comma;
				}
			}
			$arr['images']=$str_img;
			$this->_name='vd_ads';
			
			$where =" user_id=$client_id AND id = ".$data['adsid'];
			$this->update($arr, $where);
				
			$data_detail=array();
			
			if(!empty($rsreal)){//if ads for 
				$this->_name='vd_ads_detail';
				if($data['category']!=$old_cate){
					$where="ads_id = ".$ads_id;
					$this->delete($where);//delete data from ads_detail if edit change category
				}
				
				$dbg = new Application_Model_DbTable_DbVdGlobal();
				$rscontrol = $dbg->getAllcontrollByCategoryId($data['category']);
				if(!empty($rscontrol)){//if change category should delete detail first
					
					foreach ($rscontrol as $rscon){
						if(empty($data[$rscon['label_name']])){
							$data[$rscon['label_name']]='';//check here more
						}
						$data_detail=array(
								'ads_id'=>$ads_id,
								'control_name'=>$rscon['label_name'],
								'control_id'=>$rscon['id'],
								'control_value'=>$data[$rscon['title']]);
						$where = "control_id = ".$rscon['id']." AND ads_id = ".$ads_id;//udpate by control_id to detail ads
						$this->update($data_detail,$where);
					}
				}
			}	
			$db->commit();
			return 1;
		}catch(exception $e){
			echo $e->getMessage();exit();
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getRealAdsByUser($adsid,$user_id){
		$db = $this->getAdapter();
		$sql="SELECT ad.id,ad.category_id FROM vd_ads AS ad,vd_ads_detail AS dt WHERE ad.id=dt.ads_id AND ad.id = $adsid AND ad.user_id = $user_id ";
		return $db->fetchRow($sql);
	}
	function getLastAdsId(){
		$db = $this->getAdapter();
		$sql="SELECT (id+1) AS adsid FROM `vd_ads` ORDER BY id DESC LIMIT 1 ";
		return $db->fetchOne($sql);
	}

	
	function uploadImageFirst($data){
		$client_session=new Zend_Session_Namespace('client');
		
		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
		$part= PUBLIC_PATH.'/images/adsimg/';
		$index = $data['index'];
// 		$name = $_FILES["filePhoto$index"]['name'];
// 		$size = $_FILES["filePhoto$index"]['size'];
		$photo = $_FILES["filePhoto$index"];
			$temp = explode(".", $photo["name"]);
			$newName = date("Y-m-d").round(microtime(true)).$client_session->client_id.'.'.end($temp);
			move_uploaded_file($_FILES["filePhoto$index"]["tmp_name"], $part . $newName);
			
			
			$uploadimage= $part.$newName;
			
			$percent = 0.5;
			list($width, $height) = getimagesize($uploadimage);
			if ($width<=400){
				$new_width = $width;
				$new_height = $height;
			}else{
				$new_width = $width * $percent;
				$new_height = $height * $percent;
			}
			
			
			// Resample
			$image_p = imagecreatetruecolor($new_width, $new_height);
			// Load the stamp and the photo to apply the watermark to
			if (end($temp) == 'jpg') {
				$im = imagecreatefromjpeg($uploadimage);
			} else
				if (end($temp) == 'jpeg') {
				$im = imagecreatefromjpeg($uploadimage);
			} else
				if (end($temp) == 'png') {
				$im = imagecreatefrompng($uploadimage);
			} else
				if (end($temp) == 'gif') {
				$im = imagecreatefromgif($uploadimage);
			}
			imagecopyresampled($image_p, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);// resize image
			
			// Output
			//imagejpeg($image_p, null, 100);
			
			
			
			$textcolor = imagecolorallocate($image_p, 32, 32, 32);
			//$stamp =	imagestring($im, 14, 30, 50,  'Sample Water Mark', $textcolor);
			$stamp = imagecreatetruecolor(imagesx($image_p), 20);
				imagefilledrectangle($stamp, 0, 0, imagesx($image_p), 20, 0xFFFFFF);
				imagestring($stamp, 18, (imagesx($image_p)/2)-((imagesx($image_p)/2)/3), 0, "www.cam-app.com", $textcolor);
// 			$fontfile = $fontpart."arial.ttf";
// 			imagettftext($stamp, 14, 0, imagesx($stamp), 0, $textcolor, $fontfile, 'Sample Water Mark');
		
			$marge_bottom = 10;
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);
			// Merge the stamp onto our photo with an opacity of 50%
			imagecopymerge($image_p, $stamp, imagesx($image_p) - $sx, imagesy($image_p)/2, 0, 0, imagesx($stamp), imagesy($stamp), 50);
		
			// Save the image to file and free memory
				imagejpeg($image_p, $uploadimage, 50);
				
			//imagedestroy($uploadimage);
				
				
		return $newName;
	}
	
	public function getAdsDetail($ads_id){
		$db =$this->getAdapter();
		$language = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
				);
		$province = $province_field[$language];
		$sql= "SELECT ads.* ,
			(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author,
			(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = ads.`category_id`  AND catd.languageId =$language LIMIT 1)  AS cateogry_title,
			(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`parent`  AND catd.languageId =$language LIMIT 1)  AS parent_cateogry_title,
			(SELECT province.$province FROM `vd_province` AS province WHERE province.id = ads.`province_id` LIMIT 1) AS province,
			(SELECT cs.alias_store FROM `vd_client_store` AS cs WHERE cs.id = ads.store_id LIMIT 1 ) AS store_alias,
			(SELECT cs.store_title FROM `vd_client_store` AS cs WHERE cs.id = ads.store_id LIMIT 1 ) AS store_title
			FROM `vd_ads` AS ads,
			`vd_category` AS cat
			 WHERE cat.`id` = ads.`category_id` AND ads.`id`=$ads_id LIMIT 1";
		return $db->fetchRow($sql);
	}
}
?>