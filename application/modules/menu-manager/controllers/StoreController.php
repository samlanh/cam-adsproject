<?php
class MenuManager_StoreController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	private $sex=array(1=>'M',2=>'F');
	public function indexAction(){
			$db = new MenuManager_Model_DbTable_Dbstore();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'branch_id'=>-1,
						'adv_search' => '',
						'status' => -1,
						'show_all'=>'',
						'start_date'=> date('Y-m-d'),
						'customer_id'=>-1,
						'end_date'=>date('Y-m-d'));
			}
			
			$rs_rows= $db->getAllStore($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Store Name","Customer Name","Socail","Email","Phone","Date","Status");
			$link1 = array('module'=>'menu-manager','controller'=>'store','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('store_title'=>$link1,'store_phone'=>$link1,'social_media'=>$link1,'name_kh'=>$link1,'name_en'=>$link1));
	
// 		$frm = new Application_Form_FrmAdvanceSearch();
// 		$frm = $frm->AdvanceSearch();
// 		Application_Model_Decorator::removeAllDecorator($frm);
// 		$this->view->frm_search = $frm;
		
// 		$fm = new Group_Form_FrmClient();
// 		$frm = $fm->FrmAddClient();
// 		Application_Model_Decorator::removeAllDecorator($frm);
// 		$this->view->frm_client = $frm;
		
  }
  
  public function addAction(){
  	if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new MenuManager_Model_DbTable_Dbstore();
  		$db->addNewstore($data);
  		if(!empty($data['save_close'])){
  			Application_Form_FrmMessage::Sucessfull("INSERT_SUCESS","/menu-manager/store");
  		}else{
  			Application_Form_FrmMessage::Sucessfull("INSERT_SUCESS","/menu-manager/store/add");
  		}
  	}
  	$db = new Application_Model_DbTable_DbGlobalselect();
  	
  	$this->view->rsclient = $db->getAllClient();
  	$this->view->rstemp = $db->getAlltemplate();
  	$this->view->rsindustry = $db->getIndustryType();
  }
  public function editAction(){
//   	$id = $this->getRequest()->getParam('id');
//   	try{
//   		$db = new MenuManager_Model_DbTable_DbMenuManager();
//   		if($this->getRequest()->isPost()){
//   			$_data = $this->getRequest()->getPost();
//   			$_data['id']=$id;
//   			$db->addMainMenu($_data);
//   			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/menu-manager/index");
//   		}
//   		$row = $db->getMainMenuById($id);
//   		$frm = new MenuManager_Form_FrmMenu();
//   		$frm_manager=$frm->FrmMenuManager($row);
//   		Application_Model_Decorator::removeAllDecorator($frm_manager);
//   		$this->view->frm = $frm_manager;
//   	}catch (Exception $e){
//   		Application_Form_FrmMessage::message("Application Error");
//   		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
//   	}
  }
  public function deleteAction(){
//   	try{
//   		$id = $this->getRequest()->getParam('id');
//   		$db = new MenuManager_Model_DbTable_DbMenuManager();
//   		if(!empty($id)){
//   			$db->deleteMenu($id);
//   			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/menu-manager/index");
//   		}
//   	}catch (Exception $e){
//   		Application_Form_FrmMessage::message("Application Error");
//   		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
//   	}
  }
  
  
}

