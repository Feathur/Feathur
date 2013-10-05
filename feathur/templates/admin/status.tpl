<div id="Status">
<br>
<script type="text/javascript">
	function uptime() {
		$(function() {
			$.getJSON("admin.php?status=1",function(result){
				$("#Status").html(result.content);
			});
		});
	}
	{%if isset|Status == false}
		setInterval(uptime, {%?RefreshTime}000);
	{%/if}
</script>
{%if isempty|Data == true}
	<div align="center">Unfortunately no servers have been configured for this uptime script.</div>
{%/if}
{%if isempty|Data == false}
	<div align="center">This page automatically updates every {%?RefreshTime} seconds. Network uptime may not be 100% accurate.</div>
	<br><br>
	<div align="center"><div class="simplebox grid740">
		<div class="titleh">
			<h3>Server Status</h3>
		</div>
		<table id="ServerStatus" class="tablesorter"> 
			<thead>
				<tr>
					<th syle="width:5%"><div align="center">Status</div></th>
					<th syle="width:15%"><div align="center">Name</div></th>
					<th syle="width:20%"><div align="center">Uptime</div></th>
					<th syle="width:40%"><div align="center">Data</div></th>
				</tr>
			</thead>
			<tbody>
			{%foreach server in Data}
				<tr>
					<td><div align="center"><img src="./templates/status/{%if isempty|server[status] == true}offline{%/if}{%if isempty|server[status] == false}online{%/if}.png" style="width:25px;height:25px;"></div></td>
					<td>
						{%if isempty|server[display_hs] == false}<a href="history.php?id={%?server[id]}">{%?server[name]}</a>{%/if}
						{%if isempty|server[display_hs] == true}{%?server[name]}{%/if}
						{%if isempty|server[display_location] == false}<br>{%?server[location]}{%/if}
					</td>
					<td>
						{%if isempty|server[display_network_uptime] == false}NW: {%?server[network_uptime]}{%/if}
						{%if isempty|server[display_network_uptime] == false}{%if isempty|server[display_hardware_uptime] == false}<br>{%/if}{%/if}
						{%if isempty|server[display_hardware_uptime] == false}HW: {%?server[hardware_uptime]}{%/if}
					</td>
					<td>
						<div id="box-data">
							<div style="width:{%?server[percent]}%;float:left;">
								{%if isempty|server[display_load] == false}
									<div align="center"><strong>Load:</strong> {%if isset|server[load_average] == false}N/A{%/if}{%?server[load_average]}</div>
								{%/if}
								{%if isempty|server[display_bandwidth] == false}
									<div align="center"><strong>BW:</strong> {%if isset|server[bandwidth] == false}N/A{%/if}{%?server[bandwidth]} MB/s</div>
								{%/if}
							</div>
							{%if isempty|server[display_memory] == false}
								<div style="width:{%?server[percent]}%;float:left;margin-left:10px;">
									<div align="center"><strong>RAM Usage</strong></div>
									<div class="progress" style="padding:0;margin:0;">
										<div class="bar bar-warning" style="width: {%?server[percent_used_memory]}%;padding-top:5px;">U</div>
										<div class="bar bar-success" style="width: {%?server[percent_free_memory]}%;padding-top:5px;">F</div>
									</div>
								</div>
							{%/if}
							{%if isempty|server[display_hard_disk] == false}
								<div style="width:{%?server[percent]}%;float:right;margin-left:10px;">
									<div align="center"><strong>Disk Usage</strong></div>
									<div class="progress" style="padding:0;margin:0;">
										<div class="bar bar-warning" style="width: {%?server[percent_used_disk]}%;padding-top:5px;">U</div>
										<div class="bar bar-success" style="width: {%?server[percent_free_disk]}%;padding-top:5px;">F</div>
									</div>
								</div>
							{%/if}
						</div>
					</td>
				</tr>
			{%/foreach}
			</tbody>
		</table>
	</div></div>
{%/if}
</div>