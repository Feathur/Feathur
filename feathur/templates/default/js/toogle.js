
/* Stats Toogle */
$(document).ready(function(){
  $("#open-stats, #stats .close").click(function(){
    $("#stats").slideToggle()
  });
});


/* Simple Tips */
$(document).ready(function(){
  $(".simple-tips .close").click(function(){
    $(".simple-tips").slideToggle()
  });
});




/* ALERT AND DIALOG BOXES */
//<![CDATA[    
   // START ready function
   $(document).ready(function(){
 
	// TOGGLE SCRIPT
 
	$(".albox .close").click(function(event){
		$(this).parents(".albox").slideToggle();
 
		// Stop the link click from doing its normal thing
		return false;
	}); // END TOGGLE
 
   }); // END ready function
 //]]>



//<![CDATA[    
   // START ready function
   $(document).ready(function(){
 
	// TOGGLE SCRIPT
 
	$(".toggle-message .title, .toggle-message p").click(function(event){
		$(this).parents(".toggle-message").find(".hide-message").slideToggle();
 
		// Stop the link click from doing its normal thing
		return false;
	}); // END TOGGLE
 
   }); // END ready function
 //]]>





/* SUBMENU */
//<![CDATA[    
   // START ready function
   $(document).ready(function(){
 
	// TOGGLE SCRIPT
	$(".subtitle .action").click(function(event){
		$(this).parents(".subtitle").find(".submenu").slideToggle();
 
		// Stop the link click from doing its normal thing
		return false;
	}); // END TOGGLE
 
   }); // END ready function
 //]]>


