
/* Tipsy */
$(function(){ $('.tips').tipsy({gravity: 's',html: true}); });
$(function(){ $('.tips-right').tipsy({gravity: 'w',html: true}); });
$(function(){ $('.tips-left').tipsy({gravity: 'e',html: true}); });
$(function(){ $('.tips-bottom').tipsy({gravity: 'n',html: true}); });



/* Ie7 z-index fix */
$(function() {
    var zIndexNumber = 1000;
    $('div').each(function() {
        $(this).css('zIndex', zIndexNumber);
        zIndexNumber -= 10;
    });
});



/* Form Style */
$(function(){

$(".st-forminput").focus(function(){

$(this).attr('class','st-forminput-active ');

});

$(".st-forminput").blur(function(){

$(this).attr('class','st-forminput');

});

});




/* Login Input Form */
$(function(){

$(".login-input-pass").focus(function(){

$(this).attr('class','login-input-pass-active ');

});

$(".login-input-pass").blur(function(){

$(this).attr('class','login-input-pass');

});

});
$(function(){

$(".login-input-user").focus(function(){

$(this).attr('class','login-input-user-active ');

});

$(".login-input-user").blur(function(){

$(this).attr('class','login-input-user');

});

});




/* Uniform */
      $(function(){
        $(".uniform").uniform();
      });


/* jWYSIWYG editor */
  $(function()
  {
      $('#wysiwyg').wysiwyg();
  });


/* Table Shorter */
$(document).ready(function() 
    { 
        $("#myTable").tablesorter(); 
    } 
); 




/* Full Calendar */

	$(document).ready(function() {
	
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			editable: true,
			events: [
				{
					title: 'All Day Event',
					start: new Date(y, m, 1)
				},
				{
					title: 'Long Event',
					start: new Date(y, m, d-5),
					end: new Date(y, m, d-2)
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d-3, 16, 0),
					allDay: false
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d+4, 16, 0),
					allDay: false
				},
				{
					title: 'Meeting',
					start: new Date(y, m, d, 10, 30),
					allDay: false
				},
				{
					title: 'Lunch',
					start: new Date(y, m, d, 12, 0),
					end: new Date(y, m, d, 14, 0),
					allDay: false
				},
				{
					title: 'Birthday Party',
					start: new Date(y, m, d+1, 19, 0),
					end: new Date(y, m, d+1, 22, 30),
					allDay: false
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'http://google.com/'
				}
			]
		});
		
	});




/* iphone style switches */
	$(document).ready( function(){ 
		$(".cb-enable").click(function(){
			var parent = $(this).parents('.switch');
			$('.cb-disable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('.checkbox',parent).attr('checked', true);
		});
		$(".cb-disable").click(function(){
			var parent = $(this).parents('.switch');
			$('.cb-enable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('.checkbox',parent).attr('checked', false);
		});
	});


