<div id="Notice" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2 nofluid"></div>

{%if isempty|Virtualization == true}
    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
        <div class="outlined whitebox">
            <h3 class="title">Templates</h3>
            <div align="center" class="pure-g">
                <br>
                <div class="pure-u-sm-1 pure-u-lg-1-2 pure-u-xl-1-2">
                    <a class="pure-button pure-button-primary button-blue button-xlarge" href="admin.php?view=templates&type=openvz" style="width:90%;">OpenVZ Templates</a>
                </div>
                <div class="pure-u-sm-1 pure-u-lg-1-2 pure-u-xl-1-2">
                    <a class="pure-button pure-button-primary button-gray button-xlarge disabled" href="#" style="width:90%;">Xen Templates</a>
                </div>
                <div class="pure-u-sm-1 pure-u-lg-1-2 pure-u-xl-1-2">
                    <a class="pure-button pure-button-primary button-blue button-xlarge" href="admin.php?view=templates&type=kvm" style="width:90%;">KVM Templates</a>
                </div>
                <div class="pure-u-sm-1 pure-u-lg-1-2 pure-u-xl-1-2">
                    <a class="pure-button pure-button-primary button-gray button-xlarge disabled" href="#" style="width:90%;">LXC Templates</a>
                </div>
            </div>
        </div>
    </div>
{%/if}

{%if isempty|Virtualization == false}
    <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $("#OpenVZTable,#KVMTable").dataTable({
            "dom": '<"table-top"lf>rt<"table-bottom"ip>',
            "pagingType": "full_numbers",
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "DisplayLength": 10,
            "stateSave": true,
            "language": {
                "emptyTable": "There are no templates; Add one using the '+' above.",
                "paginate": {
                    "previous": "‹",
                    "next": "›",
                    "last": "»",
                    "first": "«",
                }
            }
        });
    });
    </script>
	{%if Virtualization == openvz}
		<script type="text/javascript" charset="utf-8">
            $(document).ready(function() {
				$("#NewOpenVZTemplate").click(function(){
					$("#NewOpenVZForm").modal();
				});
				$('.noEnterSubmit').keypress(function(e){
					if ( e.which == 13 ) e.preventDefault();
				});
				$('#SubmitOpenVZTemplate').click(function() {
					var name = $("#OpenVZTemplateName").val();
					var url = $("#OpenVZTemplateURL").val();
					$('#FormOpenVZTemplate').html('<a href="#" class="pure-button pure-button-primary button-grey" id="SubmitOpenVZTemplate" disabled>Please Wait...</a>');
					if(!url){
						$('#FormOpenVZTemplate').html('<a href="#" class="pure-button pure-button-primary button-green" id="SubmitOpenVZTemplate">Add OpenVZ Template</a>');
					}
					else {
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=templates&type=openvz&action=addtemplate&name=" + name + "&url=" + url,function(result){
                            loading(0);
							$('#Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
							if(result.reload == 1){
								location.reload();
							}
						});
					}
				});
				$(".DeleteOpenVZTemplate").click(function() {
					var templatename = $(this).attr('rel');
					var templateid = $(this).attr('value');
					$("#DeleteFormName").html(templatename);
					$("#DeleteFormValue").html(templateid);
					$("#DeleteForm").modal();
				});
				$("#ConfirmDelete").click(function() {
					var deleteid = $("#DeleteFormValue").text();
					$.modal.close();
					loading(1);
					$.getJSON("admin.php?view=templates&type=openvz&action=removetemplate&id=" + deleteid,function(result){
                        loading(0);
						$('#Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
						if(result.reload == 1){
							location.reload();
						}
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
					$("#EditForm").modal();
				});
				$("#SubmitEditOpenVZTemplate").click(function() {
					var editid = $("#EditFormValue").text();
					var name = $("#EditFormName").val();
					$.modal.close();
					loading(1);
					$.getJSON("admin.php?view=templates&type=openvz&action=updatetemplate&id=" + editid + "&name=" + name,function(result){
                        loading(0);
                        $("td.template-"+editid).html(name);
                        $(".icon-button.EditOpenVZTemplate").attr("rel",name);
						$('#Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
						if(result.reload == 1){
							location.reload();
						}
					});
				});
			});
		</script>
        <br>
		<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
            <h3 class="title inlineB">OpenVZ Templates</h3>
            <div class="shortcuts-icons inlineB">
                <a class="shortcut tips" id="NewOpenVZTemplate" title="Add Template"><i class="fa fa-plus-circle"></i></a>
            </div>
			<table id="OpenVZTable">
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
							<td class="template-{%?template[id]}">{%?template[name]}</td>
							<td>{%?template[path]}</td>
							<td><div align="center">OpenVZ</div></td>
							<td>
								<div align="center">
									<a alt="Delete Template" class="icon-button DeleteOpenVZTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><i class="fa fa-times-circle"></i></a>
									<a alt="Edit Template" class="icon-button EditOpenVZTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><i class="fa fa-edit"></i></a>
								</div>
							</td>
						</tr>
					{%/foreach}
				{%/if}
				{%if isset|TemplateList == false}
					<tr>
                        <td style="display:none;"></td>
                        <td style="display:none;"></td>
                        <td style="display:none;"></td>
                        <td colspan="4"><i>No OpenVZ templates found; Try adding one using the '+' button above.</i></td><!-- Last one for css reasons -->
					</tr>
				{%/if}
			</table>
		</div>
		<div id="NewOpenVZForm" style="display:none;" align="center">
            <h3 class="title" align="center">Add OpenVZ Template</h3>
            <form id="form1" name="form1" class="SubmitOpenVZTemplate noEnterSubmit pure-form pure-form-aligned">
                <div class="pure-control-group">
                    <label for="OpenVZTemplateName">Template Name:</label>
                    <input name="openvztemplatename" id="OpenVZTemplateName" type="text" required>
                </div>
                <div class="pure-control-group">
                    <label for="OpenVZTemplateName">Download URL:</label>
                    <input name="openvztemplateurl" id="OpenVZTemplateURL" type="text" required>
                </div>
                <br>
                <div align="center" class="formnote">Please be aware submitting new templates may take up to 2 minutes (download time).</div>
                <br>
                <div align="center" id="FormOpenVZTemplate">
                    <a href="#" class="pure-button pure-button-primary button-green" id="SubmitOpenVZTemplate">Add OpenVZ Template</a>
                </div>
            </form>
		</div>
		<div id="DeleteForm" style="display:none;" align="center">
            <h3 class="title" align="center">Delete Template</h3>
            <form id="form3" name="form3" class="Delete noEnterSubmit">
                <div class="formnote"><a style="color:#737F89;" id="DeleteFormText"></a><a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a><a id="DeleteFormType" style="display:none;"></a>?</div><br>
                <div align="center" id="FormDelete" class="pure-g">
                    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                        <a href="#" class="pure-button pure-button-primary button-red button-xlarge" id="ConfirmDelete" style="width:90%;">Yes</a>
                    </div>
                    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                        <a href="#" class="pure-button pure-button-primary button-blue button-xlarge" id="CancelDelete" style="width:90%;">No</a>
                    </div>
                </div>
            </form>
		</div>
		<div id="EditForm" style="display:none;" align="center">
            <h3 class="title" align="center">Edit Template</h3>
            <form id="form1" name="form1" class="SubmitOpenVZTemplate noEnterSubmit pure-form pure-form-aligned">
                <div class="pure-control-group">
                    <label for="#EditFormName">Template Name:</label>
                    <input name="openvztemplatename" id="EditFormName" value="" type="text"><a id="EditFormValue" style="display:none;"></a>
                </div>
                <br>
                <div align="center" id="FormEditOpenVZTemplate">
                    <a class="pure-button pure-button-primary button-blue" id="SubmitEditOpenVZTemplate">Update Template</a>
                </div>
            </form>
		</div>
	{%/if}
	{%if Virtualization == kvm}
		<script type="text/javascript" charset="utf-8">
            $(document).ready(function() {
				$("#NewKVMTemplate").click(function(){
					$("#NewKVMForm").modal();
				});
				$('.noEnterSubmit').keypress(function(e){
					if ( e.which == 13 ) e.preventDefault();
				});
				$('#SubmitKVMTemplate').click(function() {
					var name = $("#KVMTemplateName").val();
					var url = $("#KVMTemplateURL").val();
					$('#FormKVMTemplate a').html('Please Wait...');
					if(!url){
						$('#FormKVMTemplate a').html('Add KVM Template');
					}
					else {
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=templates&type=kvm&action=addtemplate&name=" + name + "&url=" + url,function(result){
                            loading(0);
							$('#Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
							if(result.reload == 1){
								location.reload();
							}
						});
					}
				});
				$(".DeleteKVMTemplate").click(function() {
					var templatename = $(this).attr('rel');
					var templateid = $(this).attr('value');
					$("#DeleteFormName").html(templatename);
					$("#DeleteFormValue").html(templateid);
					$("#DeleteForm").modal();
				});
				$("#ConfirmDelete").click(function() {
					var deleteid = $("#DeleteFormValue").text();
					$.modal.close();
					loading(1);
					$.getJSON("admin.php?view=templates&type=kvm&action=removetemplate&id=" + deleteid,function(result){
                        loading(0);
						$('#Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
						if(result.reload == 1){
							location.reload();
						}
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
					$("#EditForm").modal();
				});
				$("#SubmitEditKVMTemplate").click(function() {
					var editid = $("#EditFormValue").text();
					var name = $("#EditFormName").val();
					$.modal.close();
					loading(1);
					$.getJSON("admin.php?view=templates&type=kvm&action=updatetemplate&id=" + editid + "&name=" + name,function(result){
                        loading(0);
                        $("td.template-"+editid).html(name);
                        $(".icon-button.EditKVMTemplate").attr("rel",name);
						$('#Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
						if(result.reload == 1){
							location.reload();
						}
					});
				});
			});
		</script>
		<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
            <h3 class="title inlineB">KVM Templates</h3>
            <div class="shortcuts-icons inlineB">
                <a class="shortcut tips" id="NewKVMTemplate" title="Add Template"><i class="fa fa-plus-circle"></i></a>
            </div>
			<table id="KVMTable">
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
							<td class="template-{%?template[id]}">{%?template[name]}</td>
							<td>{%?template[path]}</td>
							<td><div align="center">KVM</div></td>
							<td>
								<div align="center">
									<a alt="Delete Template" class="icon-button DeleteKVMTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><i class="fa fa-times-circle"></i></a>
									<a alt="Edit Template" class="icon-button EditKVMTemplate" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?template[name]}" value="{%?template[id]}"><i class="fa fa-edit"></i></a>
								</div>
							</td>
						</tr>
					{%/foreach}
				{%/if}
				{%if isset|TemplateList == false}
					<tr>
                        <td style="display:none;"></td>
                        <td style="display:none;"></td>
                        <td style="display:none;"></td>
                        <td colspan="4"><i>No KVM templates found; Try adding one using the '+' button above.</i></td><!-- Last one for css reasons -->
					</tr>
				{%/if}
			</table>
		</div>
		<div id="NewKVMForm" style="display:none;" align="center">
            <h3 class="title" align="center">Add KVM Template</h3>
            <form id="form1" name="form1" class="SubmitKVMTemplate noEnterSubmit pure-form pure-form-aligned">
                <div class="pure-control-group">
                    <label for="KVMTemplateName">Template Name:</label>
                    <input name="kvmtemplatename" id="KVMTemplateName" type="text" required>
                </div>
                <div class="pure-control-group">
                    <label for="KVMTemplateName">Download URL:</label>
                    <input name="kvmtemplateurl" id="KVMTemplateURL" type="text" required>
                </div>
                <br>
                <div align="center" class="formnote">Please be aware submitting new templates may take up to 2 minutes (download time).</div>
                <br>
                <div align="center" id="FormKVMTemplate">
                    <a href="#" class="pure-button pure-button-primary button-green" id="SubmitKVMTemplate">Add KVM Template</a>
                </div>
            </form>
		</div>
		<div id="DeleteForm" style="display:none;" align="center">
            <h3 class="title" align="center">Delete Template</h3>
            <form id="form3" name="form3" class="Delete noEnterSubmit">
                <div class="formnote"><a style="color:#737F89;" id="DeleteFormText"></a><a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a><a id="DeleteFormType" style="display:none;"></a>?</div><br>
                <div align="center" id="FormDelete" class="pure-g">
                    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                        <a href="#" class="pure-button pure-button-primary button-red button-xlarge" id="ConfirmDelete" style="width:90%;">Yes</a>
                    </div>
                    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                        <a href="#" class="pure-button pure-button-primary button-blue button-xlarge" id="CancelDelete" style="width:90%;">No</a>
                    </div>
                </div>
            </form>
		</div>
		<div id="EditForm" style="display:none;" align="center">
            <h3 class="title" align="center">Edit Template</h3>
            <form id="form1" name="form1" class="SubmitKVMTemplate noEnterSubmit pure-form pure-form-aligned">
                <div class="pure-control-group">
                    <label for="#EditFormName">Template Name:</label>
                    <input name="kvmtemplatename" id="EditFormName" value="" type="text"><a id="EditFormValue" style="display:none;"></a>
                </div>
                <br>
                <div align="center" id="FormEditKVMTemplate">
                    <a class="pure-button pure-button-primary button-blue" id="SubmitEditKVMTemplate">Update Template</a>
                </div>
            </form>
		</div>
	{%/if}
{%/if}