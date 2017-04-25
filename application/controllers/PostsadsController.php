<?php

class PostsadsController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    	
    }
    function errorpageAction(){
    	
    }
    public function chooseCategoryAction(){ // choose category before go to write post ads
    	$this->_helper->layout()->disableLayout();
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
    public function writePostAction(){ // write post ads and submit ads to finish
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
	    	$this->view->rsstore = $db->getAllStoreByUser();
    	}else{
    		$this->_redirect("index/login");
    	}
    }
    public function updatePostAction(){ // write post ads and submit ads to finish
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$dbp = new Application_Model_DbTable_DbPostAds();
    		
//     		print_r($data);exit();
    	    $result = $dbp->addUpdateAds($data);
    		
    		$session_post=new Zend_Session_Namespace('postads');
    		$session_post->success=$result;
    		Application_Form_FrmMessage::Sucessfull("Your ad has been insert success", "/dashboard/myads");
    	}
    	$client_session=new Zend_Session_Namespace('client');
    	if(!empty($client_session->client_id)){ //check session has been have or not
    		$paramid = $this->getRequest()->getParam('adsid');
    		$db = new Application_Model_DbTable_DbVdGlobal();
    		
    		$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
    		$rs_ads = $dbform->getAdsById($paramid);
    		
    		if(empty($rs_ads)){
    			$this->_redirect("postsads/errorpage");
    		}
    		$this->view->rs_ads = $rs_ads;//result ads row
    		
    		$this->view->form = $dbform->getAllFormTypeCateid($rs_ads['category_id'],null,$rs_ads['id']);//get form controll
    		
    		$cate = $db->getCategoryIdbyAlias($rs_ads['cate_alias']);
    		$this->view->cate = $cate;//get parent category for selected 
    
    		$db = new Application_Model_DbTable_DbClient();
    		$this->view->client_info = $db->getClientInfo(null,$client_session->client_id);
    
    		$db = new Application_Model_DbTable_DbGlobalselect();
    		$this->view-> rslocation = $db->getAllLocation();
    		
    		$this->view->rsstore = $db->getAllStoreByUser();
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
    function getCommunebyidAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbVdGlobal();
    		$rs = $db->getAllCommunebyDistict($data['distict_id']);
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





