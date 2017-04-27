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

