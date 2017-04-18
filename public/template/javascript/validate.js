/*-----------------------------------------------------------------------------------*/
/*	Custom Script
/*-----------------------------------------------------------------------------------*/

/*----validate form-------------*/
function validateFormAds(form)
{
	var message = document.getElementById('alertMessage');
	var blogalert = document.getElementById('alert');
  if(form.title.value != ""  ) {
    if(form.title.value.length =="") {
      message.innerHTML = "Note: Title is required please fill the title !";
      /* blogalert.style.display = "block";
      form.title.focus();
      window.scroll({
    	  top: 0, 
    	  left: 0, 
    	  behavior: 'smooth'
    	}); */
      return false;
    }
    if(form.price.value.length =="") {
        message.innerHTML = "Note: Price is required please fill the price !";
       /*  blogalert.style.display = "block";
        form.price.focus();
        window.scroll({
      	  top: 0, 
      	  left: 0, 
      	  behavior: 'smooth' 
      	});*/
        return false;
      }
    if(form.description.value =="") {
      message.innerHTML = "Note: Description is required please fill the description !";
      /* blogalert.style.display = "block";
      form.description.focus();
      window.scroll({
    	  top: 0, 
    	  left: 0, 
    	  behavior: 'smooth' 
    	});*/
      return false;
    }
	 /* if(form.name.value == "") {
      message.innerHTML = "Note: Name is required please fill the name !";
     blogalert.style.display = "block";
      form.name.focus();
      window.scroll({
    	  top: 0, 
    	  left: 0, 
    	  behavior: 'smooth' 
    	});
      return false;
    }*/
//	if(form.email.value == "") {
//      message.innerHTML = "Note: Please enter your email address!";
//      form.email.focus();
//      return false;
//    }
  } else {
	  
    message.innerHTML = "Note: Please complete all field that require.";
    blogalert.style.display = "block";
   /*-- form.title.focus();
    window.scroll({
    	  top: 0, 
    	  left: 0, 
    	  behavior: 'smooth' 
    	});*/

    return false;
  }
  return true;
}

function uploadimageajax(){
	var myform = document.getElementById("postads_form");
	//var formData = jQuery(myform).serializeArray();
	var formData = new FormData(myform);
	index = jQuery("#index").val();
	jQuery.ajax({
        url: baseurl+"/postsads/upload",
        type: "post",
        data: formData,		
		cache: false,

		 processData: false,
		contentType: false,
		cache: false,        
        success: function(data){
			jQuery("#ads-image"+index).val(data);
			},
			error: function(err) {
				alert(err);
			}
		});
}

/*jQuery("#postads_form").on('submit',(function(e) {
	e.preventDefault();
//	$("#message").empty();
//	$('#loading').show();
	
	index = jQuery("#index").val();
	jQuery.ajax({
		url: baseurl+"/postsads/upload", // Url to which the request is send
		type: "POST",             // Type of request to be send, called as method
		data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,        // To send DOMDocument or non processed data file it is set to false
		success: function(data)   // A function to be called if request succeeds
		{
			//alert(data);
			jQuery("#ads-image"+index).val(data);
//			$('#loading').hide();
//			$("#message").html(data);
		}
	});
}));*/


