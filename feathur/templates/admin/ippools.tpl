<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		oTable = $('#BlockListTable').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bSort": false,
				"aaSorting": [[ 0, "asc" ]],
				"iDisplayLength": -1
				"oLanguage": {
						"sEmptyTable": "There are no IP blocks, add one using the + above."
				}
		});
	});
</script>
{%if isset|Type == false}
	<div align="center">
		<br><br>
		<table style="border:0px solid white;width:50%;height:100px;">
			<tr>
				<td width="40%" align="center">
					<a class="button-blue" href="admin.php?view=ippools&type=0">IPv4 Pools</a>
				</td>
				<td width="40%" align="center">
					<a class="button-blue" href="admin.php?view=ippools&type=1">IPv6 Pools</a>
				</td>
			</tr>
		</table>
	</div>
{%/if}
{%if isset|Type == true}
	{%if isempty|Type == true}
		<br><br>
		<div align="center">
			<h4>IPv4 Pools</h4>
			{%if isset|BlockList == true}
				<table class="tablesorter" id="BlockListTable" style="width:90%">
					<thead>
							<tr>
									<th width="60%"><div align="center">Name</div></th>
									<th width="20%"><div align="center">Usage</div></th>
									<th width="20%"><div align="center">Actions</div></th>
							</tr>
					</thead>        
					{%if isset|BlockList == true}
							{%foreach block in BlockList}
									<tr>
											<td><a href="#" class="ManageBlock" value="{%?block[id]}">{%?block[name]}</a></td>
											<td><div align="center">{%?block[used]} / {%?block[total]}</div></td>
											<td>
													<div align="center">
															<a original-title="Delete" class="icon-button tips DeleteBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?block[name]}" value="{%?block[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
															<a original-title="Edit" class="icon-button tips EditBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?block[name]}" value="{%?block[id]}"><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
													</div>
											</td>
									</tr>
							{%/foreach}
					{%/if}
					{%if isset|BlockList == false}
							<tr>
									<td></td>
									<td></td>
									<td></td>
							</tr>
					{%/if}
				</table>
			{%/if}
			{%if isset|BlockList == false}
				<br><br>
				<div align="center">There are currently no pools defined.</div>
			{%/if}
		</div>
	{%/if}
	{%if isempty|Type == false}
		<br><br>
		<div align="center">
			<h4>IPv6 Pools</h4>
			{%if isset|BlockList == true}
				<table class="tablesorter" style="width:90%">
					<thead>
							<tr>
									<th width="60%"><div align="center">Name</div></th>
									<th width="20%"><div align="center">Actions</div></th>
							</tr>
					</thead>        
					{%if isset|BlockList == true}
							{%foreach block in BlockList}
									<tr>
											<td><a href="#" class="ManageBlock" value="{%?block[id]}">{%?block[name]}</a></td>
											<td>
													<div align="center">
															<a original-title="Delete" class="icon-button tips DeleteBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?block[name]}" value="{%?block[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
															<a original-title="Edit" class="icon-button tips EditBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?block[name]}" value="{%?block[id]}"><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
													</div>
											</td>
									</tr>
							{%/foreach}
					{%/if}
					{%if isset|BlockList == false}
							<tr>
									<td></td>
									<td></td>
									<td></td>
							</tr>
					{%/if}
				</table>
			{%/if}
			{%if isset|BlockList == false}
				<br><br>
				<div align="center">There are currently no pools defined.</div>
			{%/if}
		</div>
	{%/if}
{%/if}