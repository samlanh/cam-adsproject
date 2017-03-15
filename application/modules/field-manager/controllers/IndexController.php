<?php
class FieldManager_IndexController extends Zend_Controller_Action {
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
			$db = new FieldManager_Model_DbTable_DbField();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 				
 			}
			else{
				$search= array(
						'advance_search'=>'',
						'field_type'=>'',
						'status_search'=>'',
						);
			}
			$rs_rows= $db->getAllField($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("TITLE","TYPE","IS_REQUIER","STATUS");
			$link_info=array('module'=>'field-manager','controller'=>'index','action'=>'edit',);
			$this->view->list=$list->getCheckList(4, $collumns, $rs_rows,array('title'=>$link_info,'type'=>$link_info),0);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new FieldManager_Form_FrmField();
		$frm_manager=$frm->FrmField();
		Application_Model_Decorator::removeAllDecorator($frm_manager);
		$this->view->frm = $frm_manager;
  }
  public function addAction(){
  	try{
  		$db = new FieldManager_Model_DbTable_DbField();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addField($_data);
  			if(!empty($_data['save_close'])){
  				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/field-manager/index");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new FieldManager_Form_FrmField();
  		$frm_manager=$frm->FrmField();
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  }
  public function editAction(){
  	$id = $this->getRequest()->getParam('id');
  	try{
  		if (!empty($id)){
	  		$db = new FieldManager_Model_DbTable_DbField();
	  		if($this->getRequest()->isPost()){
	  			$_data = $this->getRequest()->getPost();
	  			$_data['id']=$id;
	  			$db->addField($_data);
	  			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/field-manager/index");
	  		}
	  		$row = $db->getFieldById($id);
	  		$this->view->row = $row;
	  		
	  		$this->view->fieldvalue = $db->getFieldValueByFileid($id);
	  		$this->view->id = $id;
	  		$frm = new FieldManager_Form_FrmField();
	  		$frm_manager=$frm->FrmField($row);
	  		Application_Model_Decorator::removeAllDecorator($frm_manager);
	  		$this->view->frm = $frm_manager;
  		}else{
  			$this->_redirect("/field-manager/index/");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  }
  public function deleteAction(){
  	try{
  		$id = $this->getRequest()->getParam('id');
  		$db = new FieldManager_Model_DbTable_DbField();
  		if(!empty($id)){
  			$db->deleteField($id);
  			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/field-manager/index");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
}

