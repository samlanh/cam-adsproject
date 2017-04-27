<?php

class Application_Model_DbTable_DbStore extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	public static function getClientID(){
		$client_session=new Zend_Session_Namespace('client');
		return $client_session->client_id;
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		return $session_lang->lang_id;
	}
	function countStoreByClient(){
		$client_id = $this->getClientID();
		$db = $this->getAdapter();
		$sql="SELECT COUNT(cs.`id`) FROM `vd_client_store` AS cs WHERE cs.`status`=1 and cs.`client_id` = $client_id";
		return $db->fetchOne($sql);
	}
	function generateStoreAlias($client_id,$store_title){
		$db = new Application_Model_DbTable_DbClient();
		$client= $db->getClientInfo(null,$client_id);
	
	$sepacial_char = array(
			0=>" ",
			1=>".",
			2=>"?",
			3=>":",
			4=>",",
			5=>"/",
			6=>";",
			7=>"%",
			8=>"&",
			9=>"$",
			10=>"@",
			11=>"!",
			12=>"#",
			13=>"^",
			14=>"*",
			15=>"(",
			16=>")",
			17=>"",
			18=>"=",
			19=>"+",
			20=>"[",
			21=>"]",
			22=>"{",
			23=>"}",
			22=>"<",
			23=>">",
			);
		$title=$store_title;
		foreach($sepacial_char as $key => $rr){
			$title = str_replace($rr,'',$title);
		}
		//$title = str_replace(' ','',$client['user_name']);
		return $title.$client['id'].date("Y").date("m").date("d").round(microtime(true)).($this->countStoreByClient()+1);
	}
	function addStore($data){
		$client_id = $this->getClientID();
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			$part= PUBLIC_PATH.'/images/store/';
			$newName='';
			if (!empty($_FILES["logo_image"]["tmp_name"])){
				$photo = $_FILES["logo_image"];
				$temp = explode(".", $photo["name"]);
				$newName = "store".date("Y").date("m").date("d").round(microtime(true)).$client_id.'.'.end($temp);
				move_uploaded_file($_FILES["logo_image"]["tmp_name"], $part . $newName);
			}
			$slideimage="";
			if ($data['templatess']!=2){
				$partslide= PUBLIC_PATH.'/images/store/slide/';
				for ($l=1; $l<=3; $l++){
					$slide = $_FILES['slideimage'.$l]['name'];
					list($txt, $ext) = explode(".", $slide);
					if(in_array($ext,$valid_formats)) {//check ext
						if($slide!=""){
							$temp = explode(".", $slide);
							$newnamefile = "slide".$l.date("Y").date("m").date("d").round(microtime(true)).'.'.$ext;
							if(file_exists("$partslide.$newnamefile")) unlink("$partslide.$newnamefile");
							move_uploaded_file($_FILES['slideimage'.$l]["tmp_name"], $partslide . $newnamefile);
							if (empty($slideimage)){
								$slideimage=$newnamefile;
							}else{
								$slideimage = $slideimage.",".$newnamefile;
							}
						}
					}
				}
			}
			$arr = array(
					'store_title'=>$data['store_title'],
					'alias_store'=>$this->generateStoreAlias($client_id,$data['store_title']),
					'client_id'=>$client_id,
					'template_id'=>$data['templatess'],
					'logo_store'=>$newName,
					'store_address'=>$data['address'],
					'store_phone'=>$data['phone'],
					'store_email'=>$data['email'],
					'store_about_us'=>$data['abouts'],
					'status'=>1,
					'template_color'=>$data['templatesscolor'],
					'font_color'=>$data['fontsscolor'],
					'create_date'=>date("Y-m-d"),
					'modify_date'=>date("Y-m-d"),
					'images_slide'=>$slideimage
					);
			$this->_name="vd_client_store";
			$this->insert($arr);
			$db->commit();
			return 1;
		}catch(exception $e){
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getIdStoreByAlias($alias){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_client_store` AS cs WHERE cs.`alias_store`='$alias' LIMIT 1";
		return $db->fetchRow($sql);
	}
	function updateStore($data){
		$client_id = $this->getClientID();
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$alias = $this->getIdStoreByAlias($data['alias']);
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			$part= PUBLIC_PATH.'/images/store/';
			$newName=$alias['logo_store'];
			if (!empty($_FILES["logo_image"]["tmp_name"])){
				$photo = $_FILES["logo_image"];
				$temp = explode(".", $photo["name"]);
				$newName = "store".date("Y").date("m").date("d").round(microtime(true)).$client_id.'.'.end($temp);
				move_uploaded_file($_FILES["logo_image"]["tmp_name"], $part . $newName);
			}
			$slideimage='';
			$oldimage = explode(",", $alias['images_slide']);
			$txt='';
			$ext='';
			if ($data['templatess']!=2){
				$partslide= PUBLIC_PATH.'/images/store/slide/';
				for ($l=1; $l<=3; $l++){
					$slide = $_FILES['slideimage'.$l]['name'];
					if (!empty($slide)){
						list($txt, $ext) = explode(".", $slide);
					}
					if(in_array($ext,$valid_formats)) {//check ext
						if($slide!=""){
							$temp = explode(".", $slide);
							$newnamefile = "slide".$l.date("Y").date("m").date("d").round(microtime(true)).'.'.$ext;
							if(file_exists("$partslide.$newnamefile")) unlink("$partslide.$newnamefile");
							move_uploaded_file($_FILES['slideimage'.$l]["tmp_name"], $partslide . $newnamefile);
							if (empty($slideimage)){
								$slideimage=$newnamefile;
							}else{
								if (!empty($newnamefile)){
									$slideimage = $slideimage.",".$newnamefile;
								}
							}
						}
					}else{
							$newnamefile=$oldimage[$l-1];
							if (empty($slideimage)){
								$slideimage=$newnamefile;
							}else{
								if (!empty($newnamefile)){
								$slideimage = $slideimage.",".$newnamefile;
								}
							}
						}
				}
			}
			
			$arr = array(
					'client_id'=>$client_id,
					'template_id'=>$data['templatess'],
					'logo_store'=>$newName,
					'store_address'=>$data['address'],
					'store_phone'=>$data['phone'],
					'store_email'=>$data['email'],
					'store_about_us'=>$data['abouts'],
					'status'=>1,
					'template_color'=>$data['templatesscolor'],
					'font_color'=>$data['fontsscolor'],
// 					'create_date'=>date("Y-m-d"),
					'modify_date'=>date("Y-m-d"),
					'images_slide'=>$slideimage
			);
			if ($alias['store_title']!=$data['store_title']){
				$arr['store_title']=$data['store_title'];
				$arr['alias_store']=$this->generateStoreAlias($client_id,$data['store_title']);
			}
			$this->_name="vd_client_store";
			$where = ' id = '.$alias['id'];
			$this->update($arr, $where);
			$db->commit();
			return 2;
		}catch(exception $e){
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	
	//for controller store
	function getStringProvince(){
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		return $province_field[$lang_id];
	}
	function getAdsByUserid($most_view=null,$feature=null,$store_id=null){
		$this->_name='vd_ads';
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$province = $this->getStringProvince($lang_id);
		$sql="SELECT ads.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_phone,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`parent`  AND catd.languageId =$lang_id LIMIT 1)  AS parent_category_name,
		(SELECT $province FROM `vd_province` WHERE id= ads.province_id ) as province_name
		FROM $this->_name AS ads, `vd_category` AS cat  WHERE cat.`id` = ads.`category_id`   AND ads.store_id = $store_id";
		$order='';
		if (!empty($most_view)){ // for use in store
			$order.=" ORDER BY ads.viewer DESC LIMIT 10";
		}
		if (!empty($feature)){ // for use in store
			$order.=" ORDER BY ads.id DESC LIMIT 10";
		}
		return $db->fetchAll($sql.$order);
	}
	
	function  getAllAdsByName($cagetory_name,$store_id){
		$db = $this->getAdapter();
		$category_id = $this->categoryIdByName($cagetory_name);
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		$province = $province_field[$lang_id];
		$sql=" SELECT *,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = `user_id` LIMIT 1) AS author,
		(SELECT title FROM `vd_category_detail` WHERE category_id=vd_ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
		(SELECT $province FROM `vd_province` WHERE id=vd_ads.province_id ) as province_name
		FROM `vd_ads` WHERE store_id=$store_id AND
		category_id=$category_id";
		$where='';
		$parent = $this->checkCateparent($category_id);
		if ($parent['parent']==0){
			$where.=" OR (SELECT c.`parent` FROM `vd_category` AS c WHERE c.`id` = category_id LIMIT 1)  = $category_id";
		}
		$where.=" AND STATUS =1 AND is_expired=0  ";
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	function checkCateparent($id){
		$db = $this->getAdapter();
		$sql=" SELECT c.`parent` FROM `vd_category` AS c WHERE c.`id` = $id  limit 1";
		return $db->fetchRow($sql);
	}
	function categoryIdByName($cagetory_name){
		$db = $this->getAdapter();
		$sql=" SELECT id FROM `vd_category` WHERE alias_category='".$cagetory_name."' LIMIT 1 ";
		$cate_id = $db->fetchOne($sql);
		if(empty($cate_id)){
			$cate_id=0;
		}
		return $cate_id;
	}
	
	function getParentCategory($store_id){ // for get Category for store page
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT 
			cat.`parent`,
			catd.`title`,
			(SELECT cas.`alias_category` FROM `vd_category` AS cas WHERE cat.`parent` LIMIT 1) AS parentalias,
			ad.`category_id` 
			FROM `vd_ads` AS ad,
			`vd_category` AS cat,
			`vd_category_detail` AS catd
			WHERE cat.`id` = ad.`category_id` AND
			catd.`category_id` = cat.`parent` AND catd.`languageId`=$lang_id
			AND ad.store_id = $store_id
			GROUP BY cat.`parent`";
		return $db->fetchAll($sql);
	}
	function getChildCategory($cate){ // for
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT cat.`id`,cat.`alias_category`,catd.`title` FROM `vd_category` AS cat, 
		`vd_category_detail` AS catd
		WHERE cat.`id`=catd.`category_id` AND catd.`languageId`=$lang_id AND cat.`parent`=$cate ";
		return $db->fetchAll($sql);
	}
	
	function getAdsDetail($alias){ // for page ads detail
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		$province = $province_field[$lang_id];
		$this->_name='vd_ads';
		$sql="SELECT ads.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_phone,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
		cat.alias_category,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`parent`  AND catd.languageId =$lang_id LIMIT 1)  AS parent_category_name,
		(select parent.alias_category from vd_category as parent  where parent.id = cat.`parent` LIMIT 1 ) AS parent_alias_category,
		(SELECT $province FROM `vd_province` WHERE id= ads.province_id ) as province_name
		FROM $this->_name AS ads, `vd_category` AS cat  WHERE cat.`id` = ads.`category_id` AND ads.`alias`='$alias' LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAdsDetailById($ads_id,$store_id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `vd_ads_detail` WHERE ads_id=$ads_id AND status=1 ";
		return $db->fetchAll($sql);
	}
	function getRelatedAds($data,$store_id){// for page ads detail
		$this->_name='vd_ads';
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		$province = $province_field[$lang_id];
		$category = $data['category_id'];
		$adsid = $data['id'];
		$client= $this->getClientID();
		$sql="SELECT ads.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_phone,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`parent`  AND catd.languageId =$lang_id LIMIT 1)  AS parent_category_name,
		(SELECT $province FROM `vd_province` WHERE id= ads.province_id ) as province_name
		FROM $this->_name AS ads, `vd_category` AS cat  WHERE cat.`id` = ads.`category_id` AND ads.`user_id`=$client AND ads.`store_id`=$store_id AND ads.`category_id`=$category AND ads.id < $adsid  LIMIT 10";
		return $db->fetchAll($sql);
	}
	
	function updateBannerToStore($data){
		$client_id = $this->getClientID();
		$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
		$part= PUBLIC_PATH.'/images/store/banner/';
		$db = $this->getAdapter();
	
		try{
			
			$dbstore = new Application_Model_DbTable_DbGlobalselect();
			$store = $dbstore->getAllStoreByClient($client_id);
			
			if (!empty($store)) foreach ($store as $key=> $rs){
				$db->beginTransaction();
				$bannerhome_name = $_FILES['bannerhome'.$rs['alias_store']]['name'];
				$bannerhom_size = $_FILES['bannerhome'.$rs['alias_store']]['size'];
				
				$bannerdetail_name = $_FILES['bannerdetail'.$rs['alias_store']]['name'];
				$bannerdetail_size = $_FILES['bannerdetail'.$rs['alias_store']]['size'];
				
				$bannerhome= $rs['banner_home'];
				$bannerdetail= $rs['banner_detail'];
					list($txt, $ext) = explode(".", $bannerhome_name);
					if(in_array($ext,$valid_formats)) {//check ext
						
							if($bannerhome_name!=""){
								$temp = explode(".", $bannerhome_name);
								$newnamefile = "banner1".date("Y").date("m").date("d").round(microtime(true)).'.'.$ext;
								if(file_exists("$part.$newnamefile")) unlink("$part.$newnamefile");
								move_uploaded_file($_FILES['bannerhome'.$rs['alias_store']]["tmp_name"], $part . $newnamefile);
								$bannerhome = $newnamefile;
							}
					}
					list($txt, $ext) = explode(".", $bannerdetail_name);
					if(in_array($ext,$valid_formats)) {//check ext
						if($bannerdetail_name!=""){
							$temp = explode(".", $bannerdetail_name);
							$newnamefile1 = "banner2".date("Y").date("m").date("d").round(microtime(true)).'.'.$ext;
							if(file_exists("$part.$newnamefile1")) unlink("$part.$newnamefile1");
							move_uploaded_file($_FILES['bannerdetail'.$rs['alias_store']]["tmp_name"], $part . $newnamefile1);
							$bannerdetail = $newnamefile1;
						}
					}
					$arr = array(
							'banner_home'=>$bannerhome,
							'banner_detail'=>$bannerdetail,
							'modify_date'=>date("Y-m-d"),
					);
					$where=" client_id = $client_id AND id=".$rs['id'];
					$this->_name="vd_client_store";
					$this->update($arr, $where);
					$db->commit();
			}
			
			return 1;
		}catch(exception $e){
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}
?>