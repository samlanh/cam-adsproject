<?php 
Class Group_Form_FrmContractor extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddContractor($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_namekh = new Zend_Dojo_Form_Element_TextBox('name_kh');
		$_namekh->setAttribs(array(
						'dojoType'=>'dijit.form.ValidationTextBox',
						'class'=>'fullside',
						'required' =>'true'
		));
		
		$_join_nation_id = new Zend_Dojo_Form_Element_TextBox('join_nation_id');
		$_join_nation_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_nationality = new Zend_Dojo_Form_Element_TextBox('nationality');
		$_nationality->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_nationality->setValue("ខ្មែរ");
		
		$p_nationality = new Zend_Dojo_Form_Element_TextBox('p_nationality');
		$p_nationality->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$p_nationality->setValue("ខ្មែរ");
		
		$_sex = new Zend_Dojo_Form_Element_FilteringSelect('sex');
		$_sex->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt_status = $db->getVewOptoinTypeByType(11,1);
		unset($opt_status[-1]);
		unset($opt_status['']);
		$_sex->setMultiOptions($opt_status);
		
		
		$_province = new Zend_Dojo_Form_Element_FilteringSelect('province');
		$_province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'filterDistrict();',
		));
		
		$rows =  $db->getAllProvince();
		$options=array($this->tr->translate("SELECT_PROVINCE")); //array(''=>"------Select Province------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['province_id']]=$row['province_en_name'];
		$_province->setMultiOptions($options);
		
		$_house = new Zend_Dojo_Form_Element_TextBox('house');
		$_house->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		$_street = new Zend_Dojo_Form_Element_TextBox('street');
		$_street->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$national_id=new Zend_Dojo_Form_Element_TextBox('national_id');
		$national_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				));
		$_id = new Zend_Form_Element_Hidden("id");
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		
		$_hnamekh = new Zend_Dojo_Form_Element_TextBox('hname_kh');
		$_hnamekh->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				//'required' =>'true'
		));
		
		
		$_ksex = new Zend_Dojo_Form_Element_FilteringSelect('ksex');
		$_ksex->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt_kstatus= $db->getVewOptoinTypeByType(11,1);
		unset($opt_kstatus['-1']);
		unset($opt_kstatus['']);
		$_ksex->setMultiOptions($opt_kstatus);
		
		$_pnameen = new Zend_Dojo_Form_Element_ValidationTextBox('pname_en');
		$_pnameen->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		
		$_lphone = new Zend_Dojo_Form_Element_TextBox('lphone');
		$_lphone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_istatus=  new Zend_Dojo_Form_Element_FilteringSelect('istatus');
		$_istatus->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_istatus_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_istatus->setMultiOptions($_status_opt);
		
		$_wife_nation_id = new Zend_Dojo_Form_Element_TextBox('wife_nation_id');
		$_wife_nation_id->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				//'required' =>'true'
		));
		
		$_age = new Zend_Dojo_Form_Element_TextBox('age');
		$_age->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$p_age = new Zend_Dojo_Form_Element_TextBox('p_age');
		$p_age->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$group = new Zend_Dojo_Form_Element_TextBox('group');
		$group->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$current_address = new Zend_Dojo_Form_Element_TextBox('current_address');
		$current_address->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'width:100%;min-height:60px;'));
		if($data!=null){
			$_namekh->setValue($data['namekh']);
			$_sex->setValue($data['sex']);
			$_province->setValue($data['province']);
			$_house->setValue($data['house_no']);
			$_street->setValue($data['street']);
			$_phone->setValue($data['phone']);
			$_status->setValue($data['status']);
			$_id->setValue($data['id']);
			$_nationality->setValue($data['nationality']);
			$national_id->setValue($data['nation_id']);
			$_hnamekh->setValue($data['wife_namekh']);
			$_lphone->setValue($data['wife_phone']);
			$_wife_nation_id->setValue($data['wife_nation_id']);
			$_ksex->setValue($data['wife_sex']);
			$p_nationality->setValue($data['wife_nationlity']);
			$_age->setValue($data['age']);
			$p_age->setValue($data['wife_age']);
			$group->setValue($data['group']);
			$current_address->setValue($data['current_address']);
		}
		$this->addElements(array($p_nationality,$_nationality,$_wife_nation_id,$_istatus,$_lphone,$_pnameen,$_ksex,$_hnamekh,$_join_nation_id,
				$_id,$national_id,$_namekh,$_sex,$current_address,
				$_province,$_house,$_street,$group,
				$_phone,$_status,$_age,$p_age));
		return $this;
		
	}	
}