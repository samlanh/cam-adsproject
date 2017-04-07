<?php
/*-- old form when testing dynamic form by category---*/
class listadsController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	//$this->_helper->layout()->disableLayout();
    	$id = $this->getRequest()->getParam("cateid");
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$this->view->rsads = $db->getAllAdsByName($id);
    	$this->view->cate_info = $db->getCategoryInfo($id);
    	
    }
    function detailAction(){
//     	$this->_helper->layout()->disableLayout();
    	$ads_alise = $this->getRequest()->getParam("ads");
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$adsdetail = $db->getAdsDetail($ads_alise);
    	$this->view->adsDetail = $adsdetail;
    	
    	$this->view->relate_pro = $db->getRelatedAds($adsdetail);
    }
}





