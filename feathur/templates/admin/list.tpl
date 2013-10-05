<br><br>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		oTablea = $('#vpstable').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"aaSorting": [[ 0, "asc" ]],
			"bSort": false,
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"iDisplayLength": 10,
			"bStateSave": true,
			"oLanguage": {
			"sEmptyTable": "No Entries"
			}
		});
		oTableb = $('#usertable').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"aaSorting": [[ 0, "asc" ]],
			"bSort": false,
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"iDisplayLength": 10,
			"bStateSave": true,
			"oLanguage": {
			"sEmptyTable": "No Entries"
			}
		});
		
		oTablec = $('#servertable').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort": false,
			"aaSorting": [[ 0, "asc" ]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"iDisplayLength": 10,
			"bStateSave": true,
			"oLanguage": {
			"sEmptyTable": "No Entries"
			}
		});
	});
</script>
	{%if isset|Result == true}
		{%if isset|VPSCount == true}
			<div align="center"><div class="simplebox grid740">
				<div class="titleh">
					<h3>VPS</h3>
			</div>
			<table class="tablesorter" id="vpstable">
				<thead>
					<tr>
						<th width="30%"><div align="center">Hostname</div></th>
						<th width="25%"><div align="center">User</div></th>
						<th width="25%"><div align="center">Primary IP</div></th>
						<th width="10%"><div align="center">Type</div></th>
					</tr>
				</thead>
				{%foreach entry in Result}
					{%if entry[result_type] == vps}
						<tr>
							<td><a href="view.php?id={%?entry[id]}">{%?entry[hostname]}</a></td>
							<td><div align="center"><a href="admin.php?view=clients&id={%?entry[user_id]}">{%?entry[username]}</a></div></td>
							<td><div align="center">{%?entry[primary_ip]}</div></td>
							<td><div align="center">{%?entry[type]}</div></td>
						</tr>
					{%/if}
				{%/foreach}
			</table></div></div>
		{%/if}
		{%if isset|UserCount == true}
			<div align="center"><div class="simplebox grid740">
				<div class="titleh">
					<h3>Users</h3>
			</div>
			<table class="tablesorter" id="usertable">
				<thead>
					<tr>
						<th width="40%"><div align="center">Client Username</div></th>
						<th width="30%"><div align="center">Email Address</div></th>
						<th width="30%"><div align="center">View VPS</div></th>
					</tr>
				</thead>
				{%foreach entry in Result}
					{%if entry[result_type] == user}
						<tr>
							<td><a href="admin.php?view=clients&id={%?entry[id]}">{%?entry[username]}</a></td>
							<td><div align="center">{%?entry[email_address]}</div></td>
							<td><div align="center"><a href="admin.php?view=list&type=search&search=user={%?entry[id]}">Client VPS</a></div></td>
						</tr>
					{%/if}
				{%/foreach}
			</table></div></div>
		{%/if}
		{%if isset|ServerCount == true}
			<div align="center"><div class="simplebox grid740">
				<div class="titleh">
					<h3>Servers</h3>
				</div>
			<table class="tablesorter" id="servertable">
				<thead>
					<tr>
						<th width="60%"><div align="center">Server Name</div></th>
						<th width="20%"><div align="center">IP Address</div></th>
						<th width="20%"><div align="center">Type</div></th>
					</tr>
				</thead>
				{%foreach entry in Result}
					{%if entry[result_type] == server}
						<tr>
							<td><a href="admin.php?view=list&type=search&search=server={%?entry[id]}">{%?entry[name]}</a></td>
							<td><div align="center">{%?entry[ip_address]}</div></td>
							<td><div align="center">{%?entry[type]}</div></td>
						</tr>
					{%/if}
				{%/foreach}
			</table></div></div>
		{%/if}
	{%/if}
	{%if isset|ServerCount == false}
		{%if isset|UserCount == false}
			{%if isset|VPSCount == false}
				{%if isset|Result == false}
					<div align="center">Unfortunately no results returned for your query.</div>
				{%/if}
			{%/if}
		{%/if}
	{%/if}