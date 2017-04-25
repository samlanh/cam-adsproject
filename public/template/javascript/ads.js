/*-----------------------------------------------------------------------------------*/
/*	Custom Script
/*-----------------------------------------------------------------------------------*/
/*
	popup form
*/

var url_adsdetail = '/postsads/ads-detail';
function getAdsDetail(index){ /* get detail ads */
	$("#load-wrapper").css("display", "block");
	
	$.ajax({
		url: baseurl+url_adsdetail,
		type: "post",
		data: {'ads_code':index},
		success: function(data){
			val = $.parseJSON(data);
				
			var date = new Date(val.date_modified);
			var day = date.getDate();
			var c_day = date.getDay();
			if(c_day<10){
				c_day = '0'+c_day;
			}
			var monthIndex = date.getMonth();
			var year = date.getFullYear();
			
			document.getElementById('pop-titlebar').innerHTML =val.ads_title;
			document.getElementById('pop-title').innerHTML ='<a class="ads-title" href="'+baseurl+'/listads/detail/ads/'+val.alias+'" >'+val.ads_title+'</a>';
			document.getElementById('pop-viewer').innerHTML = val.viewer;
			document.getElementById('pop-date').innerHTML = c_day+" "+monthNames[monthIndex]+" "+year;
			document.getElementById('pop-price').innerHTML = "$ "+parseFloat(val.price).toFixed(3);

			document.getElementById('pop-description').innerHTML = val.description;
			document.getElementById('pop-author').innerHTML = '<a href="'+baseurl+'/store/index?store='+val.store_alias+'" >'+val.author+'</a>';
			document.getElementById('pop-category').innerHTML = val.parent_cateogry_title;
			document.getElementById('pop-location').innerHTML = val.province;
		
			var identity = val.images;
			var arrays = identity.split(',');
			
			var imagesSlide="";
			for(var i=0;i<arrays.length;i++) {
				var imageurl = baseurl+"/images/adsimg/"+arrays[i];
				imagesSlide+= "<img  class=\"mySlides\" src=\"" + imageurl + "\">";

			}
			$("#blog-main-slide").append(imagesSlide);
			
			var j=0;
			var html="";
			for(var k=0;k<arrays.length;k++) { j++;
				var imageurl = baseurl+"/images/adsimg/"+arrays[k];
				html+= "<div class=\"thumnail-image-slide\"><img onclick=\"currentDiv("+j+");\" class=\"images-thum\" src=\"" + imageurl + "\"></div>";
			}
			 $("#thumnail-blog").append(html);
			 showDivs(1);
			
			
			},
			error: function(err) {
				alert(err);
			}
		});
	 $("#pop-ads-detail").css("display", "block");
	 $("#load-wrapper").css("display", "none");
}

var slideIndex = 1;
	showDivs(null);
	function plusDivs(n) {
	  showDivs(slideIndex += n);
	}
	
	function currentDiv(n) {
	  showDivs(slideIndex = n);
	}
	
	function showDivs(n) {
		if(n!=null){
		  var i;
		  var x = document.getElementsByClassName("mySlides");
		 
		  if (n > x.length) {slideIndex = 1}
		  if (n < 1) {slideIndex = x.length}
		  for (i = 0; i < x.length; i++) {
			 x[i].style.display = "none";
		  }
		  x[slideIndex-1].style.display = "initial";
		}
	
	}
	
	
	

jQuery("#close-detail").live("click", closeformdetail);
window.onclick = function(event) {//When the user clicks anywhere outside of the modal, close it

	if (event.target == document.getElementById("pop-ads-detail")) {
		closeformdetail();
	}
	

}
function closeformdetail() {
	 $("#pop-ads-detail").css("display", "none");
	 document.getElementById('thumnail-blog').innerHTML = "";
			document.getElementById('blog-main-slide').innerHTML = "";
}
