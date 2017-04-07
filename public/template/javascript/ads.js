/*-----------------------------------------------------------------------------------*/
/*	Custom Script
/*-----------------------------------------------------------------------------------*/

var slideIndex = 1;
	showDivs(slideIndex);
	
	function plusDivs(n) {
	  showDivs(slideIndex += n);
	}
	
	function currentDiv(n) {
	  showDivs(slideIndex = n);
	}
	
	function showDivs(n) {
	  var i;
	  var x = document.getElementsByClassName("mySlides");
	 
	  if (n > x.length) {slideIndex = 1}
	  if (n < 1) {slideIndex = x.length}
	  for (i = 0; i < x.length; i++) {
		 x[i].style.display = "none";
	  }
	  x[slideIndex-1].style.display = "initial";
	
	}
	
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
			document.getElementById('pop-title').innerHTML ='<a class="ads-title" href="'+baseurl+'/index/ads-detail/ads/'+val.id+'" >'+val.ads_title+'</a>';
			document.getElementById('pop-viewer').innerHTML = val.viewer;
			document.getElementById('pop-date').innerHTML = c_day+" "+monthNames[monthIndex]+" "+year;
			document.getElementById('pop-price').innerHTML = "$ "+parseFloat(val.price).toFixed(3);

			document.getElementById('pop-description').innerHTML = val.description;
			document.getElementById('pop-author').innerHTML = '<a href="'+baseurl+'/index/store/user/'+val.user_id+'" >'+val.author+'</a>';
			document.getElementById('pop-category').innerHTML = val.parent_cateogry_title;
			document.getElementById('pop-location').innerHTML = val.province;
			},
			error: function(err) {
				alert(err);
			}
		});
	 $("#pop-ads-detail").css("display", "block");
	 $("#load-wrapper").css("display", "none");
}
jQuery("#close-detail").live("click", closeformdetail);
window.onclick = function(event) {//When the user clicks anywhere outside of the modal, close it
	if (event.target == document.getElementById("pop-ads-detail")) {
		closeformdetail();
	}

}
function closeformdetail() {
	 $("#pop-ads-detail").css("display", "none");
}
