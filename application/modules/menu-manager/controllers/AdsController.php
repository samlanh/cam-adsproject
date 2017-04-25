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
						'keywork_search'=>'',
						'location_search'=>-1,
						'category_search'=>-1,
						'province_id'=>-1,
						'district'=>-1,
						'commune'=>-1,
				);
			}
			$this->view->search= $data;
			$this->view-> rsads = $db->getAllAds($data);
			
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
  public function addAction(){
  	try{
  		$db = new MenuManager_Model_DbTable_DbMenuManager();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addMainMenu($_data);
  			if(!empty($_data['save_close'])){
  				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/menu-manager/index");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new MenuManager_Form_FrmMenu();
  		$frm_manager=$frm->FrmMenuManager();
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  
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

