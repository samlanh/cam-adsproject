<?php
/*-- old form when testing dynamic form by category---*/
class ListadsController extends Zend_Controller_Action
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
    				'category_search'=>-1,
    				'location_search'=>-1,
    				);
    	}
    	
    	$sess_search=new Zend_Session_Namespace('homesearch');
    	$sess_search->keywork_search=empty($data['keywork_search'])?'':$data['keywork_search'];
    	$sess_search->category_search=empty($data['category_search'])?'':$data['category_search'];
    	$sess_search->location_search=empty($data['location_search'])?'':$data['location_search'];
    		
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
     				'location_search'=>-1,
     				'category_search'=>-1,
     				'province_id'=>-1,
     				'district'=>-1,
     				'commune'=>-1,
     		);
     	}
     	$this->view-> search= $data;
     	$db = new Application_Model_DbTable_DbGlobalselect();
     	$this->view->rsads = $db->getAllAdvanceSearch($data);
     	
     	$db = new Application_Model_DbTable_DbVdGlobal();
     	$param = $this->getRequest()->getParam('category');
     	$cate = $db->getCategoryIdbyAlias($param);
     	
//      	$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
//      	$this->view->form = $dbform->getAllFormSearchByCateid($cate['id'],1);
    }
    function searchajaxAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbGlobalselect();
    		$data['district'] = empty($data['district'])?-1:$data['district'];
    		$data['commune'] = empty($data['commune'])?-1:$data['commune'];
    		$data['location_search'] = empty($data['location_search'])?-1:$data['location_search'];
    		$data['category_search'] = empty($data['category_search'])?-1:$data['category_search'];
    		$rs = $db->getAllAdvanceSearch($data,1);
    		print_r(($rs));
    		exit();
    	}
    }
    function getcontrollerAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
    		$rs = $dbform->getAllFormSearchByCateid($data['category_id'],1,3);
    		print_r(($rs));
    		exit();
    	}	
    }
}





