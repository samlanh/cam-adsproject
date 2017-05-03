<?php

class MenuManager_Model_DbTable_Dbstore extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_client_store';
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
    public function getAllStore($search){
    	$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$province = $this->getStringProvince();
// 		$province = $province_field[$lang_id];
		$sql=" SELECT 
		   s.id,s.store_title,s.store_phone,s.social_media,s.store_email,
			(SELECT customer_name FROM `vd_client` WHERE id=s.client_id LIMIT 1) AS customer_name,
			s.date,
			(SELECT name_en FROM `vd_view` WHERE TYPE=1 AND key_code=s.status LIMIT 1) As status
			FROM `vd_client_store`  AS s WHERE 1 ";
	
// 		$from_date =(empty($search['start_date']))? '1': " ad.publish_date >= '".$search['start_date']." 00:00:00'";
// 		$to_date = (empty($search['end_date']))? '1': " ad.publish_date <= '".$search['end_date']." 23:59:59'";
    	
// 		$where= " AND ".$from_date." AND ".$to_date;
//     	if ($search['advance_search']!=""){
//     		$s_where = array();
//     		$s_search = addslashes(trim($search['advance_search']));
//     		$s_where[]=" ad.ads_code LIKE '%{$s_search}%'";
//     		$s_where[]=" ad.ads_title LIKE '%{$s_search}%'";
//     		$s_where[]=" ad.price LIKE '%{$s_search}%'";
//     		$s_where[]=" ad.description LIKE '%{$s_search}%'";
//     		$where .=' AND '.implode(' OR ',$s_where).'';
//     	}
//     	if ($search['category_search']>0){
//     		$where.=" AND ad.`category_id` =".$search['category_search'];
//     	}
//     	if ($search['status_used']>-1){
//     		$where.=" AND ad.`is_suspend` =".$search['status_used'];
//     	}
//     	if ($search['province']>0){
//     		$where.=" AND ad.`province_id` =".$search['province'];
//     	}
//     	if ($search['district']>0){
//     		$where.=" AND ad.`district_id` =".$search['district'];
//     	}
//     	if ($search['commune']>0){
//     		$where.=" AND ad.`commune_id` =".$search['commune'];
//     	}
		$where="";
    	$order="";
    	return $db->fetchAll($sql.$where.$order);
    }
    function addNewstore($data){
    	$client_session=new Zend_Session_Namespace('client');
    	$client_id = $client_session->client_id;
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		
//     		$adsid = $this->getLastAdsId();
    		$adsidalias = str_replace(" ",'', $data['store_name']);
    			
    		$arr = array(
    				"alias_store"   => $adsidalias,
    				"store_title"   => $data['store_name'],
    				"store_email"   => $data['store_email'],
//     				"banner_home"   => $data['ads_code'],
//     				"alias_store"   => $data['ads_code'],
    				"social_media"   => $data['social_media'],
    				"banner_detail" => $data['banner_detail'],
    				"client_id"     => $data['client'],
    				"template_id"   => $data['template_id'],
    				"logo_store"    => $data['logo'],
    				"store_phone"   => $data['store_phone'],
    				"store_policy"  => $data['store_policy'],
    				"create_date"   => date("Y-m-d"),
    				"template_color"=>$data['template_color'],
    				"store_about_us"  => $data['about'],
    				"store_address"  => $data['address'],
//     				"expired_date"  => date("Y-m-d", strtotime(" date('Y-m-d') +15 day")),
    				"status"          =>1,
    				"user_id"          =>$client_id,
    				'store_industry_type'=>$data['store_industry_type'],
    		);
    		$this->insert($arr);
    		$db->commit();
    		return 1;
    	}catch(exception $e){
    		echo $e->getMessage();exit();
    		Application_Form_FrmMessage::message("Your Submit Error!");
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

