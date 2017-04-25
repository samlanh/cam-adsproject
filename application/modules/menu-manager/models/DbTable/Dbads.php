<?php

class MenuManager_Model_DbTable_Dbads extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_article';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    function getStringProvince(){
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	return $db->getStringProvince();
    }
    public function getAllAds($search){
    	$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
// 		$province_field = array(
// 				"1"=>"province_en_name",
// 				"2"=>"province_kh_name"
// 		);
		$province = $this->getStringProvince();
// 		$province = $province_field[$lang_id];
		$sql=" SELECT *,
			(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = `user_id` LIMIT 1) AS author,
			(SELECT title FROM `vd_category_detail` WHERE category_id=vd_ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
			(SELECT $province FROM `vd_province` WHERE id=vd_ads.province_id LIMIT 1) as province_name,
			(SELECT name_en FROM `vd_view` WHERE TYPE=1 AND key_code=vd_ads.status LIMIT 1) As status,
			(SELECT customer_name FROM `vd_client` WHERE id=vd_ads.user_id LIMIT 1) AS customer_name,
			(SELECT name_en FROM `vd_view` WHERE TYPE=4 AND key_code=vd_ads.is_suspend LIMIT 1) As status_use
			FROM `vd_ads` WHERE 1 ";
		$where='';
		$where.="  ";
		$order=' ORDER BY id DESC';
    	$where='';
//     	echo $sql;exit();
//     	if ($search['status_search']!=""){
//     		$where.=" AND act.`status` =".$search['status_search'];
//     	}
//     	if ($search['category']>0){
//     		$where.=" AND act.`category_id` =".$search['category'];
//     	}
//     	$order = " ORDER BY act.`id` DESC";
    	$order="";
    	return $db->fetchAll($sql.$where.$order);
    }
    function addAds($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
// 	function getArticleById($id){
// 		$db= $this->getAdapter();
// 		$sql="SELECT * FROM $this->_name WHERE id =".$id;
// 		return $db->fetchRow($sql);
// 	}
// 	function getArticleTitleByLang($article_id,$lang){
// 		$db = $this->getAdapter();
// 		$sql="SELECT acd.`title`,acd.`language_id`,acd.description FROM `vd_article_detail` AS acd WHERE acd.`articleId`=$article_id AND acd.`language_id`=$lang";
// 		return $db->fetchRow($sql);
// 	}
// 	public function CheckTitleAlias($alias){
// 		$db =$this->getAdapter();
// 		$sql = "SELECT c.`id` FROM $this->_name AS c WHERE c.`alias_article`= '$alias'";
// 		return $db->fetchRow($sql);
// 	}
// 	function deleteAds($id){
// 		$db = $this->getAdapter();
// 		$db->beginTransaction();
// 		try{
// 			$arr = array(
// 					'status'=>-1,
// 			);
// 				$where = " id =".$id;
// 				$this->update($arr, $where);
// 			$db->commit();
// 		}catch(exception $e){
// 			Application_Form_FrmMessage::message("Application Error");
// 			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
// 			$db->rollBack();
// 		}
// 	}
}

