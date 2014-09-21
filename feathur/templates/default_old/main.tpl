{%if isset|UserVPS == true}
<br><br>
<script type="text/javascript">
	$(document).ready(function() {
		{%foreach server in UserVPS}
			$.getJSON("view.php?id={%?server[id]}&action=statistics",function(result){
					$('#UpDown{%?server[id]}').html('<img src="templates/status/' + result.result + '.png" style="width:15px;height:15px;">');
			});
		{%/foreach}
	});
</script>
	<div align="center">
		<div class="simplebox grid740">
			<div class="titleh">
				<h3>Your VPS</h3>
			</div>
			<table class="tablesorter">
				<thead>
					<tr>
						<th width="40px"><div align="center">U/D</div></th>
						<th width="25%"><div align="center">Hostname</div></th>
						<th width="20%"><div align="center">Server</div></th>
						<th width="15%"><div align="center">Type</div></th>
						<th width="20%"><div align="center">Primary IP</div></th>
						<th width="10%"><div align="center">View</div></th>
					</tr>
				</thead>
				<tbody>
					{%foreach server in UserVPS}
						<tr>
							<td valign="middle"><div align="center"><div id="UpDown{%?server[id]}" style="width:15px;"></div></div></td>
							<td valign="middle"><a href="view.php?id={%?server[id]}">{%?server[hostname]}</a></td>
							<td valign="middle"><div align="center">{%?server[server_name]}</div></td>
							<td valign="middle"><div align="center">{%?server[type]}</div></td>
							<td valign="middle"><div align="center">{%?server[primary_ip]}</div></td>
							<td valign="middle"><div align="center"><a href="view.php?id={%?server[id]}" class="button small blue">View</a></div></td>
						</tr>
					{%/foreach}
				</tbody>
			</table>
		</div>
	</div>
{%/if}