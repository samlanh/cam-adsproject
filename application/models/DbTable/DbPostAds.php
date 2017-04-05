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
	function addPostsAds($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
// 			$photo = $_FILES['filePhoto1'];
// 			$temp = explode(".", $photo["name"]);
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
					"meta_description"=>$data['description'],
					"meta_keyword"    =>$data['description'],
					"alias"           =>date("Y-m-d"),//check later
					"phone1"          =>$data['phone1'],
					"phone2"          =>$data['phone2'],
// 					"images"          =>$data['status'],
// 					"image_feature"   =>$data['note'],
					// 					"user_id"    =>$data['client_id'],
			);
			$this->_name='vd_ads';
			$ads_id = $this->insert($arr);
			
			$data_detail=array();
			$dbg = new Application_Model_DbTable_DbVdGlobal();
			$rscontrol = $dbg->getAllcontrollByCategoryId($data['category']);
				if(!empty($rscontrol)){
					foreach ($rscontrol as $rscon){
						if(empty($data[$rscon['label_name']])){
							$data[$rscon['label_name']]='';//check here more
						}
						$data_detail=array(
											'ads_id'=>$ads_id,
											'control_name'=>$rscon['label_name'],
								            'control_id'=>$rscon['id'],
											'control_value'=>$data[$rscon['label_name']]);	
						$this->_name='vd_ads_detail';
						$this->insert($data_detail);
					}
				}
// 				exit();
			$db->commit();
		}catch(exception $e){
			echo $e->getMessage();exit();
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}

	
	function uploadImageFirst($data){
		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
		$part= PUBLIC_PATH.'/images/';
		$index = $data['index'];
// 		$name = $_FILES["filePhoto$index"]['name'];
// 		$size = $_FILES["filePhoto$index"]['size'];
		$photo = $_FILES["filePhoto$index"];
			$temp = explode(".", $photo["name"]);
			$newName = "Ko.".end($temp);
			move_uploaded_file($_FILES["filePhoto$index"]["tmp_name"], $part . $newName);
			
			$uploadimage=$part.$newName;
			
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
			
			$textcolor = imagecolorallocate($im, 32, 32, 32);
			
			//$stamp =	imagestring($im, 14, 30, 50,  'Sample Water Mark', $textcolor);
			$stamp = imagecreatetruecolor(imagesx($im), 20);
				imagefilledrectangle($stamp, 0, 0, imagesx($im), 20, 0xFFFFFF);
				imagestring($stamp, 18, (imagesx($im)/2)-((imagesx($im)/2)/3), 0, "www.cam-app.com", $textcolor);
// 			$fontfile = $fontpart."arial.ttf";
// 			imagettftext($stamp, 14, 0, imagesx($stamp), 0, $textcolor, $fontfile, 'Sample Water Mark');
		
			$marge_bottom = 10;
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);
			// Merge the stamp onto our photo with an opacity of 50%
		$watermark =	imagecopymerge($im, $stamp, imagesx($im) - $sx, imagesy($im)/2, 0, 0, imagesx($stamp), imagesy($stamp), 50);
		
			// Save the image to file and free memory
				imagejpeg($im, $uploadimage, 50);
				
			//imagedestroy($uploadimage);
			
		return $newName;
	}
}
?>