<?php

class Application_Model_DbTable_DbVdGlobal extends Zend_Db_Table_Abstract
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
		$lang = $session_lang->lang_id;
		if(empty($lang)){
			$session_lang->lang_id=1;
			return 1;
		}else{return $lang;}
	}
	function getStringProvince(){
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		return $province_field[$lang_id];
	}
	public function getLaguage(){// get language active for front and backend
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_language` AS l WHERE l.`status`=1";
		return $db->fetchAll($sql);
	}
	public function getMenuManager(){ // for backend 
		$db = $this->getAdapter();
		$sql="SELECT mg.`id`,mg.`title` AS `name` FROM `vd_menu_manager` AS mg WHERE mg.`status`=1";
		return $db->fetchAll($sql);
	}
	public function getMenuItems($parent = 0, $spacing = '', $cate_tree_array = ''){ // for backend 
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT m.`id`,
			(SELECT md.title FROM `vd_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=1 LIMIT 1) AS name,m.`parent`
			 FROM `vd_menu` AS m WHERE m.`status`=1 AND m.`parent` = $parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getMenuItems($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	public function  getAllArticle(){ // for backend 
		$db = $this->getAdapter();
		$sql="SELECT *,
		(SELECT ad.title FROM `vd_article_detail` AS ad WHERE ad.articleId = c.`id` AND ad.language_id =1 LIMIT 1) AS title
		 FROM `vd_article` AS c WHERE c.`status`=1";
		return $db->fetchAll($sql);
	}
	function getMenuType(){ // for backend 
		$db=$this->getAdapter();
		$sql="SELECT mt.`id`,mt.`title` AS name FROM `vd_menu_type` AS mt WHERE mt.`status`=1";
		return $db->fetchAll($sql);
	}
	
	public function getCategory($parent = 0, $spacing = '', $cate_tree_array = ''){ // for backend 
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$language = $this->getCurrentLang();
		
		$sql="SELECT c.`id`,
		(SELECT cd.title FROM `vd_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=$language LIMIT 1) AS name,
		c.`parent` FROM `vd_category` AS c WHERE c.`status`=1 AND c.cate_type=1 AND  c.`parent`=$parent ORDER BY id ASC";
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
	public function getCategoryForMenu($parent = 0, $spacing = '', $cate_tree_array = ''){ // for backend menu item category for menu
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$language = $this->getCurrentLang();
	
		$sql="SELECT c.`id`,
		(SELECT cd.title FROM `vd_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=$language LIMIT 1) AS name,
		c.`parent` FROM `vd_category` AS c WHERE c.`status`=1 AND c.cate_type=2 AND c.`parent`=$parent ORDER BY id ASC";
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
	function  getAllAdsToHomepage($category_id){
		$db = $this->getAdapter();
		$province = $this->getStringProvince();
		$lang_id = $this->getCurrentLang();
		$sql=" SELECT *,
		(SELECT customer_name FROM `vd_client` WHERE id=`vd_ads`.user_id LIMIT 1) as suppliyer_name,
		(SELECT id FROM `vd_client` WHERE id=1 LIMIT 1) as suppliyerid,
		(SELECT title FROM `vd_category_detail` WHERE category_id=vd_ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
		(SELECT $province FROM `vd_province` WHERE id=vd_ads.province_id LIMIT 1 ) as province_name,
		(SELECT cs.alias_store FROM `vd_client_store` AS cs WHERE cs.id = vd_ads.store_id LIMIT 1 ) AS store_alias
		FROM `vd_ads` WHERE
		(select  vdc.parent from vd_category as vdc where vdc.id = category_id LIMIT 1)=$category_id
		AND STATUS =1 AND is_expired=0 ORDER BY id DESC LIMIT 15 ";
		return $db->fetchAll($sql);
	}
	function getAllcontrollByCategoryId($cate_id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_field_type` AS ft WHERE FIND_IN_SET($cate_id,ft.`category`) AND ft.`status`=1 ";
		return $db->fetchAll($sql);
	}
	function getFieldTypeSelect(){ // for front (form select option)
		$db = $this->getAdapter();
		$sql="SELECT ft.`id`,ft.`title` AS name  FROM `vd_field_type`  AS ft WHERE ft.`status`=1 AND ft.type IN ('select','cascade')";
		return $db->fetchAll($sql);
	}
	function getCategoryParent($parent=0,$homepage=null){// get parent category for frontend
		$language = $this->getCurrentLang();
		$db = $this->getAdapter();
		$sql="SELECT cat.`id`,cat.`parent`,
		(SELECT alias_category FROM `vd_category` WHERE id=cat.`parent` LIMIT 1) AS parent_alias,
		cat.`alias_category`,cat.`fontawsome_icon`,cat.id as category_id,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id= cat.`id` AND catd.languageId=$language LIMIT 1) AS `name`
		 FROM `vd_category` AS cat WHERE cat.`status`=1 and cat.`cate_type`=1";
		if($homepage!=null){
			$sql.=" AND cat.is_showhomepage=1 ";
		}
		if($parent==0){//get parent
			$sql.=" AND cat.`parent`=0 ";
		}else{//get subcagegory
			$sql.=" AND cat.`parent`!=0 ";
		}
		$order = " ORDER BY cat.`parent` ASC,cat.ordering ASC ";
// 		echo $sql;exit();
		return $db->fetchAll($sql.$order);
	}
	function getSubCateByParent($parent){
		$language =1;
		$db = $this->getAdapter();
		$sql="SELECT cat.`id`,cat.`parent`,cat.`alias_category`,cat.`fontawsome_icon`,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id= cat.`id` AND catd.languageId=$language LIMIT 1) AS `name`
		FROM `vd_category` AS cat WHERE cat.`status`=1 AND cat.`parent`=$parent";
		return $db->fetchAll($sql);
	}
	function getCategoryIdbyAlias($alias){ // get id from alias on write ads action
		$language =1;
		$db = $this->getAdapter();
		$sql="SELECT *,
				(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id= c.`id` AND catd.languageId=$language LIMIT 1) AS `name`,
				(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id= c.`parent` AND catd.languageId=$language LIMIT 1) AS `parent_name` FROM `vd_category` AS c WHERE c.`alias_category`='$alias' LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAllDistrictByProvince($province_id){
		$language = $this->getCurrentLang();
		$lang_name=array(
				1=>'district_name',
				2=>'district_namekh'
				);
		$string_dis = $lang_name[$language];
		$db = $this->getAdapter();
		$sql="SELECT dis_id AS id , $string_dis AS district_name FROM `ln_district` WHERE status =1 AND pro_id = $province_id  ORDER BY district_namekh ASC";
		return $db->fetchAll($sql);
	}
	function getAllCommunebyDistict($district_id){
		$language = $this->getCurrentLang();
		$lang_name=array(
				1=>'commune_name',
				2=>'commune_namekh'
		);
		$string_dis = $lang_name[$language];
		$db = $this->getAdapter();
		$sql="SELECT com_id AS id, $string_dis AS commune_name FROM `ln_commune` WHERE status =1 AND district_id= $district_id ORDER BY $string_dis ASC ";
		//return $sql;
		return $db->fetchAll($sql);
	}
}
?> 