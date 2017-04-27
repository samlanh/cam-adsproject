<?php
class MenuManager_AdsController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	private $sex=array(1=>'M',2=>'F');
	public function indexAction(){
		try{
			$db = new MenuManager_Model_DbTable_Dbads();
			if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
			}else{
				$data = array(
						'advance_search'=>'',
						'province'=>-1,
						'category_search'=>-1,
						'province_id'=>-1,
						'district'=>-1,
						'commune'=>-1,
						'status_used'=>-1,
						'start_date'=>date("Y-m-d"),
						'end_date'=>date("Y-m-d"),
						
				);
			}
			$this->view->search= $data;
			$this->view->rsads= $db->getAllAds($data);
			
// 			$db = new Application_Model_DbTable_DbGlobalselect();
// 			$this->view->rsads = $db->getAllAdvanceSearch($data);
			
			$dbg = new Application_Model_DbTable_DbVdGlobal();
			$this->view->rscate = $dbg->getCategory();
			
			$dbl = new Application_Model_DbTable_DbGlobalselect();
			$this->view->rslocation =$dbl->getAllLocation();
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new MenuManager_Form_FrmMenu();
		$frm_manager=$frm->FrmMenuManager();
		Application_Model_Decorator::removeAllDecorator($frm_manager);
		$this->view->frm = $frm_manager;
  }
  public function chooseCategoryAction(){ // choose category before go to write post ads
  	$this->_helper->layout()->disableLayout();
  	$dbg = new Application_Model_DbTable_DbGlobalsetting();
  	$allow_post = $dbg->getSystemSetting('allow_post');
  	 
  	$paramid = $this->getRequest()->getParam('cateid');
  	$this->view->cateid = $paramid;
  	$client_session=new Zend_Session_Namespace('client');
  	if(!empty($client_session->client_id)){ //check session has been have or not
  		$db = new Application_Model_DbTable_DbVdGlobal();
  		$this->view->parentcate = $db->getCategoryParent();
  
  		$session_post=new Zend_Session_Namespace('postads');
  		$this->view->success = $session_post->success;
  	}else{
  		$requestpost=new Zend_Session_Namespace('requestpost');
  		$requestpost->requestpost = 1;
  		$this->_redirect("index/login");
  	}
  }
  public function addAction(){
  		$this->_helper->layout()->disableLayout();
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$dbp = new Application_Model_DbTable_DbPostAds();
    		$result = $dbp->addPostsAds($data);
    		if(!empty($data['saveclose'])){
    			$this->_redirect("/dashboard/myads/".$result);
    		}else{
    			$session_post=new Zend_Session_Namespace('postads');
    			$session_post->success=$result;
    			$this->_redirect("/postsads/choose-category/".$result);
    			//Application_Form_FrmMessage::Sucessfull("Your ad has been insert success", "/postsads/choose-category/");
    		}
    	}
    	$param = $this->getRequest()->getParam('category');
    	if(!empty($param)){ //check session has been have or not
	    	
	    	$db = new Application_Model_DbTable_DbVdGlobal();
	    	
	    	$cate = $db->getCategoryIdbyAlias($param);
	    	$this->view->cate = $cate;
	    	
	    	$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
	    	$this->view->form = $dbform->getAllFormTypeCateid($cate['id']);
	    	
	    	$db = new Application_Model_DbTable_DbClient();
	    	$this->view->client_info = $db->getClientInfo(null,$client_session->client_id);
	    	
	    	$db = new Application_Model_DbTable_DbGlobalselect();
	    	$this->view-> rslocation = $db->getAllLocation();
	    	$this->view->rsstore = $db->getAllStoreByUser();
    	}else{
    		$this->_redirect("menu-manager/ads/choose-category");
    	}
    	$dbg = new Application_Model_DbTable_DbGlobalsetting();
    	$this->view->rsallowimg  =  $dbg->getSystemSetting('allow_image',1);
  
  }
  public function editAction(){
  	$id = $this->getRequest()->getParam('id');
  	try{
  		$db = new MenuManager_Model_DbTable_DbMenuManager();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$_data['id']=$id;
  			$db->addMainMenu($_data);
  			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/menu-manager/index");
  		}
  		$row = $db->getMainMenuById($id);
  		$frm = new MenuManager_Form_FrmMenu();
  		$frm_manager=$frm->FrmMenuManager($row);
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  public function deleteAction(){
  	try{
  		$id = $this->getRequest()->getParam('id');
  		$db = new MenuManager_Model_DbTable_DbMenuManager();
  		if(!empty($id)){
  			$db->deleteMenu($id);
  			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/menu-manager/index");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  
  
}

