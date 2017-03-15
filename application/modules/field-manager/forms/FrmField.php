<?php 
Class FieldManager_Form_FrmField extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmField($data=null){
		$db = new Application_Model_DbTable_DbVdGlobal();
		$language = $db->getLaguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$_title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'onkeypress'=>'return checkSpcialChar(event);',
				'placeholder'=>$this->tr->translate("TITLE")
		));
		$_title->setValue($request->getParam("title"));
		
		$_label = new Zend_Dojo_Form_Element_ValidationTextBox('label');
		$_label->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("LABEL")
		));
		$_label->setValue($request->getParam("label"));
		
		$field_tyoe = new Zend_Dojo_Form_Element_FilteringSelect("field_type");
		$_arr_field=array(
				""=>$this->tr->translate("CHOOSE_TYPE_FIELD"),
				"select"=>$this->tr->translate("Select"),
				"text"=>$this->tr->translate("TextBox"),
				"emailaddress"=>$this->tr->translate("Email Address"),
				"date"=>$this->tr->translate("Date"),
				"number"=>$this->tr->translate("Number"),
				"textarea"=>$this->tr->translate("Text Area"),
				"image"=>$this->tr->translate("Image"),
				"cascade"=>$this->tr->translate("Cascade"),
		);
		$field_tyoe->setMultiOptions($_arr_field);
		$field_tyoe->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'onChange'=>'checkFieldType()',
				'class'=>'fullside'));
		$field_tyoe->setValue($request->getParam('field_type'));
				
		$_status_search = new Zend_Dojo_Form_Element_FilteringSelect("status_search");
		$_arrsearch=array(
				""=>$this->tr->translate("SELECT_STATUS"),
				"1"=>$this->tr->translate("ACTIVE"),
				"0"=>$this->tr->translate("DACTIVE"),
		);
		$_status_search->setMultiOptions($_arrsearch);
		$_status_search->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				));
		$_status_search->setValue($request->getParam('status_search'));
		
		$id = new Zend_Form_Element_Hidden("id");

		$note = new Zend_Dojo_Form_Element_TextBox('note');
		$note->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'width:100%;min-height:60px;'));
		$note->setValue($request->getParam('note'));
		
		$_status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$_arr=array(
				"1"=>$this->tr->translate("ACTIVE"),
				"0"=>$this->tr->translate("DACTIVE"),
		);
		$_status->setMultiOptions($_arr);
		$_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$_status->setValue($request->getParam('status'));
		
		$category = new Zend_Form_Element_Multiselect("category");
		$category->setAttribs(array(
				'required'=>'true',
				'class'=>'fullside',
				'style'=>"min-height: 300px;max-height: 700px; padding: 5px;"));
 		$option = array();
// 		$option = array("0"=>$this->tr->translate("ALL"));
		$result = $db->getCategory();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		$category->setMultiOptions($option);
		//$category->setValue($request->getParam('parent'));
		
		$_field_parent = new Zend_Dojo_Form_Element_FilteringSelect("field_parent");
		$_field_parent->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside'));
		$option = array("0"=>$this->tr->translate("CHOOSE_PARENT"));
		$result = $db->getFieldTypeSelect();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		$_field_parent->setMultiOptions($option);
		
		$advance_search = new Zend_Dojo_Form_Element_TextBox('advance_search');
		$advance_search->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("SEARCH")
		));
		$advance_search->setValue($request->getParam("advance_search"));
		if($data!=null){
// 			$category->setValue($data['parent']);
			$field_tyoe->setValue($data['type']);
			$_status->setValue($data['status']);
			$id->setValue($data['id']);
			$_field_parent->setValue($data['field_parent']);
			$note->setValue($data['description']);
			$_title->setValue($data['title']);
			$_label->setValue($data['label_name']);
		}
		
		$this->addElements(array($id,$field_tyoe,$_title,$_status,$note,$_status_search,$_label,
				$category,$_field_parent,$advance_search
				));
		return $this;
	}	
}