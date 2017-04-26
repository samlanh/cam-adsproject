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
		$sql=" SELECT ad.*,
			(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author,
			(SELECT title FROM `vd_category_detail` WHERE category_id=ad.category_id AND languageId=$lang_id LIMIT 1) as category_name,
			(SELECT $province FROM `vd_province` WHERE id=ad.province_id LIMIT 1) as province_name,
			(SELECT name_en FROM `vd_view` WHERE TYPE=1 AND key_code=ad.status LIMIT 1) As status,
			(SELECT customer_name FROM `vd_client` WHERE id=ad.user_id LIMIT 1) AS customer_name,
			(SELECT name_en FROM `vd_view` WHERE TYPE=4 AND key_code=ad.is_suspend LIMIT 1) As status_use
			FROM `vd_ads`  AS ad WHERE 1 ";
	
		$from_date =(empty($search['start_date']))? '1': " ad.publish_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " ad.publish_date <= '".$search['end_date']." 23:59:59'";
    	
		$where= " AND ".$from_date." AND ".$to_date;
    	if ($search['advance_search']!=""){
    		$s_where = array();
    		$s_search = addslashes(trim($search['advance_search']));
    		$s_where[]=" ad.ads_code LIKE '%{$s_search}%'";
    		$s_where[]=" ad.ads_title LIKE '%{$s_search}%'";
    		$s_where[]=" ad.price LIKE '%{$s_search}%'";
    		$s_where[]=" ad.description LIKE '%{$s_search}%'";
    		$where .=' AND '.implode(' OR ',$s_where).'';
    	}
    	if ($search['category_search']>0){
    		$where.=" AND ad.`category_id` =".$search['category_search'];
    	}
    	if ($search['status_used']>-1){
    		$where.=" AND ad.`is_suspend` =".$search['status_used'];
    	}
    	if ($search['province']>0){
    		$where.=" AND ad.`province_id` =".$search['province'];
    	}
    	if ($search['district']>0){
    		$where.=" AND ad.`district_id` =".$search['district'];
    	}
    	if ($search['commune']>0){
    		$where.=" AND ad.`commune_id` =".$search['commune'];
    	}
    	
//     	if ($search['commune']>0){
//     		$where.=" AND ad.`category_id` =".$search['commune'];
//     	}
//     	if ($search['commune']>0){
//     		$where.=" AND ad.`commune_id` =".$search['commune'];
//     	}
//     	if ($search['commune']>0){
//     		$where.=" AND ad.`category_id` =".$search['commune'];
//     	}
    	
    	
//     	$order = " ORDER BY act.`id` DESC";
    	$order="";
//     	echo$sql.$where.$order;
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

