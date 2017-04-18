<?php
/*-- old form when testing dynamic form by category---*/
class listadsController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	//$this->_helper->layout()->disableLayout();
    	$id = $this->getRequest()->getParam("cateid");
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	if (!empty($id)){
	    	$this->view->rsads = $db->getAllAdsByName($id);
	    	$this->view->cate_info = $db->getCategoryInfo($id);
    	}else{
    		$this->view->rsads = $db->getAllAds();
    	}
    	
    	$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
    	$category_id = $db->categoryIdByName($id);
    	$this->view->form = $dbform->getAllFormSearchByCateid($category_id,1);
    	
    	$this->view-> rslocation = $db->getAllLocation();
    	
    	$this->view->rsbannerleft = $db->getBannerByPosition(1);//1 = left
    	$this->view->rsbannerright = $db->getBannerByPosition(2);//2 = right
    	
    	
    }
    function detailAction(){
//     	$this->_helper->layout()->disableLayout();
    	$ads_alise = $this->getRequest()->getParam("ads");
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$adsdetail = $db->getAdsDetail($ads_alise);
    	$this->view->adsDetail = $adsdetail;
    	$this->view->rsattr = $db->getAdsDetailById($adsdetail['id']);
    	$this->view->relate_pro = $db->getRelatedAds($adsdetail);
    	$db->addCountView($ads_alise);
    }
    function resultAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    				'keywork_search'=>'',
    				'category_id'=>-1,
    				'province_id'=>-1,
    				);
    	}
    	$this->view-> search= $data;
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$this->view->rsads = $db->getSearchHomePage($data);
    }
    function advresultAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$data['keywork_search']='';
    	}else{
    		$data = array(
    				'keywork_search'=>'',
    				'category_id'=>-1,
    				'province_id'=>-1,
    		);
    	}
    	$this->view-> search= $data;
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$this->view->rsads = $db->getAdvanceSearch($data);
    }
    function advancesearchAction(){
     	$this->_helper->layout()->disableLayout();
     	if($this->getRequest()->isPost()){
     		$data = $this->getRequest()->getPost();
     	}else{
     		$data = array(
     				'keywork_search'=>'',
     				'category_search'=>-1,
     				'province_id'=>-1,
     				'district'=>-1,
     				'commune'=>-1,
     		);
     	}
     	$this->view-> search= $data;
     	print_r($data);
     	$db = new Application_Model_DbTable_DbGlobalselect();
     	$this->view->rsads = $db->getAllAdvanceSearch($data);
    }
}





