<div id="Status" style="margin:5px;">
	<script type="text/javascript">
		$(document).ready(function() {
			var counttx = 0;
			function timerrx() {
				counttx=counttx+1;
				$('#timer').html(counttx);
			}
			function uptime() {
					$(function() {
							$.getJSON("admin.php?json=1",function(result){
									$("#Status").html(result.content);
									counttx=0;
							});
					});
			}
			{%if isset|Status == false}
				setInterval(uptime, {%?RefreshTime}000);
				setInterval(timerrx, 1000);
			{%/if}
		});
	</script>
	<div align="center">Welcome to Feathur, here is a quick system report:</div><br><br>
	{%if isset|Down == true}
		<div class="albox errorbox">
			Server(s) Down: {%foreach system in Down}{%?system[name]}, {%/foreach}
			<a href="#" class="close tips" title="close">close</a>
		</div>
		<br><br>
	{%/if}
	<div style="width:30px;display:inline;white-space:nowrap;">Last update: <a id="timer" style="white-space:nowrap;">0</a> seconds ago</div>
	<br><br>
	{%if isset|Statistics == true}
		{%foreach server in Statistics}
			<div class="simplebox grid360-{%if isempty|server[type] == true}right{%/if}{%if isempty|server[type] == false}left{%/if}" style="padding:3px;padding-bottom:10px;">
				<div class="titleh">
					<h3>
						<div style="width:39%;float:left;">
							<img src="./templates/status/{%if isempty|server[status] == true}offline{%/if}{%if isempty|server[status] == false}online{%/if}.png" style="width:10px;height:10px;">{%if isset|server[name] == true}{%?server[name]}{%/if}
						</div>
						{%if isempty|server[status] == false}
							<div style="width:59%;float:right;padding-right:5px;" align="right">
								{%if isempty|server[load_average] == false}Load: {%?server[load_average]}{%/if}
								{%if isempty|server[load_average] == false}{%if isempty|server[bandwidth] == false}&nbsp;|&nbsp;{%/if}{%/if}
								{%if isempty|server[bandwidth] == false}BW: {%?server[bandwidth]}{%/if}
							</div>
						{%/if}
					</h3>
				</div>
				<div class="body padding10">
					<div align="center" style="height:50px;">
						{%if isempty|server[status] == false}
							Uptime: {%if isempty|server[uptime] == false}{%?server[uptime]}{%/if}
							<hr>
							<div style="width:40%;float:left;">
								<strong>Memory Usage:</strong>
								<div class="progress" style="padding:0;margin:0;">
									<div class="bar bar-warning" style="width: {%?server[ram_usage]}%;padding-top:5px;">U</div>
									<div class="bar bar-success" style="width: {%?server[ram_free]}%;padding-top:5px;">F</div>
								</div>
							</div>
							<div style="width:40%;float:right;">
								<strong>Disk Usage:</strong>
								<div class="progress" style="padding:0;margin:0;">
									<div class="bar bar-warning" style="width: {%?server[disk_usage]}%;padding-top:5px;">U</div>
									<div class="bar bar-success" style="width: {%?server[disk_free]}%;padding-top:5px;">F</div>
								</div>
							</div>
						{%/if}
						{%if isempty|server[status] == true}
							Server is currently unconnectable.
						{%/if}
					</div>
				</div>
			</div>
			{%if isempty|server[type] == true}<div class="clear"></div>{%/if}
		{%/foreach}
	{%/if}
	{%if isset|Statistics == false}
		<br><br>
		<div align="center">
			Add a server to Feathur so updates will appear here.
		</div>
	{%/if}
</div>