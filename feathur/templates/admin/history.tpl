{%if isempty|AllowUserNotifications == false}<div align="center"><a href="notifications.php?id={%?ServerId}">Notify Me When This Server Goes Down</a></div>{%/if}
{%if isset|History == true}
	<h5 style="padding:0;margin:0;">{%?ServerName} History</h5>
	<hr style="padding:0;margin:0;">
	<div align="center">
		<table class="striped" style="width:50%">
			<thead>
				<tr>
					<th width="50%"><div align="center">Status</div></th>
					<th width="50%"><div align="center">Date/Time</div></th>
				</tr>
			</thead>
			<tbody>
				{%foreach date in History}
					<tr>
						<td>
							{%if isempty|date[status] == false}<font color="green">Server Online</font>{%/if}
							{%if isempty|date[status] == true}<font color="red">Server Offline</font>{%/if}
						</td>
						<td><div align="center">{%?date[date]}</div></td>
					</tr>
				{%/foreach}
			</tbody>
		</table>
	</div>
{%/if}
{%if isset|Statistics == true}
	<script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Date/Time', 'Used', 'Total'],
			{%foreach entry in Statistics}
				['{%?entry[date]}', {%?entry[used_memory]}, {%?entry[total_memory]}],
			{%/foreach}
			]);
			var options = {
          			title: '',
				hAxis: {title: 'Date/Time'}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('memoryusage'));
			chart.draw(data, options);
		}
		google.load("visualization", "1", {packages:["corechart"]});
	</script>
	<script type="text/javascript">
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Date/Time', 'Used', 'Total'],
			{%foreach entry in Statistics}
				['{%?entry[date]}', {%?entry[hard_disk_used]}, {%?entry[hard_disk_total]}],
			{%/foreach}
			]);
			var options = {
          			title: '',
				hAxis: {title: 'Date/Time'}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('diskusage'));
			chart.draw(data, options);
		}
	</script>
	<script type="text/javascript">
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Date/Time', 'Load'],
			{%foreach entry in Statistics}
				['{%?entry[date]}', {%?entry[load_average]}],
			{%/foreach}
			]);
			var options = {
          			title: '',
				hAxis: {title: 'Date/Time'}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('loadaverage'));
			chart.draw(data, options);
		}
	</script>
	<script type="text/javascript">
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable([
			['Date/Time', 'MB/s'],
			{%foreach entry in Statistics}
				['{%?entry[date]}', {%?entry[bandwidth]}],
			{%/foreach}
			]);
			var options = {
          			title: '',
				hAxis: {title: 'Date/Time'}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('bandwidth'));
			chart.draw(data, options);
		}
	</script>
	<br><br>
	<div align="center"><strong>Server graphs for the last {%?GraphLimit} days</strong></div>
	<div>
		<div style="float:left;width:400px;">
			<div align="center"><h5>Load Average</h5></div>
			<div id="loadaverage" style="width:400px;height:300px;"></div>
		</div>
		<div style="float:right;width:400px;">
			<div align="center"><h5>Memory Usage (GB)</h5></div>
			<div id="memoryusage" style="width:400px;height:300px;"></div>
		</div>
	</div>
	<div>
		<div style="float:left;width:400px;">
			<div align="center"><h5>Disk Usage (GB)</h5></div>
			<div id="diskusage" style="width:400px;height:300px;"></div>
		</div>
		<div style="float:right;width:400px;">
			<div align="center"><h5>Bandwidth</h5></div>
			<div id="bandwidth" style="width:400px;height:300px;"></div>
		</div>
	</div>
{%/if}