var isSmallScreen = false;
var prevTab=1;

function showCon(i)
{
  if(i != prevTab)
  {
    $(".tab").removeClass("cur");
    $(".tab.btn"+prevTab).removeClass("cur");
    $(".tab.btn"+i).addClass("cur");
    for(var n=1;n < $('.tabs.primarytabs').children().size()+2;n++)
    {
      $("#tabCon.con"+n).hide();
    }
    $("#tabCon.con"+i).show();
    $("#tabConWrap").css("height",$("#tabCon.con"+i).height() + "px");
    prevTab=i;
    $(".chosen-select").chosen(); //recheck for fancy dropdowns
  }
}

function loading(number)
{
  $("#loading").css("margin-bottom",((number === 1) ? "0" : "-60px"));
}

function setNotice(div, html, type)
{
  $(div).addClass('alert '+type+'box').html(html);
}

function doSidebarChanges()
{
  // Make room for the second sidebar, if enabled
  if($("#sidebar2").css("display") != "none")
  {
    $("#page-wrapper").css("width","calc(100% - 270px - 1em)");
  } else {
    $("#page-wrapper").css("width","calc(100% - 1em)");
  }

  if(isSmallScreen)
  {
    $("#sidebar").css("margin-left","-195px");
    $(".tabs.primarytabs").css("left","60px");
  } else {
    $("#sidebar").css("margin-left","0px");
    $(".tabs.primarytabs").css({"left" : "245px", "margin-left" : "0px"});
  }
}


$(document).ready(function()
{

  var notSmall = (($(window).width > 960) ? false : true);

  if($(window).width() > 960 && isSmallScreen === true)
  {
    isSmallScreen = false;
  } else if($(window).width() <= 960 && isSmallScreen === false){
    isSmallScreen = true;
  }

  // Remove the html box-shadow if on login,activate,forgot page
  if($("body").hasClass("login"))
  {
    $("html").css("box-shadow","none");
  }

  $("html, body, .wrapper").css("min-height", $(window).height() + "px");

  /* SUBMENU */

  $(".navcat .action").click(function(event)
  {
    $(this).parents(".navcat").find(".navsub").slideToggle();
    return false;
  });
 
  // Fixes display:inline-block from pure-g class from causing
  // all tabbed contents to either show or not show on initial load

  if($("#tabCon.con1").length)
  {
    $("#tabCon.con1").show();
  }
    
  $(window).on('load resize', doSidebarChanges);

  $("#GeneralNotice").click(function(){
    $(this).slideUp();
  });

  $("#sbtoggle").click(function()
  {
    if ($("#sidebar").css("margin-left") != "0px")
    {
      $("#sidebar").css("margin-left","0px");
      $(".tabs.primarytabs").css("left","260px");
    } else {
      $("#sidebar").css("margin-left","-195px");
      $(".tabs.primarytabs").css("left","60px");
    }
  });
    
  $("#profilebox .profilemenu").css("height","0px").removeClass("open");
  $("#profilebox").click(function()
  {
    if($(".profilemenu").css("height") == "0px")
    {
      $("#profilebox .profilemenu").css("height","67px");
      $("#profilebox").addClass("open");
    } else {
      $("#profilebox .profilemenu").css("height","0px");
      $("#profilebox").removeClass("open");
    }
  });
});
