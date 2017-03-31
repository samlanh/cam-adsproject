<?php
/*-- old form when testing dynamic form by category---*/
class IndexController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    	
    }

    public function indexAction()
    {
    	//$this->_helper->layout()->disableLayout();
    }
    public function administratorAction()
    {
    	// action body
    	 
    	/* set this to login page to change the character charset of browsers to Utf-8  ...*/
    
    	$this->_helper->layout()->disableLayout();
    	$form=new Application_Form_FrmLogin();
    	$form->setAction('index');
    	$form->setMethod('post');
    	$form->setAttrib('accept-charset', 'utf-8');
    	$this->view->form=$form;
    	$key = new Application_Model_DbTable_DbKeycode();
    	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
    
    
    	if($this->getRequest()->isPost())
    	{
    			
    		$formdata=$this->getRequest()->getPost();
    			
    		if($form->isValid($formdata))
    		{
    			$session_lang=new Zend_Session_Namespace('lang');
    			$session_lang->lang_id=$formdata["lang"];//for creat session
    			Application_Form_FrmLanguages::getCurrentlanguage($session_lang->lang_id);//for choose lang for when login
    			$user_name=$form->getValue('txt_user_name');
    			$password=$form->getValue('txt_password');
    			$db_user=new Application_Model_DbTable_DbUsers();
    			if($db_user->userAuthenticate($user_name,$password)){
    				// 					$this->view->msg = 'Authentication Sucessful!';
    				// 					$this->view->err="0";
    					
    				$session_user=new Zend_Session_Namespace('auth');
    				$user_id=$db_user->getUserID($user_name);
    				$user_info = $db_user->getUserInfo($user_id);
    					
    				$arr_acl=$db_user->getArrAcl($user_info['user_type']);
    				$session_user->url_report=$db_user->getArrAclReport($user_info['user_type']);
    				$session_user->user_id=$user_id;
    				$session_user->user_name=$user_name;
    				$session_user->pwd=$password;
    				$session_user->level= $user_info['user_type'];
    				$session_user->last_name= $user_info['last_name'];
    				$session_user->first_name= $user_info['first_name'];
    				$session_user->theme_style=$db_user->getThemeByUserId($user_id);
    					
    				$a_i = 0;
    				$arr_actin = array();
    				for($i=0; $i<count($arr_acl);$i++){
    					$arr_module[$i]=$arr_acl[$i]['module'];
    				}
    
    				$arr_module=(array_unique($arr_module));
    				$arr_actin=(array_unique($arr_actin));
    				$arr_module=$this->sortMenu($arr_module);
    					
    				$session_user->arr_acl = $arr_acl;
    				$session_user->arr_module = $arr_module;
    				$session_user->arr_actin = $arr_actin;
    
    				$session_user->lock();
    					
    				$log=new Application_Model_DbTable_DbUserLog();
    				$log->insertLogin($user_id);
    				// 					foreach ($arr_module AS $i => $d){
    				// 						if($d !== 'user'){
    				// 							$url = '/' . @$arr_module[2];
    				// 						}
    				// 						else{
    				// 							$url = self::REDIRECT_URL;
    				// 							break;
    				// 						}
    				// 					}
    				foreach ($arr_module AS $i => $d){
    					if($d !== 'transfer'){
    						$url = '/' . $arr_module[0];
    					}
    					else{
    						$url = self::REDIRECT_URL;
    						break;
    					}
    				}
    					
    				Application_Form_FrmMessage::redirectUrl("/home");
    				exit();
    			}
    			else{
    				$this->view->msg = 'ឈ្មោះ​អ្នក​ប្រើ​ប្រាស់ និង ពាក្យ​​សំងាត់ មិន​ត្រឺម​ត្រូវ​ទេ ';
    			}
    				
    		}
    		else{
    			$this->view->msg = 'លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់ទេ!';
    		}
    	}
    }
    protected function sortMenu($menus){
    	$menus_order = Array ( 'home','other','group','menu-manager','field-manager','report','rsvacl','setting');
    	$temp_menu = Array();
    	$menus=array_unique($menus);
    	foreach ($menus_order as $i => $val){
    		foreach ($menus as $k => $v){
    			if($val == $v){
    				$temp_menu[] = $val;
    				unset($menus[$k]);
    				break;
    			}
    		}
    	}
    	return $temp_menu;    	
    }

    public function logoutAction()
    {
        // action body
        $this->_redirect("/index/administrator");
        if($this->getRequest()->getParam('value')==1){        	
        	$aut=Zend_Auth::getInstance();
        	$aut->clearIdentity();        	
        	$session_user=new Zend_Session_Namespace('auth');
        	
        	$log=new Application_Model_DbTable_DbUserLog();
			$log->insertLogout($session_user->user_id);
			
        	$session_user->unsetAll();       	
	           	         	 
        	Application_Form_FrmMessage::redirectUrl("/");
        	exit();
        } 
    }

    public function changepasswordAction()
    {
        // action body
        if ($this->getRequest()->isPost()){ 
			$session_user=new Zend_Session_Namespace('auth');    		
    		$pass_data=$this->getRequest()->getPost();
    		if ($pass_data['password'] == $session_user->pwd){
    			    			 
				$db_user = new Application_Model_DbTable_DbUsers();				
				try {
					$db_user->changePassword($pass_data['new_password'], $session_user->user_id);
					$session_user->unlock();	
					$session_user->pwd=$pass_data['new_password'];
					$session_user->lock();
					Application_Form_FrmMessage::Sucessfull('ការផ្លាស់ប្តូរដោយជោគជ័យ', self::REDIRECT_URL);
				} catch (Exception $e) {
					Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
				}				
    		}
    		else{
    			Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
    		}
        }   
    }

    public function errorAction()
    {
        // action body
        
    }
    public function  demoAction(){
    	$this->_helper->layout()->disableLayout();
    	
    	
    	$frm = new Loan_Form_Frmdemo();
    	$frm_loan=$frm->FrmAddLoan();
    	Application_Model_Decorator::removeAllDecorator($frm_loan);
    	$this->view->frm_loan = $frm_loan;
    	
    	$frmpopup = new Application_Form_FrmPopupGlobal();
    	$db = new Application_Model_DbTable_DbGlobal();
    	$this->view->client_doc_type = $db->getclientdtype();
    }
    public static function start(){
    	return ($this->getRequest()->getParam('limit_satrt',0));
    }
    function changelangeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_lang=new Zend_Session_Namespace('lang');
    		$session_lang->lang_id=$data['lange'];
    		Application_Form_FrmLanguages::getCurrentlanguage($data['lange']);
    		print_r(Zend_Json::encode(2));
    		exit();
    	}
    }
    public function testAction(){
    	$db = new Application_Model_DbTable_DbFormPostAds();
    	$this->view->category = $db->getCategory();
    }
    public function getformAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbFormPostAds();
    		$cus = $db->getAllControlByCateid($data['category']);
    		print_r(Zend_Json::encode($cus));
    		exit();
    	}
    }
    function getCascadeAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbFormPostAds();
    		$province = $db->getCascadeOption($data['parent']);
    		print_r(Zend_Json::encode($province));
    		exit();
    	}
    }
    public function postAdsAction(){
    	$db = new Application_Model_DbTable_DbFormPostAds();
    	$this->view->category = $db->getCategory();
    }
    function addWatermarkAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbFormPostAds();
    		$db->addWaterMark1($data);
    		$this->_redirect("index/post-ads");
    	}
    }
    function logoutsAction(){
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	$client_session->unsetAll();
    }
    function loginAction(){
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	if(empty($client_session->client_id)){ //check session has been have or not
	    	if($this->getRequest()->isPost())
	    	{
	    		$data=$this->getRequest()->getPost();
	    		$db = new Application_Model_DbTable_DbClient();
	    		if($db->cusAuthenticate($data['user_name'],$data['password'])){
	    			$cus_infor = $db->getClientInfo($data);
	    			$client_session=new Zend_Session_Namespace('client');
	    			$client_session->client_id = $cus_infor['id'];
	    			$client_session->client_email = $cus_infor['email'];
	    			$client_session->client_name = $cus_infor['user_name'];
	    			$client_session->is_verify = $cus_infor['is_verify_acc'];
	    			
	    			$this->_redirect("dashboard/");
	    		}else{
	    			$this->view->message = 'Invalid user name and password. Please check and try again.';
	    		}
	    	}
    	}else{
    		$this->_redirect("dashboard/");
    	}
    
    }
    function registerAction(){
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	if(empty($client_session->client_id)){ //check session has been have or not
	    	if($this->getRequest()->isPost()){
	    		$data=$this->getRequest()->getPost();
	    		$db = new Application_Model_DbTable_DbClient();
	    		$data['code_random']= rand(1000,9999); // generate security code 4 charecter
	    		if (!empty($data)){
	    			$db = new Application_Model_DbTable_DbClient();
	    			$check = $db->checkEmailClient($data['email']); // check email has been use or not befor submit
	    			if ($check==0){
		    			$client = $db->clientRegister($data);
		    			
		    			$client_session->client_id = $client; // set value to session
		    			$client_session->client_email = $data['email'];
		    			$client_session->client_name = $data['user_name'];
		    			$client_session->is_verify = 0;
		    			
		    			$db_sentmail = new Application_Model_DbTable_DbSentMail();
		    			$db_sentmail->sentCodeVerifyAcc($data);
		    			$this->_redirect("index/verify-code");
	    			}else{
	    				$this->view->message = "Email has been used. please use other email";
	    			}
	    		}
	    	}
    	}else{
    		$this->_redirect("dashboard/");
    	}
    }
    function verifyCodeAction(){
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	if(!empty($client_session->client_id)){ //check session has been have or not
    		if($client_session->is_verify==1){ //check account have verify ready or not
    			$this->_redirect("dashboard/");
    		}
    		if($this->getRequest()->isPost()){
    			$data=$this->getRequest()->getPost();
    			
    			$data['id']=$client_session->client_id;
    			$db = new Application_Model_DbTable_DbClient();
    			$verify = $db->checkVerifyCode($data);
    			
    			if ($verify==1){
    				$db->activationClient($data['id']); //update to activate account
    				$client_session->is_verify = 1;
    				$this->_redirect("dashboard/");
    			}else{
    				$this->view->message = "Invalid security code. please check again.";
    			}
    		}
    	}else{
    		$this->_redirect("index/login");
    	}
    }
    function checkmailAction(){ //ajax check email has been use or not
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbClient();
    		$rs = $db->checkEmailClient($data['email']);
    		print_r(Zend_Json::encode($rs));
    		exit();
    	}
    }
}





