<?php

class Application_Model_DbTable_DbFormPostAds extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	public function getCategory($parent = 0, $spacing = '', $cate_tree_array = ''){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT c.`id`,
		(SELECT cd.title FROM `vd_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=1 LIMIT 1) AS name,
		c.`parent` FROM `vd_category` AS c WHERE c.`status`=1 AND c.`parent`=$parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	function getAllControlByCateid($cate_id){// old test form
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_field_type` AS ft WHERE FIND_IN_SET($cate_id,ft.`category`) AND ft.`status`=1";
		$row =  $db->fetchAll($sql);
		$form='';
		if (!empty($row)) foreach ($row as $rs){
			if($rs["is_require"]==1){
				$isreq= 'required="required"';
				$sigrequir='<span class="req">*</span>';
			}else{ $isreq='';$sigrequir='';
			}
			if ($rs["type"]=="select"){
				$form.=$this->OptionSelect($rs["id"],$rs["title"],$rs["is_require"],$rs["label_name"]);
			}elseif ($rs["type"]=="cascade"){
				$form.='<div class="top-row">';
					$form.='<div class="field-wrap labelname"><label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="field-wrap">';
						$form.='<select onChange="getCascadeValue('.$rs["title"].')"  id="'.$rs["title"].'" name="'.$rs["title"].'" '.$isreq.'>';
						$form.='</select>';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="text"){
				$form.='<div class="top-row">';
					$form.='<div class="field-wrap labelname"><label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="field-wrap">';
						$form.='<input type="text" value="" '.$isreq.' id="'.$rs["title"].'" name="'.$rs["title"].'" />';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="number"){
				$form.='<div class="top-row">';
					$form.='<div class="field-wrap labelname"><label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="field-wrap">';
						$form.='<input id="'.$rs["title"].'" name="'.$rs["title"].'" '.$isreq.' type="text" value="" onkeypress="return isNumber(event);" >';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="textarea"){
				$form.='<div class="top-row">';
					$form.='<div class="field-wrap labelname"><label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="field-wrap">';
						$form.=' <textarea id="'.$rs["title"].'" name="'.$rs["title"].'" '.$isreq.' ></textarea>';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="emailaddress"){
				$form.='<div class="top-row">';
					$form.='<div class="field-wrap labelname"><label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="field-wrap">';
						$form.=' <input type="email" id="'.$rs["title"].'" name="'.$rs["title"].'" '.$isreq.' autocomplete="off"/>';
					$form.='</div>';
				$form.='</div>';
			}
		}
		$arr = array('form'=>$form);
		return $arr;
	}
	
	function OptionSelect($fielid,$name,$require,$labelname){ // old form test
		if($require==1){
			$isreq= 'required="required"';
			$sigrequir='<span class="req">*</span>';
		}else{ $isreq=''; $sigrequir='';}
		$functionOnchage='';
		if ($this->checkFieldHasChild($fielid)>0){
			$controlname = "'".$name."'";
			$functionOnchage='onChange="getCascadeValue('.$controlname.')"';
		}
		$value = $this->getValueofOption($fielid);
		$string='';
		$string.='<div class="top-row">';
			$string.='<div class="field-wrap labelname"><label>'.$labelname.$sigrequir.'</label> </div>';
				$string.='<div class="field-wrap">';	
					$string.='<select '.$functionOnchage.'   id="'.$name.'" name="'.$name.'" '.$isreq.'>';
						$string.='<option value=""></option>';
						if (!empty($value)) foreach ($value as $rs){
						$string.='<option value="'.$rs['name'].'">'.$rs['name'].'</option>';
						}
					$string.='</select>';
				$string.='</div>';
		$string.='</div>';
		return $string;
	}
	function getValueofOption($fielid){
		$db = $this->getAdapter();
		$sql="SELECT fv.`id`,fv.`name` FROM `vd_field_value` AS fv WHERE fv.`field_type_id`=".$fielid;
		return $db->fetchAll($sql);
	}
	function checkFieldHasChild($parent){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(ft.`id`) AS counter FROM `vd_field_type` AS ft WHERE  ft.`status`=1 AND ft.`field_parent`=$parent";
		return $db->fetchOne($sql);
	}
	function checkChild($id){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(c.`id`) AS r FROM `vd_category` AS c WHERE c.`parent`=$id";
		return $db->fetchOne($sql);
	}
	function getCascadeOption($parent){
		$db= $this->getAdapter();
		$sql="SELECT *,
			(SELECT ft.title FROM `vd_field_type` AS ft WHERE ft.id = fv.`field_type_id` LIMIT 1) AS fieldname
		 FROM `vd_field_value` AS fv WHERE fv.`parent`='$parent' ORDER BY fv.ordering ASC";
		$rows=$db->fetchAll($sql);
		$options = '';
		$fieldname='';
		if(!empty($rows))foreach($rows as $value){
			$fieldname=$value['fieldname'];
			$options .= '<option value="'.$value['name'].'" >'.htmlspecialchars($value['value'], ENT_QUOTES).'</option>';
		}
		$array = array("fieldname"=>$fieldname,"value"=>$options,);
		return $array;
	}
	function addWaterMark($data){
		$part= PUBLIC_PATH.'/images/';
		$photo_name1='';
		$photo = $_FILES['filePhoto'];
			move_uploaded_file($_FILES['filePhoto']["tmp_name"], $part . $photo["name"]);
			$photo_name1 = $photo["name"];
			
			$uploadimage=$part.$_FILES["filePhoto"]["name"];
			$newname=$_FILES["filePhoto"]["name"];
			
			// Set the thumbnail name
			$thumbnail = $part.$newname."_thumbnail.jpg";
			$actual = $part.$newname.".jpg";
			$imgname=$newname."_thumbnail.jpg";
			
			// Load the mian image
			$source = imagecreatefromjpeg($uploadimage);
			
			// load the image you want to you want to be watermarked
			$watermark = imagecreatefrompng($part.'watermark1.png');
			// get the width and height of the watermark image
			  $water_width = imagesx($watermark);
			  $water_height = imagesy($watermark);
			
			  // get the width and height of the main image image
			  $main_width = imagesx($source);
			  $main_height = imagesy($source);
			
			  // Set the dimension of the area you want to place your watermark we use 0
			  // from x-axis and 0 from y-axis 
			  $dime_x = 0;
			  $dime_y = 0;
			
			  // copy both the images
			  imagecopy($source, $watermark, $dime_x, $dime_y, 0, 0, $water_width, $water_height);
				  
			  // Final processing Creating The Image
			  imagejpeg($source, $uploadimage, 100);
			 imagedestroy($uploadimage);
		}
		function addWaterMark1($data){
			$part= PUBLIC_PATH.'/images/';
			$fontpart= PUBLIC_PATH.'/font/';
			$photo = $_FILES['filePhoto'];
			$temp = explode(".", $photo["name"]);
			$newfilename1 = $temp[0].".jpg";
			move_uploaded_file($_FILES['filePhoto']["tmp_name"], $part . $photo["name"]);
			
			$uploadimage=$part.$_FILES["filePhoto"]["name"];
			$newname=$_FILES["filePhoto"]["name"];
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
			imagecopymerge($im, $stamp, imagesx($im) - $sx, imagesy($im)/2, 0, 0, imagesx($stamp), imagesy($stamp), 50);
			
			// Save the image to file and free memory
			imagejpeg($im, $uploadimage, 50);
			imagedestroy($uploadimage);
			
	}
}
?>