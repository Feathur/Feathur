<?php

$cphp_debug_start = microtime(true);
$cphp_debug_log = array();
$cphp_debug_enabled = false;

function cphp_debug_enable()
{
	global $cphp_debug_enabled;
	
	$cphp_debug_enabled = true;
}

function cphp_debug_disable()
{
	global $cphp_debug_enabled;
	
	$cphp_debug_enabled = false;
}

function cphp_debug_snapshot($data)
{
	global $cphp_debug_start, $cphp_debug_log, $cphp_debug_enabled;
	
	if($cphp_debug_enabled === true)
	{
		$timestamp = microtime(true) - $cphp_debug_start;
		
		$cphp_debug_log[] = array(
			'timestamp'	=> $timestamp,
			'data'		=> $data
		);
	}
}

function cphp_debug_dump()
{
	global $cphp_debug_log;
	
	return json_encode($cphp_debug_log);
}

function cphp_debug_display($data)
{
	/* We can't use the templater for this, because that would make this function unusable if the
	 * templater itself were to ever be the subject of the debugging. */
	?>
	<!doctype html>
	<html>
		<head>
			<title>CPHP Debuglog Viewer</title>
			<style>
				body
				{
					font-family: monospace;
				}
				
				#slider
				{
					background-color: #DDDDDD;
					position: absolute;
					left: 0px;
					right: 0px;
					top: 0px;
					height: 100px;
					user-select: none;
					-moz-user-select: none;
					-webkit-user-select: none;
					-ms-user-select: none;
				}
				
				#slider_bar
				{
					background-color: #C8C8C8;
					height: 24px;
					position: relative;
				}
				
				#slider_handle
				{
					width: 24px;
					height: 24px;
					position: absolute;
					left: 0px;
					top: 0px;
					background-color: #6F6F6F;
					cursor: pointer;
				}
				
				#slider_handle.dragging
				{
					background-color: #000000;
				}
				
				#datapoint_info
				{
					padding: 7px;
				}
				
				#details
				{
					position: absolute;
					left: 0px;
					right: 0px;
					bottom: 0px;
					top: 100px;
					overflow: auto;
				}
				
				.variable
				{
					padding-left: 48px;
					border-top: 1px solid silver;
				}
				
				.variable .name
				{
					font-size: 15px;
					font-weight: bold;
					margin-right: 6px;
				}
				
				.variable .data
				{
					font-size: 13px;
				}
				
				a.expander
				{
					text-decoration: none;
					color: blue;
					font-weight: bold;
					font-size: 14px;
				}
				
				.data.undefined
				{
					color: silver;
				}
				
				.data.text
				{
					color: #A000B2;
				}
				
				.data.numeric
				{
					color: red;
				}
			</style>
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
			<script>
				var data_points = <?php echo($data); ?>;
				
				var total_data_points = data_points.length;
				
				var current_point = 0;
				
				function sliderUpdate()
				{
					total_width = $('#slider_bar').width() - $('#slider_handle').width();
					current_position = $('#slider_handle').offset().left;
					
					point_width = total_width / (total_data_points - 1);
					closest_data_point = Math.round(current_position / point_width);
					current_point = closest_data_point;
					
					real_position = closest_data_point * point_width;
					$('#slider_handle').css({'left': real_position});
					
					relevant_data_point = data_points[closest_data_point];
					
					$('#datapoint_info').html("<strong>Data point:</strong> " + closest_data_point + "<br><strong>Timestamp:</strong> " + relevant_data_point['timestamp'])
					
					updateTree(relevant_data_point['data']);
				}
				
				function switchDataPoint(i)
				{
					total_width = $('#slider_bar').width() - $('#slider_handle').width();
					point_width = total_width / (total_data_points - 2);
					real_position = i * point_width;
					$('#slider_handle').css({'left': real_position});
					
					current_point = i;
					
					$('#datapoint_info').html("<strong>Data point:</strong> " + i + "<br><strong>Timestamp:</strong> " + data_points[i]['timestamp'])
					
					updateTree(data_points[i]['data']);
				}
				
				function updateTree(data)
				{
					$('.variable.array').children('.variable').remove();
					$('.data.text, .data.numeric').html("undefined").addClass("undefined").removeClass("text").removeClass("numeric");
					updateElements(data, "root", "item");
					
					$('a.expander').each(function(){
						if($(this).text() == "[+]")
						{
							$(this).parent().children('.variable').hide();
						}
					});
					
					hookExpanders();
				}
				
				function initializeElements()
				{
					/* Build a prototype for display, out of all the available datapoints. */
					prototype = {};
					
					for(x in data_points)
					{
						prototype = $.extend(true, prototype, data_points[x]);
					}
					
					createElements(prototype['data'], "root", "item").appendTo('#details');
				}
				
				function createElements(source, key, hierarchy)
				{
					var item;
					var id = hierarchy + "_" + key.replace(/[^a-z0-9_]/gi,'');
					
					if($.isArray(source))
					{
						/* Array. */
						var me = $('<div class="variable array" id="' + id + '"><a class="expander" href="javascript:void(0);">[-]</a><span class="name">' + key + '</span><span class="data undefined">Array</span></div>');
					}
					else if($.isPlainObject(source))
					{
						/* Object. */
						var me = $('<div class="variable object" id="' + id + '"><a class="expander" href="javascript:void(0);">[-]</a><span class="name">' + key + '</span><span class="data undefined">Object</span></div>');
						
						for(item in source)
						{
							me.append(createElements(source[item], item, id));
						}
					}
					else
					{
						/* Value. */
						var me = $('<div class="variable value" id="' + id + '"><span class="name">' + key + '</span><span class="data undefined">undefined</span></div>');
					}
					
					return me;
				}
				
				function updateElements(source, key, hierarchy)
				{
					var item;
					var id = hierarchy + "_" + key.replace(/[^a-z0-9_]/gi,'');
					
					if($.isArray(source))
					{
						/* Array. */
						//$('#' + id).children('.variable').remove();
						
						for(item in source)
						{
							$('#' + id).append(createElements(source[item], item, id));
							updateElements(source[item], item, id);
						}
					}
					else if($.isPlainObject(source))
					{
						/* Object. */
						
						for(item in source)
						{
							updateElements(source[item], item, id);
						}
					}
					else
					{
						/* Value. */
						var target = $('#' + id).children('.data');
						
						target.html(source);
						target.removeClass("undefined");
						
						if(typeof(source) == "number")
						{
							target.addClass("numeric");
						}
						else
						{
							target.addClass("text");
						}
					}
				}
				
				function hookExpanders()
				{
					$('a.expander').click(function(){
						if($(this).text() == "[-]")
						{
							/* Collapse */
							$(this).text("[+]");
							$(this).parent().children(".variable").hide();
						}
						else
						{
							/* Expand */
							$(this).text("[-]");
							$(this).parent().children(".variable").show();
						}
					});
				}
				
				$(function(){
					var drag_start_x = 0;
					var drag_start_y = 0;
					var dragging_slider = false;
					
					$('#slider_handle').mousedown(function(e){
						dragging_slider = true;
						parent_offset = $(this).offset();
						drag_start_x = e.pageX - parent_offset.left;
						$('#slider_handle').addClass("dragging");
					});
					
					$('body').mouseup(function(e){
						if(dragging_slider == true)
						{
							dragging_slider = false;
							$('#slider_handle').removeClass("dragging");
						}
					});
					
					$('body').mousemove(function(e){
						//$('#details').html("Dragging: "+dragging_slider+", drag_start_x: "+drag_start_x+", pageX: "+e.pageX);
						if(dragging_slider == true)
						{
							newpos = e.pageX - drag_start_x;
							
							if(newpos > 0 && newpos < (total_width))
							{
								$('#slider_handle').css({'left': newpos});
							}
							else if(newpos < 0)
							{
								$('#slider_handle').css({'left': 0});
							}
							else if(newpos > (total_width))
							{
								$('#slider_handle').css({'left': total_width});
							}
							
							sliderUpdate();
						}
					});
					
					$(document).keydown(function(e){
						if(e.keyCode == 37 && current_point > 0) 
						{ 
							switchDataPoint(current_point - 1);
						}
						else if(e.keyCode == 39 && current_point < total_data_points - 2)
						{
							switchDataPoint(current_point + 1);
						}
					});
					
					var total_width = $('#slider_bar').width() - $('#slider_handle').width();
					
					initializeElements();
					sliderUpdate();
				});
			</script>
		</head>
		<body>
			<div id="slider">
				<div id="slider_bar">
					<div id="slider_handle"></div>
				</div>
				<div id="datapoint_info">
					
				</div>
			</div>
			<div id="details">
				
			</div>
		</body>
	</html>
	<?php
}
