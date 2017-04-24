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
	function  getAllAds(){
		$db = $this->getAdapter();
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
			FROM `vd_ads` WHERE 1 ";
		$where='';
		$where.=" AND STATUS =1 AND is_expired=0 ";
		$order=' ORDER BY id DESC';
		return $db->fetchAll($sql.$where.$order);
	}
	function  getAllAdsByName($cagetory_name){
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
			FROM `vd_ads` WHERE 
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
	function getCategoryInfo($alias){
		$db= $this->getAdapter();
		$category_id = $this->categoryIdByName($alias);
		$lang_id = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`id` AND catd.languageId=$lang_id LIMIT 1) AS cate_name,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`parent` AND catd.languageId=$lang_id LIMIT 1) AS parent_cate_name,
		(select parent.alias_category from vd_category as parent  where parent.id = cat.`parent` LIMIT 1 ) AS parent_alias_category
		FROM `vd_category` AS cat WHERE cat.id = $category_id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAllLocation(){
		$this->_name='vd_province';
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		$lang_id = $this->getCurrentLang();
		$province_string = $province_field[$lang_id];
		$sql = " SELECT id,$province_string AS province_name FROM $this->_name WHERE status=1 AND (province_en_name!='' OR province_kh_name!='') ORDER BY id DESC ";
		$db = $this->getAdapter();
		return $db->fetchAll($sql);
	}
	function getAllStoreByUser(){
		$client_session=new Zend_Session_Namespace('client');
		$user_id = $client_session->client_id;
		$sql = " SELECT id,alias_store AS store_name FROM `vd_client_store` WHERE STATUS=1 AND client_id=$user_id ";
		$db = $this->getAdapter();
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
	function getAdsDetailById($ads_id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `vd_ads_detail` WHERE ads_id=$ads_id AND status=1 ";
		return $db->fetchAll($sql);
	}
	function addCountView($ads_alias){
		$ads = $this->getAdsDetail($ads_alias);
		$db = $this->getAdapter();
		$arr = array(
				"viewer"   =>$ads['viewer']+1,
			);
		$where = "alias = '$ads_alias'";
		$this->update($arr, $where);
		
	}
	function getRelatedAds($data){// for page ads detail
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
		$sql="SELECT ads.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ads.`user_id` LIMIT 1) AS author_phone,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ads.category_id AND languageId=$lang_id LIMIT 1) as category_name,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id = cat.`parent`  AND catd.languageId =$lang_id LIMIT 1)  AS parent_category_name,
		(SELECT $province FROM `vd_province` WHERE id= ads.province_id ) as province_name
		FROM $this->_name AS ads, `vd_category` AS cat  WHERE cat.`id` = ads.`category_id` AND ads.`category_id`=$category AND ads.id < $adsid  LIMIT 10";
		return $db->fetchAll($sql);
	}
	function getStringProvince(){
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		return $province_field[$lang_id];
	}
	function getSearchHomePage($search){
		$this->_name='vd_ads';
		$lang_id = $this->getCurrentLang();
		$db = $this->getAdapter();
		$province = $this->getStringProvince();
		$sql="SELECT ad.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_phone,
		(SELECT $province FROM `vd_province` WHERE id= ad.province_id ) as province_name,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ad.category_id AND languageId=$lang_id LIMIT 1) as category_name
		 FROM $this->_name AS ad WHERE ad.status=1 ";
		/*if(!empty($search['keywork_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['keywork_search']));
			$s_where[] = " ads_code LIKE '%{$s_search}%'";
			$s_where[] = " ads_title LIKE '%{$s_search}%'";
			$s_where[] = " price LIKE '%{$s_search}%'";
			$s_where[] = " description LIKE '%{$s_search}%'";
			$s_where[] = " street LIKE '%{$s_search}%'";
 			$where .=' AND ('.implode(' OR ',$s_where).')';
		}*/
		if($search['category_search']>-1){
			$sql.= " AND ad.category_id = ".$search['category_search'];
		}
		if($search['location_search']>-1){
			$sql.= " AND ad.province_id = ".$search['location_search'];
		}
		return $db->fetchAll($sql);
	}
	function getAdvanceSearch($search){
		$this->_name='vd_ads';
		$lang_id = $this->getCurrentLang();
		$db = $this->getAdapter();
		$province = $this->getStringProvince();
		$sql="SELECT ad.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_phone,
		(SELECT $province FROM `vd_province` WHERE id= ad.province_id ) as province_name,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ad.category_id AND languageId=$lang_id LIMIT 1) as category_name
		FROM $this->_name AS ad WHERE ad.status=1 ";
		/*if(!empty($search['keywork_search'])){
		 $s_where = array();
		$s_search = addslashes(trim($search['keywork_search']));
		$s_where[] = " ads_code LIKE '%{$s_search}%'";
		$s_where[] = " ads_title LIKE '%{$s_search}%'";
		$s_where[] = " price LIKE '%{$s_search}%'";
		$s_where[] = " description LIKE '%{$s_search}%'";
		$s_where[] = " street LIKE '%{$s_search}%'";
		$where .=' AND ('.implode(' OR ',$s_where).')';
		}*/
		if($search['category_search']>-1){
			$sql.= " AND ad.category_id = ".$search['category_search'];
		}
		if($search['location_search']>-1){
			$sql.= " AND ad.province_id = ".$search['location_search'];
		}
		return $db->fetchAll($sql);
	}
	function getAllSubcategory($cate_name='category_id',$maincateid){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `vd_category` WHERE  parent = $maincateid ";
		$rows = $db->fetchAll($sql);
		$s_where = array();
		$where='';
		if(!empty($rows)){
			foreach ($rows as $rs){
				$s_where[] = $cate_name." = {$rs['id']}";
			}
			$where .=' AND ('.implode(' OR ',$s_where).' )';
		}
		return $where;
	}
	function getAllAdvanceSearch($search,$string=null){
		$this->_name='vd_ads';
		$lang_id = $this->getCurrentLang();
		$db = $this->getAdapter();
		$province = $this->getStringProvince();
		$sql="SELECT ad.*,
		(SELECT vc.customer_name FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author,
		(SELECT vc.photo FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_photo,
		(SELECT vc.address FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_address,
		(SELECT vc.phone FROM `vd_client` vc WHERE vc.id = ad.`user_id` LIMIT 1) AS author_phone,
		(SELECT $province FROM `vd_province` WHERE id= ad.province_id LIMIT 1) as province_name,
		(SELECT district_name FROM `ln_district` WHERE dis_id=ad.district_id LIMIT 1) as district_name,
		(SELECT commune_name FROM `ln_commune` WHERE com_id=ad.commune_id) AS commune_name,
		(SELECT title FROM `vd_category_detail` WHERE category_id= ad.category_id AND languageId=$lang_id LIMIT 1) as category_name
		FROM $this->_name AS ad,vd_ads_detail as dt WHERE ad.status=1 AND ad.id=dt.ads_id ";
		if(!empty($search['keywork_search'])){
		 $s_where = array();
			$s_search = addslashes(trim($search['keywork_search']));
			$s_where[] = " ad.ads_code LIKE '%{$s_search}%'";
			$s_where[] = " ad.ads_title LIKE '%{$s_search}%'";
			$s_where[] = " ad.price LIKE '%{$s_search}%'";
			$s_where[] = " ad.description LIKE '%{$s_search}%'";
// 			$s_where[] = " street LIKE '%{$s_search}%'";
			$sql .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['category_search']>-1){
			$sub_cate = $this->getAllSubcategory('ad.category_id',$search['category_search']);
			if(!empty($sub_cate)){//parent
				$sql.= " ".$sub_cate;
			}else{//sub cate
				$sql.= " AND ad.category_id = ".$search['category_search'];
			}
			$rscontrol = $this->getcontrollSearch($search['category_search'],1);//ទាញយក Controll តាម Cate // search in ads_detail
			if(!empty($rscontrol)){
				$s_where=array();
				foreach ($rscontrol as $controll){
					if(!empty($search[$controll['title']])){//តម្លៃក្នុង Controll មានតម្លៃ
						$s_where[] .=" dt.control_name = '".$controll['title']."'";
						//$sql.=" AND dt.control_name = ".$search[$controll['title']];
					}
					
				}
// 				$where .=' '.implode(' OR ',$s_where).'';
				$sql .=' AND ( '.implode(' OR ',$s_where).' )';
			}
		}
		if($search['location_search']>-0){
			$sql.= " AND ad.province_id = ".$search['location_search'];
		}
		if($search['district']>-0){
			$sql.= " AND ad.district_id = ".$search['district'];
		}
		if($search['commune']>-0){
			$sql.= " AND ad.commune_id = ".$search['commune'];
		}
		$sql.=" GROUP BY ad.id ";
		$rs = $db->fetchAll($sql);
		if($string!=null){
			return $this->viewProductForm($rs);
		}else{
			return $rs;
		}
	}
	function viewProductForm($result){
		if(!empty($result)){
			$tr = Application_Form_FrmLanguages::getCurrentlanguage();
			$str='';
			 foreach($result as $rs){
				$str.= "<li class='item-wrap'>
				<div class='item ad-box'>
				<div class='item-image'>";
				$base_url=Zend_Controller_Front::getInstance()->getBaseUrl();
				$str.='<a href="'.$base_url.'/listads/detail/ads/'.$rs["alias"].'">';
					if (!empty($rs['image_wfeature'])){
						$str.="<img src=".$base_url.'/images/adsimg/'.$rs['image_feature']." />";
					}else{
						$str.="<img src=".$base_url.'/images/adsimg/noimagefound.jpg'." />";
					}
					$str.='</a>';
					$str.='<div class="additional-buttons">
						<span class="quickview" onClick="getAdsDetail('.$rs['id'].');" ><i class="fa fa-search-plus"></i>'.$tr->translate("Quickview").'</span>
							<div class="sold-by-container">
								<a title="Channy" href="#"><span>'.$tr->translate("Posted By").'</span>'.$rs['author'].'</a>
							</div>
						</div>';
					$str.='</div>
						<div class="item-price">
							<span>$'.number_format($rs['price'],2).'</span>
						</div> 
						<div class="item-description">
							<div class="item-title">
								<h3>';
								$str.='<a href="">'.substr($rs["ads_title"],0,100).'</a>
								</h3>
							</div>';
					$str.='<div class="item-info">
							<p class="description">';
							$str1 = utf8_encode($rs['description']);
							$description = substr($str1, 0, 600);
							 utf8_decode($description); 
					$str.='</p>
							<ul>
							   <li>'.$rs['category_name'].'</li>
							   <li>'.$rs['province_name'].'</li>
							   <li>'.$rs['district_name'].'</li>
							   <li>'.$rs['commune_name'].'</li>
							</ul>
						</div>
					</div><!-- item-description -->
					<div class="footerads" >
							<ul>
							   <li> <i class="fa fa-eye"></i>'.$rs['viewer'].' Views</li>
							   <li> <i class="fa fa-clock-o"></i>'.date("M d, Y",strtotime($rs["publish_date"])).'</li>
							</ul>
					</div>
					</div><!-- item -->
		    	</li><!-- item-wrap -->';
			}
			return $str;
		}									    			
	}
	function getcontrollSearch($cate_id,$search=null){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_field_type` AS ft WHERE FIND_IN_SET($cate_id,ft.`category`) AND ft.`status`=1 ";
		if($search!=null){
			$sql.=" AND ft.is_search=1 ";
		}
		return $db->fetchAll($sql);
	}
	function getBannerByPosition($position){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM `vd_banneravertise` WHERE STATUS=1 AND position_id=$position ";
		return $db->fetchAll($sql);
	}
	function getAdsByUserid($user_id){
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
		FROM $this->_name AS ads, `vd_category` AS cat  WHERE cat.`id` = ads.`category_id` AND ads.user_id = $user_id ";
		return $db->fetchAll($sql);
	} 
	function getMenuItems(){ //  for menu front
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT md.title FROM `vd_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=$lang_id LIMIT 1) AS title
		 FROM `vd_menu` AS m WHERE m.`status`=1 ";
		return $db->fetchAll($sql);
	}
	function getMenuItemsByAlias($alias){ //  for Controler page index
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_menu` AS m WHERE m.`alias_menu`='$alias' limit 1";
		return $db->fetchRow($sql);
	}
	function getArcticleByCate($cateId){ //  for Controler page index
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT *,
			(SELECT ad.title FROM `vd_article_detail` AS ad WHERE ad.articleId = a.`id` AND ad.language_id=$lang_id LIMIT 1) AS title,
			(SELECT ad.description FROM `vd_article_detail` AS ad WHERE ad.articleId = a.`id` AND ad.language_id=$lang_id LIMIT 1) AS description
			 FROM `vd_article` AS a WHERE a.`status`=1 AND a.`category_id`=$cateId ";
		return $db->fetchAll($sql); 
	}
	function deleteMyadsById($adsid){
		$client_session=new Zend_Session_Namespace('client');
		$user_id = $client_session->client_id;
		$this->_name='vd_ads';
		$where =" id = $adsid AND user_id = ".$user_id;
		return $this->delete($where);
	}
	function renewMyadsById($adsid){
		$client_session=new Zend_Session_Namespace('client');
		$user_id = $client_session->client_id;
		$this->_name='vd_ads';
		$where =" id = $adsid AND user_id = ".$user_id;
		
		$now =  date("Y-m-d");
		$expired = date("Y-m-d", strtotime(" $now +14 day"));
		$arr =array(
				'viewer'=>0,
				'is_expired'=>0,
				'publish_date'=>date("Y-m-d"),
				'expired_date'=>$expired,
				'date_modified'=>date("Y-m-d"),
				);
		 $this->update($arr, $where);
	}
	public function getVewOptoinTypeByType($type=null,$option = null,$limit =null,$first_option =null){
		$db = $this->getAdapter();
		$sql="SELECT id,key_code,CONCAT(name_en) AS name_en ,displayby FROM `vd_view` WHERE status =1 AND name_en!='' ";//just concate
		if($type!=null){
			$sql.=" AND type = $type ";
		}
		if($limit!=null){
			$sql.=" LIMIT $limit ";
		}
		$rows = $db->fetchAll($sql);
		if($option!=null){
			$options=array();
			if($first_option==null){//if don't want to get first select
				$options=array(''=>"-----ជ្រើសរើស-----",-1=>"Add New",);
			}
			if(!empty($rows))foreach($rows AS $row){
				$options[$row['key_code']]=$row['name_en'];//($row['displayby']==1)?$row['name_kh']:$row['name_en'];
			}
			return $options;
		}else{
			return $rows;
		}
	}	
// 	function getMyStore($client_id){
// 		$db = $this->getAdapter();
// 		$sql="SELECT cs.*,c.`customer_name`,st.`template_main_color`,st.`template_main_font_color`
// 			,st.`template_title`,st.`image_theme`
// 			FROM `vd_client_store` AS cs,
// 			`vd_client` AS c,
// 			`vd_sub_template` AS st
// 			WHERE cs.`status`=1 AND c.`id`=cs.`client_id`
// 			AND st.`id`=cs.`template_id` AND cs.`client_id`=$client_id";
// 		return $db->fetchAll($sql);
// 	}
	function getMyStoreByAlias($client_id,$alias){
		$db = $this->getAdapter();
		$sql="SELECT cs.*,c.`customer_name`,st.`template_main_color`,st.`template_main_font_color`
		,st.`template_title`,st.`image_theme`
		FROM `vd_client_store` AS cs,
		`vd_client` AS c,
		`vd_sub_template` AS st
		WHERE cs.`status`=1 AND c.`id`=cs.`client_id`
		AND st.`id`=cs.`template_id` AND cs.`client_id`=$client_id and cs.`alias_store`= '$alias' ";
		return $db->fetchRow($sql);
	}
	function getAlltemplate(){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_sub_template` AS t WHERE t.`status`=1";
		return $db->fetchAll($sql);
	}
}
?>