<?php

class PostsadsController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    	
    }

    public function chooseCategoryAction(){ // choose category before go to write post ads
    	//$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	if(!empty($client_session->client_id)){ //check session has been have or not
	    	$db = new Application_Model_DbTable_DbVdGlobal();
	    	$this->view->parentcate = $db->getCategoryParent();
    	}else{
    		$this->_redirect("index/login");
    	}
    }
    public function writePostAction(){ // write post ads and submit ads to finish
    	//$this->_helper->layout()->disableLayout();
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$dbp = new Application_Model_DbTable_DbPostAds();
    		$dbp->addPostsAds($data);
    		$this->_redirect("/postsads/choose-category");
    	}
    	$client_session=new Zend_Session_Namespace('client');
    	if(!empty($client_session->client_id)){ //check session has been have or not
	    	$param = $this->getRequest()->getParam('category');
	    	$db = new Application_Model_DbTable_DbVdGlobal();
	    	
	    	$cate = $db->getCategoryIdbyAlias($param);
	    	$this->view->cate = $cate;
	    	
	    	$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
	    	$this->view->form = $dbform->getAllFormTypeCateid($cate['id']);
	    	
	    	$db = new Application_Model_DbTable_DbClient();
	    	$this->view->client_info = $db->getClientInfo(null,$client_session->client_id);
	    	
	    	$db = new Application_Model_DbTable_DbGlobalselect();
	    	$this->view-> rslocation = $db->getAllLocation();
    	}else{
    		$this->_redirect("index/login");
    	}
    }
    public function indexAction(){ 
    	try{
    		$client_session=new Zend_Session_Namespace('client');
    		if(!empty($client_session->client_id)){ 			 /* check session has been have or not */
    			
	    		if($this->getRequest()->isPost()){				 /* check data submit from method post of form */
	    			$data = $this->getRequest()->getPost();		 /* get all submit from  form */
	    			
	    			$db_global = new Application_Model_DbTable_DbVdGlobal();
	    			$cate = $db_global->getCategoryIdbyAlias($data['category']);/* get category by alias*/
	    			
	    			$data['client_id'] = $client_session->client_id; /* add client_id to data form (client_id by session) */
	    			$data['category_id'] =$cate['id'];
	    			
	    			$db = new Application_Model_DbTable_DbPostAds();
	    			$newcode = $db->getNewAdsCode();
	    			$data['ads_code'] =$newcode;
	    			$db->addPostsAds($data);
	    			
	    		}
    		}else{
    			$this->_redirect("index/login");
    		}
    	}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
    }
    function getDistrictbyidAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbVdGlobal();
    		$rs = $db->getAllDistrictByProvince($data['pro_id']);
    		print_r(Zend_Json::encode($rs));
			exit();
    	}
    }
    public function uploadAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$client_session=new Zend_Session_Namespace('client');
    		$data['id']= $client_session->client_id;
    		$db = new Application_Model_DbTable_DbPostAds();
    		$rs = $db->uploadImageFirst($data);
    		echo $rs;
    		exit();
    	}
    }
    public function adsDetailAction(){ //get ads detail for display on popup
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbPostAds();
    		$rs = $db->getAdsDetail($data['ads_code']);
    		print_r(Zend_Json::encode($rs));
    		exit();
    	}
    }
}





