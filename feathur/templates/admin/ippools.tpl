<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		oTable = $('#BlockListTable').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bSort": false,
				"aaSorting": [[ 0, "asc" ]],
				"iDisplayLength": -1
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
	{%if isset|Pool == false}
		{%if isempty|Type == true}
			<br><br>
			<div align="center">
				{%if isset|BlockList == true}
					<div class="simplebox grid740">
						<div class="titleh">
								<h3>IPv4 Blocks</h3>
								<div class="shortcuts-icons">
										<a class="shortcut tips" id="AddBlock" title="Add IP Block"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
								</div>
						</div>
						<table class="tablesorter" id="BlockListTable">
							<thead>
									<tr>
											<th width="60%"><div align="center">Name</div></th>
											<th width="20%"><div align="center">Usage</div></th>
											<th width="20%"><div align="center">Actions</div></th>
									</tr>
							</thead>        
							{%if isset|BlockList == true}
								{%if isempty|BlockList == false}
									{%foreach block in BlockList}
											<tr>
													<td><a href="admin.php?view=ippools&type=0&pool={%?block[id]}">{%?block[name]}</a></td>
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
								{%if isempty|BlockList == true}
										<tr>
											<td colspan="3">
												<div align="center">There are no IP blocks, add one using the + above.</div>
											</td>
										</tr>
								{%/if}
							{%/if}
							{%if isset|BlockList == false}
									<tr>
										<td colspan="3">
											<div align="center">There are no IP blocks, add one using the + above.</div>
										</td>
									</tr>
							{%/if}
						</table>
					</div>
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
				{%if isset|BlockList == true}
					<div class="simplebox grid740">
						<div class="titleh">
								<h3>IPv6 Blocks</h3>
								<div class="shortcuts-icons">
										<a class="shortcut tips" id="AddBlock" title="Add IP Block"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
								</div>
						</div>
						<table class="tablesorter">
							<thead>
									<tr>
											<th width="60%"><div align="center">Name</div></th>
											<th width="20%"><div align="center">Actions</div></th>
									</tr>
							</thead>        
							{%if isset|BlockList == true}
								{%if isempty|BlockList == false}
									{%foreach block in BlockList}
											<tr>
													<td><a href="admin.php?view=ippools&type=1&pool={%?block[id]}">{%?block[name]}</a></td>
													<td>
															<div align="center">
																	<a original-title="Delete" class="icon-button tips DeleteBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?block[name]}" value="{%?block[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
																	<a original-title="Edit" class="icon-button tips EditBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?block[name]}" value="{%?block[id]}"><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
															</div>
													</td>
											</tr>
									{%/foreach}
								{%/if}
								{%if isempty|BlockList == true}
										<tr>
											<td colspan="2">
												<div align="center">There are no IP blocks, add one using the + above.</div>
											</td>
										</tr>
								{%/if}
							{%/if}
							{%if isset|BlockList == false}
									<tr>
										<td colspan="2">
											<div align="center">There are no IP blocks, add one using the + above.</div>
										</td>
									</tr>
							{%/if}
						</table>
					</div>
				{%/if}
				{%if isset|BlockList == false}
					<br><br>
					<div align="center">There are currently no pools defined.</div>
				{%/if}
			</div>
		{%/if}
	{%/if}
	{%if isset|Pool == true}
		<br><br>
		<div align="center">
			<div class="simplebox grid740">
				<div class="titleh">
					<h3>{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if}</h3>
					<div class="shortcuts-icons">
						<a class="shortcut tips" title="Add IP Addresses"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
					</div>
				</div>
				<table class="tablesorter">
					<thead>
						<tr>
							<th width="60%"><div align="center">IP Address</div></th>
							<th width="20%"><div align="center">Owner</div></th>
							<th width="20%"><div align="center">Actions</div></th>
						</tr>
					</thead>        
					{%if isset|IPList == true}
						{%if isempty|IPList == false}
							{%foreach ip in IPList}
								<tr>
									<td>{%?ip[ip]}</td>
									<td><div align="center">{%if isset|ip[Owner] == true}{%if isset|ip[OwnerId] == true}<a href="admin.php?view=clients&id={%?ip[OwnerId]}">{%?ip[Owner]}</a>{%/if}{%/if}</div></td>
									<td>
										<div align="center">
											<a original-title="Delete" class="icon-button tips DeleteBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="" value=""><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
											<a original-title="Edit" class="icon-button tips EditBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="" value=""><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
										</div>
									</td>
								</tr>
							{%/foreach}
						{%/if}
						{%if isempty|IPList == true}
								<tr>
									<td colspan="2">
										<div align="center">There are no IPs, add one using the + above.</div>
									</td>
								</tr>
						{%/if}
					{%/if}
					{%if isset|IPList == false}
						<tr>
							<td colspan="2">
								<div align="center">There are no IPs, add one using the + above.</div>
							</td>
						</tr>
					{%/if}
				</table>
			</div>
		</div>
	{%/if}
{%/if}