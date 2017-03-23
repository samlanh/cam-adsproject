/*-----------------------------------------------------------------------------------*/
/*	Custom Script
/*-----------------------------------------------------------------------------------*/
var baseurl = '';
function getBaseUrl(url){
		baseurl = url;
	}
function checkSpcialChar(event){
         if(((event.keyCode > 64 && event.keyCode < 91) || event.keyCode == 45 || (event.keyCode > 96 && event.keyCode < 123) || event.keyCode == 8 || (event.keyCode >= 48 && event.keyCode <= 57))){
             event.returnValue = true;
             return;
          }
          event.returnValue = false;
}
/*----validate form-------------*/
function checkForm(form)
{
	var message = document.getElementById('confirmMessage');
  re = /^\w+$/;
  if(form.password.value != "" && form.password.value == form.c_password.value) {
    if(form.password.value.length < 6) {
      message.innerHTML = "Note: Password must contain at least six characters!"
      form.password.focus();
      return false;
    }
    if(form.password.value == form.email.value) {
      message.innerHTML = "Note: Password must be different from Email!";
      form.password.focus();
      return false;
    }
	if(form.user_name.value == "") {
      message.innerHTML = "Note: User name can't be null!";
      form.user_name.focus();
      return false;
    }
	if(form.email.value == "") {
      message.innerHTML = "Note: Please enter your email address!";
      form.email.focus();
      return false;
    }
    re = /[0-9]/;
    if(!re.test(form.password.value)) {
      message.innerHTML = "Note: password must contain at least one number (0-9)!";
      form.password.focus();
      return false;
    }
    re = /[a-z]/;
    if(!re.test(form.password.value)) {
      message.innerHTML = "Note: password must contain at least one lowercase letter (a-z)!";
      form.password.focus();
      return false;
    }
  
  } else {
    message.innerHTML = "Note: Please check that you've entered and confirmed your password!";
    form.password.focus();
    return false;
  }
  //alert("You entered a valid password: " + form.password.value);
  return true;
}
//check mail
var url_email = '/index/checkmail';
function checkMail(){
	email = $('#email').val();
	
	 var message = document.getElementById('error');
	$.ajax({
        url: baseurl+url_email,
        type: "post",
        data: {'email':email},
        success: function(data){
            val = $.parseJSON(data);
				if(val==1){
					 message.innerHTML = "  Email already use !";
				}
				else{
					message.innerHTML = "";
					
				}

			},
			error: function(err) {
				alert(err);
			}
		});
}
function validateFormlogin(form){
var message = document.getElementById('error');
  re = /^\w+$/;
  if(form.password.value != "" && form.user_name.value != "") {
 
    if(form.password.value == "") {
      message.innerHTML = "Please fill your password!";
      form.password.focus();
      return false;
    }
	if(form.user_name.value == "") {
      message.innerHTML = "Please fill your user name!";
      form.user_name.focus();
      return false;
    }
  
  } else {
    message.innerHTML = "Note: Please fill your user name and password!";
    form.password.focus();
    return false;
  }
  return true;
}


var popAccInfo = document.getElementById('pop-acc-info');//form id
var btnpopcanel = document.getElementById("edit_account");//Get the button that opens the popup
var spanClose = document.getElementById("close-acc-info");//Get the <span> element that closes the popup
btnpopcanel.onclick = function() { //When the user clicks the button, open the popup
	popAccInfo.style.display = "block";
}
spanClose.onclick = function() {//When the user clicks on <span> (x), close the popup
	popAccInfo.style.display = "none";
}
window.onclick = function(event) {//When the user clicks anywhere outside of the modal, close it
    if (event.target == popAccInfo) {
    	popAccInfo.style.display = "none";
    }
	if (event.target == popaddtional) {
    	popaddtional.style.display = "none";
    }
}

var popaddtional = document.getElementById('pop-addtional');//form id
var btnEditAdditional = document.getElementById("edit_additional");//botton click popup form
var spanCloseAdd = document.getElementById("close-pop-addtional");//botton close popup form
btnEditAdditional.onclick = function() { //When the user clicks the button, open the popup
	popaddtional.style.display = "block";
}
spanCloseAdd.onclick = function() { //When the user clicks on <span> (x), close the popup
	popaddtional.style.display = "none";
}


//edit account information
var url_edit_acc = '/dashboard/edit-acc';
function editAccountInfo(){
	var myform = document.getElementById("form-account-info");
	var formData = jQuery(myform).serializeArray();
	jQuery.ajax({
        url: baseurl+url_edit_acc,
        type: "post",
        data: formData,
        success: function(data){
				document.getElementById('pop-acc-info').style.display = "none";
				document.getElementById('email_v').innerHTML = document.getElementById('email').value;
				document.getElementById('phone_v').innerHTML = document.getElementById('phone').value;
				document.getElementById('user_name_v').innerHTML = document.getElementById('user_name').value;
				
			},
			error: function(err) {
				alert(err);
			}
		});
}
//edit Addtional information
var url_edit_additional = '/dashboard/edit-addtional';
function editAdditional(){
	var myform = document.getElementById("form-pop-addtional");
	var formData = jQuery(myform).serializeArray();
	jQuery.ajax({
        url: baseurl+url_edit_additional,
        type: "post",
        data: formData,
        success: function(data){
				document.getElementById('pop-addtional').style.display = "none";
				document.getElementById('address_v').innerHTML = document.getElementById('address').value;
				document.getElementById('website_v').innerHTML = document.getElementById('website').value;
			},
			error: function(err) {
				alert(err);
			}
		});
}
//jquery upload profile image
jQuery(document).ready(function() {		
	jQuery('#profile_image').live('change', function(){ 
		jQuery("#preview").html('');
		jQuery("#preview").html('<img src="'+baseurl+'/images/loader.gif" alt="Uploading...."/>');
		jQuery("#image_upload_form").ajaxForm({
				target: '#preview'
}).submit();

	});
}); 
var loadFile = function(event) {
    var output = document.getElementById('profile_wiew');
    output.src = URL.createObjectURL(event.target.files[0]);
  };