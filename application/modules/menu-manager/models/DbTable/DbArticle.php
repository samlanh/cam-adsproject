<?php

class MenuManager_Model_DbTable_DbArticle extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_article';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function getAllArticle($search){
    	$db=$this->getAdapter();
    	$sql="SELECT
			act.`id`,
			(SELECT cd.title FROM `vd_category_detail` AS cd WHERE cd.category_id = act.`category_id` AND languageId=1 LIMIT 1) AS category,
			(SELECT ad.title FROM `vd_article_detail` AS ad WHERE ad.articleId = act.`id` AND language_id=1 LIMIT 1) AS title,
			act.`status`
			 FROM `vd_article` AS act 
			 WHERE act.`status`>-1";
    	$where='';
    	if ($search['status_search']!=""){
    		$where.=" AND act.`status` =".$search['status_search'];
    	}
    	if ($search['category']>0){
    		$where.=" AND act.`category_id` =".$search['category'];
    	}
    	$order = "  ORDER BY act.`id` DESC";
    	return $db->fetchAll($sql.$where.$order);
    }
    function addArticle($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		if (!empty(trim($data['title_alias']))){
    			$alias = $data['title_alias'];
    		}else{
    			$alias = md5(date("Y-m-d H:i:s"));
    		}
	    	$arr = array(
	    			'category_id'=>$data['category'],
					'alias_article'=>$alias,
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d"),
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="vd_article";
	    	 if (!empty($data['id'])){
	    		 	$where=" id=".$data['id'];
	    		 	$this->update($arr, $where);
	    		 	$article_id =$data['id'];
	    		 	
	    		 	$this->_name="vd_article_detail";
	    		 	$where1=" articleId=".$data['id'];
	    		 	$this->delete($where1);
	    	}else{
	    			$this->_name="vd_article";
	    		 	$arr['create_date']= date("Y-m-d");
	    			$article_id = $this->insert($arr);
	    	}
	    	if(!empty($lang)) foreach($lang as $row){
	    			$title = str_replace(' ','',$row['title']);
	    			$arr_article = array(
	    					'articleId'=>$article_id,
	    					'title'=>$data['title'.$title],
	    					'description'=>$data['description'.$title],
	    					'language_id'=>$row['id'],
	    			);
	    			 $this->_name="vd_article_detail";
	    			$this->insert($arr_article);
	    		}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getArticleById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM $this->_name WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getArticleTitleByLang($article_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT acd.`title`,acd.`language_id`,acd.description FROM `vd_article_detail` AS acd WHERE acd.`articleId`=$article_id AND acd.`language_id`=$lang";
		return $db->fetchRow($sql);
	}
	public function CheckTitleAlias($alias){
		$db =$this->getAdapter();
		$sql = "SELECT c.`id` FROM $this->_name AS c WHERE c.`alias_article`= '$alias'";
		return $db->fetchRow($sql);
	}
	function deleteArticle($id){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr = array(
					'status'=>-1,
			);
				$where = " id =".$id;
				$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}

