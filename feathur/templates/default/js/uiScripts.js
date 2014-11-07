//<![CDATA[    
var isSmallScreen = false;
var notSmall = (($(window).width > 960) ? false : true);
var checkScreenSize = function(){
    if($(window).width() > 960 && isSmallScreen == true) {
        isSmallScreen = false;
        return false;
    } else if($(window).width() <= 960 && isSmallScreen == false){
        isSmallScreen = true;
        return true;
    }
}

// START ready function
$(document).ready(function(){
    //Remove the html box-shadow if on login,activate,forgot page
    if($("body").hasClass("login")){
        $("html").css("box-shadow","none");
    }

    $("html, body, .wrapper").css("min-height", $(window).height() + "px");
    // Alert destroyer
    $('*.alert').click(function() {
        //alert("Trying to close notice.");
        if(!$(this).hasClass("static-alert")){
            $(this).slideUp();
        }
    });

     /* SUBMENU */
	// TOGGLE SCRIPT
	$(".navcat .action").click(function(event){
		$(this).parents(".navcat").find(".navsub").slideToggle();
		return false;
	}); // END TOGGLE
 
    //Fixes display:inline-block from pure-g class from causing all tabbed contents to either show or not show on initial load
    if($("#tabCon.con1").length) {//Check if tabs exist
        $("#tabCon.con1").show();//Show first tab content by default
    }
    
    checkScreenSize();
    
    $(window).on('load resize', function(){
        doSidebarChanges();
    });

    $("#sbtoggle").click(function () {
        if ($("#sidebar").css("margin-left") != "0px") {
            $("#sidebar").css("margin-left","0px");
            $(".tabs.primarytabs").css("left","260px");
        } else {
            $("#sidebar").css("margin-left","-195px");
            $(".tabs.primarytabs").css("left","60px");
        }
    });
    
    $("#profilebox .profilemenu").css("height","0px").removeClass("open");
    $("#profilebox").click(function() {
        if($(".profilemenu").css("height") == "0px") {
            $("#profilebox .profilemenu").css("height","67px");
            $("#profilebox").addClass("open");
        } else {
            $("#profilebox .profilemenu").css("height","0px");
            $("#profilebox").removeClass("open");
        }
    });
    
}); // END ready function

var prevTab=1;
var showCon = function(i){
    if(i != prevTab){
        $(".tab").removeClass("cur");
        $(".tab.btn"+prevTab).removeClass("cur");
        $(".tab.btn"+i).addClass("cur");
        for(var n=1;n < $('.tabs.primarytabs').children().size()+2;n++){
            $("#tabCon.con"+n).hide();
        }
        $("#tabCon.con"+i).show();
        $("#tabConWrap").css("height",$("#tabCon.con"+i).height() + "px")
        prevTab=i;
    }
}

var doSidebarChanges = function(){
    checkScreenSize();
    // Make room for the second sidebar, if enabled
    if($("#sidebar2").css("display") != "none") {
        $("#page-wrapper").css("width","calc(100% - 270px - 1em)");
    } else {
        $("#page-wrapper").css("width","calc(100% - 1em)");
    }
    
    if(isSmallScreen) {
        $("#sidebar").css("margin-left","-195px");
        $(".tabs.primarytabs").css("left","60px");
    } else if(!isSmallScreen) {
        $("#sidebar").css("margin-left","0px");
        $(".tabs.primarytabs").css({"left" : "245px", "margin-left" : "0px"});
    }
};

doSidebarChanges();

var loading = function(number){
    $("#loading").css("margin-bottom",((number === 1) ? "0" : "-60px"));
};

var setNotice = function(noticediv, html, resulttype){
    //alert("Attempting to show notice");
    $(noticediv).removeClass().addClass("alert "+resulttype+"box").html(html);
};
//]]>