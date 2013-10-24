<div align="center"><div id="Notice"></div></div>
{%if isempty|Virtualization == true}
	<div align="center">
		<br><br>
		<table style="border:0px solid white;width:40%;height:100px;">
			<tr>
				<td width="50%" align="center">
					<a class="button-blue" href="admin.php?view=templates&type=openvz">OpenVZ Templates</a>
				</td>
				<td width="50%" align="center">
					<a class="button-gray" href="#">Xen Templates</a>
				</td>
			</tr>
			<tr>
				<td width="50%" align="center">
					<a class="button-blue" href="admin.php?view=templates&type=kvm">KVM Templates</a>
				</td>
				<td width="50%" align="center">
					<a class="button-gray" href="#">LXC Templates</a>
				</td>
			</tr>
		</table>
	</div>
{%/if}
<br><br>
{%if isempty|Virtualization == false}
	<div align="center">Please be aware submitting new templates may take up to 2 minutes (download time).</div>
	<br><br>
	{%if Virtualization == openvz}
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#OpenVZTable').dataTable({
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"bSort": false,
					"aaSorting": [[ 0, "asc" ]],
					"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
					"bStateSave": true,
					"oLanguage": {
						"sEmptyTable": "There are no templates, add one using the + above."
					}
				});
				$("#NewOpenVZTemplate").click(function(){
					$("#NewOpenVZForm").modal({containerCss:{width:"400", height:"200"}});
				});
				$('.noEnterSubmit').keypress(function(e){
					if ( e.which == 13 ) e.preventDefault();
				});
				$('#SubmitOpenVZTemplate').click(function() {
					var name = $("#OpenVZTemplateName").val();
					var url = $("#OpenVZTemplateURL").val();
					$('#FormOpenVZTemplate').html('<a class="button-blue" />Please Wait...</a>');
					if(!url){
						$('#FormOpenVZTemplate').html('<a class="button-blue" id="SubmitOpenVZTemplate" />Add OpenVZ Template</a>');
					}
					else {
						$.modal.close();
						$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
						$.getJSON("admin.php?view=templates&type=openvz&action=addtemplate&name=" + name + "&url=" + url,function(result){
							$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
						});
					}
				});
				$(".DeleteOpenVZTemplate").click(function() {
					var templatename = $(this).attr('rel');
					var templateid = $(this).attr('value');
					$("#DeleteFormName").html(templatename);
					$("#DeleteFormValue").html(templateid);
					$("#DeleteForm").modal({containerCss:{width:"400", height:"200"}});
				});
				$("#ConfirmDelete").click(function() {
					var deleteid = $("#DeleteFormValue").text();
					$.modal.close();
					$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
					$.getJSON("admin.php?view=templates&type=openvz&action=removetemplate&id=" + deleteid,function(result){
						$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					});
				});
				$("#CancelDelete").click(function() {
					$.modal.close();
				});
				$(".EditOpenVZTemplate").click(function() {
					var templatename = $(this).attr('rel');
					var templateid = $(this).attr('value');
					$("#EditFormName").val(templatename);
					$("#EditFormValue").html(templateid);
					$("#EditForm").modal({containerCss:{width:"400", height:"200"}});
				});
				$("#SubmitEditOpenVZTemplate").click(function() {
					var editid = $("#EditFormValue").text();
					var name = $("#EditFormName").val();
					$.modal.close();
					$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
					$.getJSON("admin.php?view=templates&type=openvz&action=updatetemplate&id=" + editid + "&name=" + name,function(result){
						$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					});
				});
			});
		</script>
		<div align="center"><div class="simplebox grid740">
				<div class="titleh">
					<h3>OpenVZ Templates</h3>
					<div class="shortcuts-icons">
						<a class="shortcut tips" id="NewOpenVZTemplate" title="Add Template"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
					</div>
				</div>
			<table class="tablesorter" id="OpenVZTable">
				<thead>
					<tr>
						<th width="30%"><div align="center">Name</div></th>
						<th width="30%"><div align="center">File</div></th>
						<th width="20%"><div align="center">Type</div></th>
						<th width="20%"><div align="center">Actions</div></th>
					</tr>
				</thead>	
				{%if isset|TemplateList == true}
					{%foreach template in TemplateList}
						<tr>
							<td>{%?template[name]}</td>
							<td>{%?template[path]}.tar.gz</td>
							<td><div align="center">OpenVZ</div></td>
							<td>
								<div align="center">
									<a original-title="Delete" class="icon-button tips DeleteOpenVZTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
									<a original-title="Edit" class="icon-button tips EditOpenVZTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
								</div>
							</td>
						</tr>
					{%/foreach}
				{%/if}
				{%if isset|TemplateList == false}
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				{%/if}
			</table>
		</div></div>
		<div id="NewOpenVZForm" style="display:none;" align="center">
			<div style="z-index: 610;" class="simplebox">
				<div style="z-index: 600;" class="titleh" align="center"><h3>Add OpenVZ Template</h3></div>
				<div style="z-index: 590;" class="body padding10">
					<div style="height:120px;">
						<form id="form1" name="form1" class="SubmitOpenVZTemplate noEnterSubmit">
							Template Name: <input name="openvztemplatename" class="st-forminput" id="OpenVZTemplateName" style="width:150px" type="text"><br>
							Download URL: &nbsp;<input name="openvztemplateurl" class="st-forminput" id="OpenVZTemplateURL" style="width:150px" type="text">
							<div style="padding:12px;"></div>
							<div align="center" style="margin-bottom:5px;" id="FormOpenVZTemplate"><a class="button-blue" style="cursor:pointer;" id="SubmitOpenVZTemplate">Add OpenVZ Template</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="DeleteForm" style="display:none;height:130px;" align="center">
			<div style="z-index: 610;" class="simplebox">
				<div style="z-index: 600;" class="titleh" align="center"><h3>Delete Template</h3></div>
				<div style="z-index: 590;" class="body padding10">
					<div style="height:120px;">
						<form id="form3" name="form3" class="Delete noEnterSubmit">
							Do you want to delete the template <a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a>?
							<div style="padding:12px;"></div>
							<div align="center" style="margin-bottom:5px;" id="FormDelete"><a class="button-blue" style="cursor:pointer;" id="ConfirmDelete">Yes</a> <a class="button-blue" style="cursor:pointer;" id="CancelDelete">No</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="EditForm" style="display:none;" align="center">
			<div style="z-index: 610;" class="simplebox">
				<div style="z-index: 600;" class="titleh" align="center"><h3>Edit Template</h3></div>
				<div style="z-index: 590;" class="body padding10">
					<div style="height:120px;">
						<form id="form1" name="form1" class="SubmitOpenVZTemplate noEnterSubmit">
							Template Name: <input name="openvztemplatename" class="st-forminput" id="EditFormName" value="" style="width:150px" type="text"><a id="EditFormValue" style="display:none;"></a><br>
							<div style="padding:12px;"></div>
							<div align="center" style="margin-bottom:5px;" id="FormEditOpenVZTemplate"><a class="button-blue" style="cursor:pointer;" id="SubmitEditOpenVZTemplate">Update Template</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	{%/if}
	{%if Virtualization == kvm}
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#KVMTable').dataTable({
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"bSort": false,
					"aaSorting": [[ 0, "asc" ]],
					"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
					"bStateSave": true,
					"oLanguage": {
						"sEmptyTable": "There are no templates, add one using the + above."
					}
				});
				$("#NewKVMTemplate").click(function(){
					$("#NewKVMForm").modal({containerCss:{width:"400", height:"200"}});
				});
				$('.noEnterSubmit').keypress(function(e){
					if ( e.which == 13 ) e.preventDefault();
				});
				$('#SubmitKVMTemplate').click(function() {
					var name = $("#KVMTemplateName").val();
					var url = $("#KVMTemplateURL").val();
					$('#FormKVMTemplate').html('<a class="button-blue" />Please Wait...</a>');
					if(!url){
						$('#FormKVMTemplate').html('<a class="button-blue" id="SubmitKVMTemplate" />Add KVM Template</a>');
					}
					else {
						$.modal.close();
						$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
						$.getJSON("admin.php?view=templates&type=kvm&action=addtemplate&name=" + name + "&url=" + url,function(result){
							$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
						});
					}
				});
				$(".DeleteKVMTemplate").click(function() {
					var templatename = $(this).attr('rel');
					var templateid = $(this).attr('value');
					$("#DeleteFormName").html(templatename);
					$("#DeleteFormValue").html(templateid);
					$("#DeleteForm").modal({containerCss:{width:"400", height:"200"}});
				});
				$("#ConfirmDelete").click(function() {
					var deleteid = $("#DeleteFormValue").text();
					$.modal.close();
					$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
					$.getJSON("admin.php?view=templates&type=kvm&action=removetemplate&id=" + deleteid,function(result){
						$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					});
				});
				$("#CancelDelete").click(function() {
					$.modal.close();
				});
				$(".EditKVMTemplate").click(function() {
					var templatename = $(this).attr('rel');
					var templateid = $(this).attr('value');
					$("#EditFormName").val(templatename);
					$("#EditFormValue").html(templateid);
					$("#EditForm").modal({containerCss:{width:"400", height:"200"}});
				});
				$("#SubmitEditKVMTemplate").click(function() {
					var editid = $("#EditFormValue").text();
					var name = $("#EditFormName").val();
					$.modal.close();
					$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
					$.getJSON("admin.php?view=templates&type=kvm&action=updatetemplate&id=" + editid + "&name=" + name,function(result){
						$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					});
				});
			});
		</script>
		<div align="center"><div class="simplebox grid740">
				<div class="titleh">
					<h3>KVM Templates</h3>
					<div class="shortcuts-icons">
						<a class="shortcut tips" id="NewKVMTemplate" title="Add Template"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
					</div>
				</div>
			<table class="tablesorter" id="KVMTable">
				<thead>
					<tr>
						<th width="30%"><div align="center">Name</div></th>
						<th width="30%"><div align="center">File</div></th>
						<th width="20%"><div align="center">Type</div></th>
						<th width="20%"><div align="center">Actions</div></th>
					</tr>
				</thead>	
				{%if isset|TemplateList == true}
					{%foreach template in TemplateList}
						<tr>
							<td>{%?template[name]}</td>
							<td>{%?template[path]}.iso</td>
							<td><div align="center">KVM</div></td>
							<td>
								<div align="center">
									<a original-title="Delete" class="icon-button tips DeleteKVMTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><img src="./templates/default/img/icons/32x32/stop32.png" alt="icon" height="16" width="16"></a>
									<a original-title="Edit" class="icon-button tips EditKVMTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><img src="./templates/default/img/icons/32x32/paperpencil32.png" alt="icon" height="16" width="16"></a>
								</div>
							</td>
						</tr>
					{%/foreach}
				{%/if}
				{%if isset|TemplateList == false}
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				{%/if}
			</table>
		</div></div>
		<div id="NewKVMForm" style="display:none;" align="center">
			<div style="z-index: 610;" class="simplebox">
				<div style="z-index: 600;" class="titleh" align="center"><h3>Add KVM Template</h3></div>
				<div style="z-index: 590;" class="body padding10">
					<div style="height:120px;">
						<form id="form1" name="form1" class="SubmitKVMTemplate noEnterSubmit">
							Template Name: <input name="kvmtemplatename" class="st-forminput" id="KVMTemplateName" style="width:150px" type="text"><br>
							Download URL: &nbsp;<input name="kvmtemplateurl" class="st-forminput" id="KVMTemplateURL" style="width:150px" type="text">
							<div style="padding:12px;"></div>
							<div align="center" style="margin-bottom:5px;" id="FormKVMTemplate"><a class="button-blue" style="cursor:pointer;" id="SubmitKVMTemplate">Add KVM Template</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="DeleteForm" style="display:none;height:130px;" align="center">
			<div style="z-index: 610;" class="simplebox">
				<div style="z-index: 600;" class="titleh" align="center"><h3>Delete Template</h3></div>
				<div style="z-index: 590;" class="body padding10">
					<div style="height:120px;">
						<form id="form3" name="form3" class="Delete noEnterSubmit">
							Do you want to delete the template <a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a>?
							<div style="padding:12px;"></div>
							<div align="center" style="margin-bottom:5px;" id="FormDelete"><a class="button-blue" style="cursor:pointer;" id="ConfirmDelete">Yes</a> <a class="button-blue" style="cursor:pointer;" id="CancelDelete">No</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="EditForm" style="display:none;" align="center">
			<div style="z-index: 610;" class="simplebox">
				<div style="z-index: 600;" class="titleh" align="center"><h3>Edit Template</h3></div>
				<div style="z-index: 590;" class="body padding10">
					<div style="height:120px;">
						<form id="form1" name="form1" class="SubmitKVMTemplate noEnterSubmit">
							Template Name: <input name="kvmtemplatename" class="st-forminput" id="EditFormName" value="" style="width:150px" type="text"><a id="EditFormValue" style="display:none;"></a><br>
							<div style="padding:12px;"></div>
							<div align="center" style="margin-bottom:5px;" id="FormEditKVMTemplate"><a class="button-blue" style="cursor:pointer;" id="SubmitEditKVMTemplate">Update Template</a></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	{%/if}
{%/if}