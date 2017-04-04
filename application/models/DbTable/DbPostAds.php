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
			$photo = $_FILES['filePhoto1'];
			$temp = explode(".", $photo["name"]);
			echo $photo["name"];
			print_r($data);exit();
			$arr = array(
					"ads_code"=>$data['ads_code'],
					"ads_title"=>$data['title'],
					"price"=>$data['price'],
					"category_id"=>$data['category_id'],
					"user_id"=>$data['client_id'],
					"date"=>date("Y-m-d"),
					"description"=>$data['description'],
					"create_date"=>date("Y-m-d"),
					"date_modified"=>date("Y-m-d"),
					"publish_date"=>date("Y-m-d"),
// 					"expired_date"=>date("Y-m-d"),
					"meta_discription"=>$data['description'],
					"meta_keyword"=>$data['description'],
					"alias"=>date("Y-m-d"),
					"phone1"=>date("Y-m-d"),
					"phone2"=>date("Y-m-d"),
					"location"=>date("Y-m-d"),
					"images"=>$data['status'],
					"image_feature"=>$data['note'],
			);
			$this->_name = "vd_ads";
			$typeid = $this->insert($arr);		
			$db->commit();
		}catch(exception $e){
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