<?php
class Other_ParamaterController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	protected $tr;
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbParamater();
// 			if($this->getRequest()->isPost()){
// 				$search=$this->getRequest()->getPost();
// 			}
// 			else{
// 				$search = array(
// 						'adv_search' => '',
// 						'search_status' => -1);
// 			}
			$rs_rows= $db->getAllParamater();
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("PARAMETER_NAME","STATUS");
			$link=array(
					'module'=>'other','controller'=>'paramater','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link,'status'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
	}
    function addAction(){
		   	if($this->getRequest()->isPost()){
		   		try{
		   			$data = $this->getRequest()->getPost();
		   			$db = new Other_Model_DbTable_DbParamater();
		   			if(isset($data['save_new'])){
		   				$db->addParamater($data);
		   				Application_Form_FrmMessage::message($this->tr->translate('INSERT_SUCCESS'));
		   			}else if(isset($data['save_close'])){
		   				$db->addParamater($data);
		   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/paramater/index');
		   			}
		   		}catch(Exception $e){
		   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
		   			$err =$e->getMessage();
		   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
		   		}
		   	}
   }
   function editAction(){
		   	if($this->getRequest()->isPost()){
		   		try{
		   			$data = $this->getRequest()->getPost();
		   			$db = new Other_Model_DbTable_DbParamater();
		   			if(isset($data['save_close'])){
		   				$db->updateParamater($data);
		   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/paramater/index');
		   			}
		   		}catch(Exception $e){
		   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
		   			$err =$e->getMessage();
		   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
		   		}
		   	}
            $id=$this->getRequest()->getParam('id');
            $db_paramater=new Other_Model_DbTable_DbParamater();
            $row_p=$db_paramater->getParamaterById($id);
           // print_r($row_p);exit();
            $this->view->row_paramater=$row_p;
   }
   public function addNewzoneAction(){
   	if($this->getRequest()->isPost()){
   		$data = $this->getRequest()->getPost();
   		$data['status']=1;
   		$db_co = new Other_Model_DbTable_DbZone();
   		$id = $db_co->addZone($data);
   		print_r(Zend_Json::encode($id));
   		exit();
   	}
   }
}

