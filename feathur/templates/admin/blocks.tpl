<div id="LoadingImage" align="right" style="padding-right:10px;margin-top:10px;visibility:hidden;"><img src="./templates/default/img/loading/9.gif"></img></div>
<br><br>
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
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		oTable = $('#BlockListTable').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort": false,
			"aaSorting": [[ 0, "asc" ]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"bStateSave": true,
			"oLanguage": {
				"sEmptyTable": "There are no IP blocks, add one using the + above."
			}
		});
		$("#AddBlock").click(function(){
			$("#NewBlockForm").modal({containerCss:{width:"400", height:"250"}});
		});
		$('.noEnterSubmit').keypress(function(e){
			if ( e.which == 13 ) e.preventDefault();
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
				$.getJSON("admin.php?view=ippools&action=addblock&name=" + name + "&gateway=" + gateway + "&netmask=" + netmask,function(result){
					$("#page").html(result.content);
					$("#LoadingImage").css({visibility: "hidden"});
				});
			}
		});
		$(".DeleteBlock").click(function() {
			var blockname = $(this).attr('rel');
			var blockid = $(this).attr('value');
			$("#DeleteFormName").html(blockname);
			$("#DeleteFormValue").html(blockid);
			$("#DeleteForm").modal({containerCss:{width:"400", height:"200"}});
		});
		$("#ConfirmDelete").click(function() {
			var deleteid = $("#DeleteFormValue").text();
			$.modal.close();
			$("#LoadingImage").css({visibility: "visible"});
			$.getJSON("admin.php?view=ippools&action=removeblock&id=" + deleteid,function(result){
				$("#page").html(result.content);
				$("#LoadingImage").css({visibility: "hidden"});
			});
		});
		$("#CancelDelete").click(function() {
			$.modal.close();
		});
		$(".EditBlock").click(function() {
			var blockname = $(this).attr('rel');
			var blockid = $(this).attr('value');
			$("#EditFormName").val(blockname);
			$("#EditFormValue").html(blockid);
			$("#EditForm").modal({containerCss:{width:"400", height:"200"}});
		});
		$("#SubmitEditBlock").click(function() {
			var editid = $("#EditFormValue").text();
			var name = $("#EditFormName").val();
			$.modal.close();
			$("#LoadingImage").css({visibility: "visible"});
			$.getJSON("admin.php?view=ippools&action=updateblock&id=" + editid + "&name=" + name,function(result){
				$("#page").html(result.content);
				$("#LoadingImage").css({visibility: "hidden"});
			});
		});
		$(".ManageBlock").click(function() {
			var blockid = $(this).attr('value');
			$("#LoadingImage").css({visibility: "visible"});
			$.getJSON("admin.php?view=ippools&block=" + blockid,function(result){
				$("#page").html(result.content);
				$("#LoadingImage").css({visibility: "hidden"});
			});
		});
	});
</script>
<div align="center"><div class="simplebox grid740">
	<div class="titleh">
		<h3>IP Address Blocks</h3>
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
</div></div>
<div id="NewBlockForm" style="display:none;" align="center">
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Add IP Block</h3></div>
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
<div id="DeleteForm" style="display:none;height:130px;" align="center">
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Delete IP Block</h3></div>
		<div style="z-index: 590;" class="body padding10">
			<div style="height:120px;">
				<form id="form3" name="form3" class="Delete noEnterSubmit">
					Do you want to delete the IP block <a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a>?
					<div style="padding:12px;"></div>
					<div align="center" style="margin-bottom:5px;" id="FormDelete"><a class="button-blue" style="cursor:pointer;" id="ConfirmDelete">Yes</a> <a class="button-blue" style="cursor:pointer;" id="CancelDelete">No</a></div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="EditForm" style="display:none;" align="center">
	<div style="z-index: 610;" class="simplebox">
		<div style="z-index: 600;" class="titleh" align="center"><h3>Edit IP Block</h3></div>
		<div style="z-index: 590;" class="body padding10">
			<div style="height:120px;">
				<form id="form1" name="form1" class="SubmitEditBlock noEnterSubmit">
					Block Name: <input name="ipblockname" class="st-forminput" id="EditFormName" value="" style="width:150px" type="text"><a id="EditFormValue" style="display:none;"></a><br>
					<div style="padding:12px;"></div>
					<div align="center" style="margin-bottom:5px;" id="FormEditBlock"><a class="button-blue" style="cursor:pointer;" id="SubmitEditBlock">Update IP Block</a></div>
				</form>
			</div>
		</div>
	</div>
</div>