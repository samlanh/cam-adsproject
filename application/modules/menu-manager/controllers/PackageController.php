<?php
class MenuManager_PackageController extends Zend_Controller_Action {
	protected $tr;
	const REDIRECT_URL ='/menu-manager';
	public function init()
	{
		/* Initialize action controller here */
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new MenuManager_Model_DbTable_Dbpackage();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'search_status' => -1,
						'start_date'=> date('Y-m-01'),
						'end_date'=>date('Y-m-d'));
			}
			$rs_rows= $db->getAllPackage($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("Package Name","Allow Post","Allow Store","Allow Show","Price","Is Free","Description","Status");
			$link=array(
					'module'=>'menu-manager','controller'=>'package','action'=>'edit');
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('package_name'=>$link,'allow_post'=>$link,'allow_store'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Other_Form_FrmHoliday();
		$frm = $frm->FrmAddHoliday();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_holiday = $frm;
	}
	function addAction()
	{
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$db = new MenuManager_Model_DbTable_Dbpackage();
				$_major_id = $db->addNewPackage($_data);
				if(!empty($_data['save_new'])){
					Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
				}else{
					Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL . '/package/index');
				}
			} catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new MenuManager_Form_FrmPackage();
		$frm_holiday=$frm->FrmAddpackage(null);
		Application_Model_Decorator::removeAllDecorator($frm_holiday);
		$this->view->frm_package = $frm_holiday;
	}
	function editAction()
	{
		$db = new MenuManager_Model_DbTable_Dbpackage();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_major_id = $db->addNewPackage($_data);
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("EDIT_SUCCESS"),self::REDIRECT_URL.'/package/index');
			} catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$id = $this->getRequest()->getParam('id');
		$row = $db->getPackageById($id);
		if(empty($row)){
			$this->_redirect('/other/holiday');
		}
		$frm = new MenuManager_Form_FrmPackage();
		$frm_holiday=$frm->FrmAddpackage($row);
		Application_Model_Decorator::removeAllDecorator($frm_holiday);
		$this->view->frm_package = $frm_holiday;
	}
}
