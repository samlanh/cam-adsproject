<?php

class Application_Model_DbTable_DbGlobalselect extends Zend_Db_Table_Abstract
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
	function  getAllAdsByName($cagetory_name){
		$db = $this->getAdapter();
		$category_id = $this->categoryIdByName($cagetory_name);
		$lang_id = $this->getCurrentLang();
		$sql=" SELECT *,
			(SELECT title FROM `vd_category_detail` WHERE category_id=vd_ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
			(SELECT province_en_name FROM `vd_province` WHERE id=vd_ads.province_id ) as province_name
			FROM `vd_ads` WHERE 
			category_id=$category_id
			AND STATUS =1 AND is_expired=0 ORDER BY id DESC ";
		return $db->fetchAll($sql);
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
	function getAllLocation(){
		$this->_name='vd_province';
		$sql = " SELECT id,province_en_name, province_kh_name FROM $this->_name WHERE status=1 AND (province_en_name!='' OR province_kh_name!='') ORDER BY id DESC ";
		$db = $this->getAdapter();
		return $db->fetchAll($sql);
	}
	
}
?>