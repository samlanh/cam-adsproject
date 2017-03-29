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
	
// 	public function loadImageFromDir($data){
// 		$str="";
// 		$dir=opendir($data['mainDir'].DIRECTORY_SEPARATOR.$data['dir']);
// 		while(@$entitys=readdir($dir))
// 		{
// 			$arraydir[]=$entitys;
// 		}
// 		closedir($dir);
// 		$contarray=count($arraydir);
// 		for($all=0;$all<$contarray;$all++){
// 			if($arraydir[$all] !=='..' and $arraydir[$all]!=="." ){
// 				$file_parts = pathinfo($arraydir[$all]);
// 				if(empty($file_parts['extension']))	{
// 						$str.="<div  class='blockimagelink'style='cursor: pointer; display: inline-block;border: solid 1px #ccc;text-align: center;padding: 10px;width: 100px;'>
// 								<img style=' width: 55px; height: 52px;' src='".$this->baseUrl()."/images/folder.png' width='30px' />
// 								<p>".$arraydir[$all]."</p>
// 							</div>";
// 				}else{
// 					$str.="<div onClick='getImageLink('".$arraydir[$all]."');' class='blockimagelink' style='cursor: pointer; display: inline-block;border: solid 1px #ccc;text-align: center;padding: 10px;width: 100px;'>
// 							str.=<img style=' height: 50px;border:1px solid #ccc;' title='".$arraydir[$all]."' src='".$this->baseUrl()."/images/directory/'".$arraydir[$all]." />
// 									<p>".substr($arraydir[$all],0,15)."</p>
// 							</div>";	
// 				}    
// 			}
// 		}
// 		$arr = array("image"=>$str,);
// 		return $arr;
// 	}
	function getFieldTypeSelect(){ // for front (form select option)
		$db = $this->getAdapter();
		$sql="SELECT ft.`id`,ft.`title` AS name  FROM `vd_field_type`  AS ft WHERE ft.`status`=1 AND ft.type IN ('select','cascade')";
		return $db->fetchAll($sql);
	}
	function getCategoryParent(){// get parent category for frontend
		$language =1;
		$db = $this->getAdapter();
		$sql="SELECT cat.`id`,cat.`parent`,cat.`alias_category`,cat.`fontawsome_icon`,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id= cat.`id` AND catd.languageId=$language LIMIT 1) AS `name`
		 FROM `vd_category` AS cat WHERE cat.`status`=1 AND cat.`parent`=0";
		return $db->fetchAll($sql);
	}
	function getSubCateByParent($parent){
		$language =1;
		$db = $this->getAdapter();
		$sql="SELECT cat.`id`,cat.`parent`,cat.`alias_category`,cat.`fontawsome_icon`,
		(SELECT catd.title FROM `vd_category_detail` AS catd WHERE catd.category_id= cat.`id` AND catd.languageId=$language LIMIT 1) AS `name`
		FROM `vd_category` AS cat WHERE cat.`status`=1 AND cat.`parent`=$parent";
		return $db->fetchAll($sql);
	}
}
?> 