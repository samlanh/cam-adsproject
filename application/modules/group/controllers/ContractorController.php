<?php
class Group_contractorController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Group_Model_DbTable_DbContractor();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				if (!empty($formdata['show_all'])){
					$showall=$formdata['show_all'];
				}else{$showall='';
				}
				$search = array(
						'adv_search' => $formdata['adv_search'],
// 						'province_id'=>$formdata['province'],
// 						'comm_id'=>$formdata['commune'],
// 						'district_id'=>$formdata['district'],
// 						'village'=>$formdata['village'],
						'status'=>$formdata['status'],
						'start_date'=> $formdata['start_date'],
						'end_date'=>$formdata['end_date'],
						'show_all'=>$showall,
						);
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
// 						'province_id'=>0,
// 						'district_id'=>'',
// 						'comm_id'=>'',
// 						'village'=>'',
						'show_all'=>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'));
			}
			
			$rs_rows= $db->getAllContractor($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("NAME_KH","SEX","NATIONALITY","AGE","NATIONAL_ID","PHONE","CURRENT_ADDRESS",
					"DATE","STATUS");
			$link=array(
					'module'=>'group','controller'=>'contractor','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(3, $collumns, $rs_rows,array('namekh'=>$link,'sex'=>$link,'nationality'=>$link,'age'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
		//$db= new Application_Model_DbTable_DbGlobal();
// 		$this->view->district = $db->getAllDistricts();
// 		$this->view->commune_name = $db->getCommune();
// 		$this->view->village_name = $db->getVillage();
		
		$this->view->result=$search;	
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Group_Model_DbTable_DbContractor();
				try{
				 if(isset($data['save_new'])){
					$id= $db->addContractor($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				else if (isset($data['save_close'])){
					$id= $db->addContractor($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
					Application_Form_FrmMessage::redirectUrl("/group/contractor");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$fm = new Group_Form_FrmContractor();
		
		$frm = $fm->FrmAddContractor();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
// 		$dbpop = new Application_Form_FrmPopupGlobal();
// 		$this->view->frm_popup_village = $dbpop->frmPopupVillage();
// 		$this->view->frm_popup_comm = $dbpop->frmPopupCommune();
// 		$this->view->frm_popup_district = $dbpop->frmPopupDistrict();
// 		$this->view->frm_popup_clienttype = $dbpop->frmPopupclienttype();
		
	}
	public function editAction(){
		$db = new Group_Model_DbTable_DbContractor();
		$id = $this->getRequest()->getParam("id");
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$data['id'] = $id;
				$db->addContractor($data);
				Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS',"/group/contractor");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam("id");
		$row = $db->getContractorById($id);
	        $this->view->row=$row;
	        
		if(empty($row)){
			$this->_redirect("/group/contractor");
		}
		$fm = new Group_Form_FrmContractor();
		$frm = $fm->FrmAddContractor($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
// 		$dbpop = new Application_Form_FrmPopupGlobal();
// 		$this->view->frm_popup_village = $dbpop->frmPopupVillage();
// 		$this->view->frm_popup_comm = $dbpop->frmPopupCommune();
// 		$this->view->frm_popup_district = $dbpop->frmPopupDistrict();
// 		$this->view->frm_popup_clienttype = $dbpop->frmPopupclienttype();
	}
	function deleteAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Group_Model_DbTable_DbContractor();
		if (!empty($id)){
			try{
				$db->deleteContractor($id);
				Application_Form_FrmMessage::Sucessfull('DELETE_SUCCESS',"/group/contractor");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("DELETE_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}else{
			$this->_redirect("/group/contractor");
		}
	}
	function insertDistrictAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_district = new Other_Model_DbTable_DbDistrict();
			$district=$db_district->addDistrictByAjax($data);
			print_r(Zend_Json::encode($district));
			exit();
		}
	}
	function insertcommuneAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_commune = new Other_Model_DbTable_DbCommune();
			$commune=$db_commune->addCommunebyAJAX($data);
			print_r(Zend_Json::encode($commune));
			exit();
		}
	}
	function addVillageAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_village = new Other_Model_DbTable_DbVillage();
			$village=$db_village->addVillage($data);
			print_r(Zend_Json::encode($village));
			exit();
		}
	}
}

