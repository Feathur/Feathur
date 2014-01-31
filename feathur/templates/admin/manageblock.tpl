<div id="LoadingImage" align="right" style="padding-right:10px;margin-top:10px;visibility:hidden;"><img src="./templates/default/img/loading/9.gif"></img></div>
{%if isset|Errors == true}
	{%foreach error in Errors}
		<div align="center">
        	<div class="albox errorbox" style="width:60%;">
				<b>Error :</b> {%?error[red]}
				<a href="#" class="close tips" title="close">close</a>
			</div>
		</div>
	{%/foreach}
{%/if}
<script type="text/javascript">
	$(document).ready(function() {
		oTable = $('#IPAddresses').dataTable({
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
		oTable = $('#Servers').dataTable({
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
		$(function() {
			$( "#tabs" ).tabs();
		});
		$('.noEnterSubmit').keypress(function(e){
			if ( e.which == 13 ) e.preventDefault();
		});
		$("#AddIP").click(function(){
			$("#NewIPForm").modal({containerCss:{width:"400", height:"360"}});
		});
		$('#SubmitNewIP').click(function() {
			var ip = $("#SingleIPAdd").val();
			$('#SubmitNewIPWrapper').html('<a class="button-blue" />Please Wait...</a>');
			if(!ip){
				$('#SubmitNewIPWrapper').html('<a class="button-blue" id="SubmitNewIP" />Add Single IP</a>');
			}
			else {
				$.modal.close();
				$("#LoadingImage").css({visibility: "visible"});
				$.getJSON("admin.php?view=ippools&action=addip&ip=" + ip + "&block={%?BlockId}",function(result){
					$("#page").html(result.content);
					$("#LoadingImage").css({visibility: "hidden"});
				});
			}
		});
		$('#SubmitNewRange').click(function() {
			var start = $("#StartIPAdd").val();
			var end = $("#EndIPAdd").val();
			$('#SubmitNewRangeWrapper').html('<a class="button-blue" />Please Wait...</a>');
			if((!start) || (!end)){
				$('#SubmitNewRangeWrapper').html('<a class="button-blue" id="SubmitNewRange" />Add Range of IPs</a>');
			}
			else {
				$.modal.close();
				$("#LoadingImage").css({visibility: "visible"});
				$.getJSON("admin.php?view=ippools&action=addip&start=" + start + "&end=" + end + "&block={%?BlockId}",function(result){
					$("#page").html(result.content);
					$("#LoadingImage").css({visibility: "hidden"});
				});
			}
		});
		$(".DeleteIP").click(function() {
			var ip = $(this).attr('rel');
			var id = $(this).attr('value');
			$("#DeleteFormName").html(ip);
			$("#DeleteFormValue").html(id);
			$("#DeleteFormText").html("Do you really want to remove the IP: ");
			$("#DeleteFormType").html("removeip");
			$("#DeleteForm").modal({containerCss:{width:"400", height:"200"}});
		});
		$("#AddServer").click(function(){
			$("#NewServerForm").modal({containerCss:{width:"400", height:"200"}});
		});
		$('#SubmitServer').click(function() {
			var id = $("#SelectedServer").val();
			$('#SubmitNewServer').html('<a class="button-blue" />Please Wait...</a>');
			if(!id){
				$('#SubmitNewServer').html('<a class="button-blue" id="SubmitServer" />Add Server To Block</a>');
			}
			else {
				$.modal.close();
				$("#LoadingImage").css({visibility: "visible"});
				$.getJSON("admin.php?view=ippools&action=addserver&id=" + id + "&block={%?BlockId}",function(result){
					$("#page").html(result.content);
					$("#LoadingImage").css({visibility: "hidden"});
				});
			}
		});
		$(".DeleteServer").click(function() {
			var name = $(this).attr('rel');
			var id = $(this).attr('value');
			$("#DeleteFormName").html(name);
			$("#DeleteFormValue").html(id);
			$("#DeleteFormText").html("Remove the following server from this block: ");
			$("#DeleteFormType").html("removeserver");
			$("#DeleteForm").modal({containerCss:{width:"400", height:"200"}});
		});
		$("#ConfirmDelete").click(function() {
			var id = $("#DeleteFormValue").text();
			var type = $("#DeleteFormType").text();
			$.modal.close();
			$("#LoadingImage").css({visibility: "visible"});
			$.getJSON("admin.php?view=ippools&action=" + type + "&id=" + id + "&block={%?BlockId}",function(result){
				$("#page").html(result.content);
				$("#LoadingImage").css({visibility: "hidden"});
			});
		});
		$("#CancelDelete").click(function() {
			$.modal.close();
		});
	});
</script>
<div align="center">
	<div id="tabs" style="width:95%">
		<ul>
			<li><a href="#tabs-1">IP Addresses</a></li>
			<li><a href="#tabs-2">Servers</a></li>
		</ul>
		<div id="tabs-1">
			<div align="center"><div class="simplebox grid740" style="width:99%">
				<div class="titleh">
					<h3>IP Addresses</h3>
					<div class="shortcuts-icons">
						<a class="shortcut tips" id="AddIP" title="Add IP Address(es)"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
					</div>
				</div>
				<table class="tablesorter" id="IPAddresses">
					<thead>
						<tr>
							<th width="60%"><div align="center">IP Address</div></th>
							<th width="20%"><div align="center">Owner</div></th>
							<th width="20%"><div align="center">Actions</div></th>
						</tr>
					</thead>	
					{%if isset|IPList == true}
						{%foreach ip in IPList}
							<tr>
								<td>{%?ip[ip]}</td>
								<td>{%if isempty|ip[OwnerId] == false}<div align="center"><a href="admin.php?view=clients&id={%?ip[OwnerId]}">{%?ip[Owner]}</a></div>{%/if}</td>
								<td>
									<div align="center">
										<a original-title="Delete" class="icon-button tips DeleteIP" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?ip[ip]}" value="{%?ip[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
									</div>
								</td>
							</tr>
						{%/foreach}
					{%/if}
					{%if isset|IPList == false}
						<tr>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					{%/if}
				</table>
			</div></div>
		</div>
		<div id="tabs-2">
			<div align="center"><div class="simplebox grid740" style="width:99%">
				<div class="titleh">
					<h3>IP Block Servers</h3>
					<div class="shortcuts-icons">
						<a class="shortcut tips" id="AddServer" title="Add Server to Block"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
					</div>
				</div>
				<table class="tablesorter" id="Servers">
					<thead>
						<tr>
							<th width="90%"><div align="center">Server Name</div></th>
							<th width="10%"><div align="center">Action</div></th>
						</tr>
					</thead>	
					{%if isset|ServerList == true}
						{%foreach server in ServerList}
							<tr>
								<td>{%?server[name]}</td>
								<td>
									<div align="center">
										<a original-title="Delete" class="icon-button tips DeleteServer" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?server[name]}" value="{%?server[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
									</div>
								</td>
							</tr>
						{%/foreach}
					{%/if}
					{%if isset|ServerList == false}
						<tr>
							<td></td>
							<td></td>
						</tr>
					{%/if}
				</table>
			</div></div>
		</div>
	</div>
</div>
<div id="NewIPForm" style="display:none;" align="center">
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Add A Single IP</h3></div>
		<div style="z-index: 590;" class="body padding10">
			<div style="height:90px;">
				<form id="form1" name="form1" class="SubmitBlockForm noEnterSubmit">
					IP Address: <input name="singleip" class="st-forminput" id="SingleIPAdd" style="width:150px" type="text"><br>
					<div style="padding:12px;"></div>
					<div align="center" style="margin-bottom:5px;" id="SubmitNewIPWrapper"><a class="button-blue" style="cursor:pointer;" id="SubmitNewIP">Add Single IP</a></div>
				</form>
			</div>
		</div>
	</div>
	<br>
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Add A Range of IPs</h3></div>
		<div style="z-index: 590;" class="body padding10">
			<div style="height:120px;">
				<form id="form1" name="form1" class="SubmitBlockForm noEnterSubmit">
					Start IP: &nbsp;<input name="startip" class="st-forminput" id="StartIPAdd" style="width:150px" type="text"><br>
					End IP: &nbsp;&nbsp;<input name="endip" class="st-forminput" id="EndIPAdd" style="width:150px" type="text">
					<div style="padding:12px;"></div>
					<div align="center" style="margin-bottom:5px;" id="SubmitNewRangeWrapper"><a class="button-blue" style="cursor:pointer;" id="SubmitNewRange">Add Range of IPs</a></div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="DeleteForm" style="display:none;height:130px;" align="center">
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Delete</h3></div>
		<div style="z-index: 590;" class="body padding10">
			<div style="height:120px;">
				<form id="form3" name="form3" class="Delete noEnterSubmit">
					<a style="color:#737F89;" id="DeleteFormText"></a><a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a><a id="DeleteFormType" style="display:none;"></a>?
					<div style="padding:12px;"></div>
					<div align="center" style="margin-bottom:5px;" id="FormDelete"><a class="button-blue" style="cursor:pointer;" id="ConfirmDelete">Yes</a> <a class="button-blue" style="cursor:pointer;" id="CancelDelete">No</a></div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="NewServerForm" style="display:none;" align="center">
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Add A Server to A Block</h3></div>
		<div style="z-index: 590;" class="body padding10">
			<div style="height:90px;">
				<form id="form1" name="form1" class="SubmitBlockForm noEnterSubmit">
					Server to Add: <select id="SelectedServer" name="SelectedServer">
						{%if isset|AvailableServers == true}
							{%foreach Server in AvailableServers}
								<option value="{%?Server[id]}">{%?Server[name]}</option>
							{%/foreach}
						{%/if}
						{%if isset|AvailableServers == false}
							<option>No Servers Available</option>
						{%/if}
					</select>
					<div style="padding:12px;"></div>
					<div align="center" style="margin-bottom:5px;" id="SubmitNewServer"><a class="button-blue" style="cursor:pointer;" id="SubmitServer">Add Server to Block</a></div>
				</form>
			</div>
		</div>
	</div>
</div>