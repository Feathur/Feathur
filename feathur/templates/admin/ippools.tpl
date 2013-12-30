<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		oTablea = $('#ListTable').dataTable({
			"bJQueryUI": true,
			"bPaginate": false,
			"aaSorting": [[ 0, "asc" ]],
			"bSort": false,
			"iDisplayLength": -1,
			"bStateSave": true,
			"oLanguage": {
			"sEmptyTable": "No Entries"
			}
		});
		$('.noEnterSubmit').keypress(function(e){
			if ( e.which == 13 ) e.preventDefault();
		});
	});
	$(function() {
	   $( "#tabs" ).tabs();
	});
</script>
<!--- If no type is set, ask admin what type they want. --->
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

<div id="result"></div>

<!--- If the Type isset, and the pool is not set display some blocks. --->
{%if isset|Type == true}
	{%if isset|Pool == false}
		
		<!--- If the Type is empty then the blocks are IPv4 --->
		{%if isempty|Type == true}
			<script type="text/javascript">
				$(document).ready(function() {
					$("#AddBlock").click(function(){
						$("#NewBlockForm").modal({containerCss:{width:"400", height:"250"}});
					});
					$('#SubmitNewBlock').click(function() {
						var name = $("#NewBlockName").val();
						var gateway = $("#NewBlockGateway").val();
						var netmask = $("#NewBlockNetmask").val()
						$('#SubmitNewBlockWrapper').html('<a class="button-blue" />Please Wait...</a>');
						if(!name){
							$('#SubmitNewBlockWrapper').html('<a class="button-blue" id="SubmitNewBlock" />Add IP Block</a>');
						}
						else {
							$.modal.close();
							$("#LoadingImage").css({visibility: "visible"});
							$.getJSON("admin.php?view=ippools&type=0&action=create_pool&name=" + name + "&gateway=" + gateway + "&netmask=" + netmask,function(result){
								if(typeof(result.red) != "undefined" && result.red !== null) {
									$("#result").html(result.red);
								} else {
									$("#result").html(result.content);
									window.location.reload();
								}
							});
						}
					});
				});
			</script>
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
						<table class="tablesorter" {%if isset|BlockList == true}{%if isempty|BlockList == false}id="ListTable"{%/if}{%/if}>
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
			<div id="NewBlockForm" style="display:none;" align="center">
				<div style="z-index: 610;" class="simplebox">
					<div style="z-index: 600;" class="titleh" align="center"><h3>Add IPv4 Block</h3></div>
					<div style="z-index: 590;" class="body padding10">
						<div style="height:170px;">
							<form id="form1" name="form1" class="SubmitBlockForm noEnterSubmit">
									Block Name: <input name="newblockname" class="st-forminput" id="NewBlockName" style="width:150px" type="text"><br>
									Gateway: &nbsp;<input name="newblockgateway" class="st-forminput" id="NewBlockGateway" style="width:150px" type="text"><br>
									Netmask: &nbsp;<input name="newblocknetmask" class="st-forminput" id="NewBlockNetmask" style="width:150px" type="text">
									<div style="padding:12px;"></div>
									<div align="center" style="margin-bottom:5px;" id="SubmitNewBlockWrapper"><a class="button-blue" style="cursor:pointer;" id="SubmitNewBlock">Add IP Block</a></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		{%/if}
		
		<!--- If the Type is not empty then the blocks are IPv6 --->
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
						<table class="tablesorter"  {%if isset|BlockList == true}{%if isempty|BlockList == false}id="ListTable"{%/if}{%/if}>
							<thead>
								<tr>
									<th width="60%"><div align="center">Name</div></th>
									<th width="40%"><div align="center">Actions</div></th>
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
	
	<!--- If the Pool isset then display the pool settings --->
	{%if isset|Pool == true}
	
		<!--- If the Pool type is empty then this block is IPv4 --->
		{%if isempty|Type == true}
			<br><br>
			<div align="center">
				<div class="grid740">
					<div id="tabs">
						<div id="tabs">
							<ul>
								<li><a href="#tabs-1">IP Addresses</a></li>
								<li><a href="#tabs-2">Servers</a></li>
							</ul>
						</div>
						<div id="tabs-1">
							<div align="center">
								<div class="simplebox" style="width:95%">
									<div class="titleh">
										<h3>{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if} IP Management</h3>
										<div class="shortcuts-icons">
											<a class="shortcut tips" title="Add IP Addresses"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
										</div>
									</div>
									<table class="tablesorter" {%if isset|IPList == true}{%if isempty|IPList == false}id="ListTable"{%/if}{%/if}>
										<thead>
											<tr>
												<th width="40%"><div align="center">IP Address</div></th>
												<th width="30%"><div align="center">Owner</div></th>
												<th width="30%"><div align="center">Actions</div></th>
											</tr>
										</thead>        
										{%if isset|IPList == true}
											{%if isempty|IPList == false}
												{%foreach ip in IPList}
													<tr>
														<td>{%?ip[ip]}</td>
														<td>
															<div align="center">
																{%if isempty|ip[Owner] == false}
																	{%if isempty|ip[OwnerId] == false}
																		<a href="admin.php?view=clients&id={%?ip[OwnerId]}">{%?ip[Owner]}</a>
																	{%/if}
																{%/if}
															</div>
														</td>
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
													<td colspan="3">
														<div align="center">There are no IPs, add one using the + above.</div>
													</td>
												</tr>
											{%/if}
										{%/if}
										{%if isset|IPList == false}
											<tr>
												<td colspan="3">
													<div align="center">There are no IPs, add one using the + above.</div>
												</td>
											</tr>
										{%/if}
									</table>
								</div>
							</div>
						</div>
						<div id="tabs-2">
							<div align="center">
								<div class="simplebox" style="width:95%">
									<div class="titleh">
										<h3>{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if} Server Management</h3>
										<div class="shortcuts-icons">
											<a class="shortcut tips" title="Add Server"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
										</div>
									</div>
									<table class="tablesorter">
										<thead>
											<tr>
												<th width="60%"><div align="center">Server</div></th>
												<th width="20%"><div align="center">Actions</div></th>
											</tr>
										</thead>        
										{%if isset|ServerList == true}
											{%if isempty|ServerList == false}
												{%foreach server in ServerList}
													<tr>
														<td>{%?server[name]}</td>
														<td>
															<div align="center">
																<a original-title="Delete" class="icon-button tips DeleteBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="" value=""><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
																<a original-title="Edit" class="icon-button tips EditBlock" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="" value=""><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
															</div>
														</td>
													</tr>
												{%/foreach}
											{%/if}
											{%if isempty|ServerList == true}
												<tr>
													<td colspan="2">
														<div align="center">There are no servers assigned to this block, add one using the + above. (1)</div>
													</td>
												</tr>
											{%/if}
										{%/if}
										{%if isset|ServerList == false}
											<tr>
												<td colspan="2">
													<div align="center">There are no servers assigned to this block, add one using the + above. (2)</div>
												</td>
											</tr>
										{%/if}
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{%/if}
		
		<!--- If the Type isset then this pool is IPv6 --->
		{%if isempty|Type == false}
			<div align="center">IPv6 Pool here</div>
		{%/if}
	{%/if}
{%/if}