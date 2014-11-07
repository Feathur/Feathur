<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
        $('.noEnterSubmit').keypress(function(e){
			if ( e.which == 13 ) e.preventDefault();
		});
        $("#result-error").hide();
		$("#result-success").hide();
	});
</script>

<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 nofluid">
	<div class="alert errorbox" id="result-error"></div>
	<div class="alert successbox" id="result-success"></div>
</div>

<!--- If no type is set, ask admin what type they want. --->
{%if isset|Type == false}
    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
        <div class="outlined whitebox">
            <h3 class="title">Select a Pool</h3>
            <div align="center" class="pure-g">
                <br>
                <div class="pure-u-1-2">
                    <a class="pure-button pure-button-primary button-blue button-xlarge" href="admin.php?view=ippools&type=0">IPv4 Pools</a></a>
                </div>
                <div class="pure-u-1-2">
                    <a class="pure-button pure-button-primary button-blue button-xlarge disabled" href="admin.php?view=ippools&type=1">IPv6 Pools</a>
                </div>
            </div>
        </div>
    </div>
{%/if}

<!--- If the Type isset, and the pool is not set display some blocks. --->
{%if isset|Type == true}
	{%if isset|Pool == false}
		
		<!--- If the Type is empty then the blocks are IPv4 --->
		{%if isempty|Type == true}
			<script type="text/javascript">
				$(document).ready(function() {
					$("#AddBlock").click(function(){
						$("#NewBlockForm").modal();
					});
					$('#SubmitNewBlock').click(function() {
						var name = $("#NewBlockName").val();
						var gateway = $("#NewBlockGateway").val();
						var netmask = $("#NewBlockNetmask").val()
						$('#SubmitNewBlockWrapper a').html('Please Wait...');
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=ippools&type=0&action=create_pool&name=" + name + "&gateway=" + gateway + "&netmask=" + netmask,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
						});
					});
					$(".DeleteBlock").click(function() {
                        var blockname = $(this).attr('rel');
                        var blockid = $(this).attr('value');
                        $("#DeleteFormName").html(blockname);
                        $("#DeleteFormValue").html(blockid);
                        $("#DeleteForm").modal();
					});
					$("#ConfirmDelete").click(function() {
						var deleteid = $("#DeleteFormValue").text();
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=ippools&type=0&action=delete_pool&id=" + deleteid,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
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
						$("#EditForm").modal();
					});
					$("#FormEditBlock").click(function() {
						var editid = $("#EditFormValue").text();
						var name = $("#EditFormName").val();
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=ippools&type=0&action=rename_pool&id=" + editid + "&name=" + name,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
						});
					});
				});
			</script>
            <br>
			<div align="center" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
				{%if isset|BlockList == true}
                    <h3 class="title inlineB">IPv4 Blocks</h3>
                    <div class="shortcuts-icons inlineB">
                        <a class="shortcut tips" id="AddBlock" title="Add IP Block" alt="Add IP Block"><i class="fa fa-plus-circle"></i></a>
                    </div>
                    <table class="dataTables_wrapper" id="ListTable">
                        <thead>
                            <tr>
                                <th width="80%"><div align="center">Name</div></th>
                                <th width="20%"><div align="center">Actions</div></th>
                            </tr>
                        </thead>
                        <tbody>
                        {%if isset|BlockList == true}
                            {%if isempty|BlockList == false}
                                {%foreach block in BlockList}
                                    <tr>
                                        <td><a href="admin.php?view=ippools&type=0&pool={%?block[id]}">{%?block[name]}</a></td>
                                        <td>
                                            <div align="center">
                                                <a original-title="Delete" class="icon-button tips DeleteBlock" rel="{%?block[name]}" value="{%?block[id]}"><i class="fa fa-times-circle"></i></a>
                                                <a original-title="Edit" class="icon-button tips EditBlock" rel="{%?block[name]}" value="{%?block[id]}"><i class="fa fa-edit"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                {%/foreach}
                            {%/if}
                            {%if isempty|BlockList == true}
                                <tr>
                                    <td style="display:none;"></td>
                                    <td colspan="2">
                                        <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IP blocks; Add one using the '+' above.</div>
                                    </td>
                                </tr>
                            {%/if}
                        {%/if}
                        {%if isset|BlockList == false}
                            <tr>
                                <td style="display:none;"></td>
                                <td colspan="2">
                                    <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IP blocks; Add one using the '+' above.</div>
                                </td>
                            </tr>
                        {%/if}
                        </tbody>
                    </table>
				{%/if}
				{%if isset|BlockList == false}
					<br>
					<div align="center">There are currently no pools defined.</div>
				{%/if}
			</div>
            
            <div id="NewBlockForm" style="display:none;" align="center">
            <h3 class="title" align="center">Add IPv4 Block</h3>
                <form id="form1" name="form1" class="SubmitBlockForm noEnterSubmit pure-form pure-form-aligned">
                    <div class="pure-control-group">
                        <label for="NewBlockName">Block Name:</label>
                        <input name="newblockname" class="st-forminput" id="NewBlockName" type="text" required>
                    </div>
                    <div class="pure-control-group">
                        <label for="NewBlockGateway">Gateway:</label>
                        <input name="newblockgateway" class="st-forminput" id="NewBlockGateway" type="text" required>
                    </div>
                    <div class="pure-control-group">
                        <label for="NewBlockNetmask">Netmask:</label>
                        <input name="newblocknetmask" class="st-forminput" id="NewBlockNetmask" type="text" required>
                    </div>
                    <br>
                    <div align="center" id="SubmitNewBlockWrapper">
                        <a href="#" class="pure-button pure-button-primary button-blue" id="SubmitNewBlock">Add IP Block</a>
                    </div>
                </form>
            </div>
			<div id="DeleteForm" style="display:none;height:130px;" align="center">
                <h3 class="title" align="center">Delete IP Block</h3>
                <form id="form3" name="form3" class="Delete noEnterSubmit">
                    <div class="formnote">Do you want to delete the IP block <a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a>?</div><br>
                    <div align="center" id="FormDelete" class="pure-g">
                        <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                            <a class="pure-button pure-button-primary button-red button-xlarge" id="ConfirmDelete" style="width:90%;">Yes</a>
                        </div>
                        <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                            <a class="pure-button pure-button-primary button-blue button-xlarge" id="CancelDelete" style="width:90%;">No</a>
                        </div>
                    </div>
                </form>
			</div>
            <div id="EditForm" style="display:none;" align="center">
                <h3 class="title" align="center">Edit IP Block</h3>
                <form id="form1" name="form1" class="SubmitEditBlock noEnterSubmit pure-form pure-form-aligned">
                    <div class="pure-control-group">
                        <label for="#EditFormName">Block Name:</label>
                        <input name="ipblockname" class="st-forminput" id="EditFormName" value="" style="width:150px" type="text"><a id="EditFormValue" style="display:none;"></a>
                    </div>
                    <br>
                    <div align="center" id="FormEditBlockWrapper">
                        <a class="pure-button pure-button-primary button-blue" id="FormEditBlock">Update IP Block</a>
                    </div>
                </form>
            </div>
		{%/if}
		
		<!--- If the Type is not empty then the blocks are IPv6 --->
		{%if isempty|Type == false}
			<script type="text/javascript">
				$(document).ready(function() {
					$("#AddBlock").click(function(){
						$("#NewBlockForm").modal();
					});
					$("#blockresult-error").hide();
					$("#blockresult-success").hide();
					$("#SubmitNewBlock").click(function(){
						var datastring = $("#newblock").serialize();
						$.ajax({
							type: "POST",
							url: "admin.php?view=ippools&type=1&action=create_pool&id=",
							data: datastring,
							dataType: "json",
							success: function(result) {
								if(typeof(result.red) != "undefined" && result.red !== null) {
									$("#blockresult-error").html(result.red);
									$("#blockresult-error").show();
								} else {
									$("#blockresult-success").html(result.content);
									$("#blockresult-success").show();
									window.location.reload();
								}
							},
						});
					});
					$(".DeleteBlock").click(function() {
                        var blockname = $(this).attr('rel');
                        var blockid = $(this).attr('value');
                        $("#DeleteFormName").html(blockname);
                        $("#DeleteFormValue").html(blockid);
                        $("#DeleteForm").modal();
					});
					$("#ConfirmDelete").click(function() {
						var deleteid = $("#DeleteFormValue").text();
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=ippools&type=1&action=delete_pool&id=" + deleteid,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
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
						$("#EditForm").modal();
					});
					$("#SubmitEditBlock").click(function() {
						var editid = $("#EditFormValue").text();
						var name = $("#EditFormName").val();
						$.modal.close();
						loading(1)
						$.getJSON("admin.php?view=ippools&type=1&action=rename_pool&id=" + editid + "&name=" + name,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
						});
					});
				});
				function nextbox(fldobj, nbox) { 
					if (fldobj.value.length==fldobj.maxLength) {
						fldobj.form.elements[nbox].focus();
					}
				}
				function CustomSelected(nameSelect) {
					var val = nameSelect.options[nameSelect.selectedIndex].value;
					document.getElementById("custombox").style.display = val == '-1' ? "block" : 'none';
				}
			</script>
			<br>
			<div align="center" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
				{%if isset|BlockList == true}
                    <h3 class="title inlineB">IPv6 Blocks</h3>
                    <div class="shortcuts-icons inlineB">
                        <a class="shortcut tips" id="AddBlock" title="Add IP Block" alt="Add IP Block"><i class="fa fa-plus-circle"></i></a>
                    </div>
                    <table class="dataTables_wrapper" id="ListTable">
                        <thead>
                            <tr>
                                <th width="80%"><div align="center">Name</div></th>
                                <th width="20%"><div align="center">Actions</div></th>
                            </tr>
                        </thead>
                        <tbody>
                        {%if isset|BlockList == true}
                            {%if isempty|BlockList == false}
                                {%foreach block in BlockList}
                                    <tr>
                                        <td><a href="admin.php?view=ippools&type=1&pool={%?block[id]}">{%?block[name]}</a></td>
                                        <td>
                                            <div align="center">
                                                <a original-title="Delete" class="icon-button tips DeleteBlock" rel="{%?block[name]}" value="{%?block[id]}"><i class="fa fa-times-circle"></i></a>
                                                <a original-title="Edit" class="icon-button tips EditBlock" rel="{%?block[name]}" value="{%?block[id]}"><i class="fa fa-edit"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                {%/foreach}
                            {%/if}
                            {%if isempty|BlockList == true}
                                <tr>
                                    <td style="display:none;"></td>
                                    <td colspan="2">
                                        <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IP blocks; Add one using the '+' above.</div>
                                    </td>
                                </tr>
                            {%/if}
                        {%/if}
                        {%if isset|BlockList == false}
                            <tr>
                                <td style="display:none;"></td>
                                <td colspan="2">
                                    <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IP blocks; Add one using the '+' above.</div>
                                </td>=
                            </tr>
                        {%/if}
                        </tbody>
                    </table>
				{%/if}
				{%if isset|BlockList == false}
					<br>
					<div align="center">There are currently no pools defined.</div>
				{%/if}
			</div>
			<div id="NewBlockForm" style="display:none;" align="center">
                <h3 class="title" align="center">Add IPv6 Block</h3>
                <div align="center">
                    <div class="alert errorbox" id="blockresult-error"></div>
                    <div class="alert succesbox" id="blockresult-success"></div>
                </div>
                <form id="newblock" name="form1" class="SubmitBlockForm noEnterSubmit">
                    <table border="0">
                        <tr>
                            <td width="75">Block Name:</td>
                            <td><input name="newblockname" class="st-forminput" id="NewBlockName" type="text"></td>
                        </tr>
                        <tr>
                            <td width="75">Netmask:</td>
                            <td><select name="newblocknetmask" id="newblocknetmask"><option value="/32">/32</option><option value="/48">/48</option><option value="/64">/64</option><option value="/80">/80</option><option value="/96">/96</option><option value="/112">/112</option></select></td>
                        </tr>
                        <tr>
                            <td width="75">Gateway:</td>
                            <td><input name="g1" onkeyup="nextbox(this,'g2');" maxlength="4" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'g3');" name="g2" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'g4');" name="g3" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'g5');" name="g4" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'g6');" name="g5" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'g7');" name="g6" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'g8');" name="g7" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" name="g8" class="st-forminput" style="width:40px;" type="text"></td>
                        </tr>
                        <tr>
                            <td width="75">First Usable:</td>
                            <td><input name="f1" onkeyup="nextbox(this,'f2');" maxlength="4" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'f3');" name="f2" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'f4');" name="f3" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'f5');" name="f4" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'f6');" name="f5" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'f7');" name="f6" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" onkeyup="nextbox(this,'f8');" name="f7" class="st-forminput" style="width:40px;" type="text">&nbsp;<input maxlength="4" name="f8" class="st-forminput" style="width:40px;" type="text"></td>
                        </tr>
                        <tr>
                            <td width="75">IPv6 Per VPS:</td>
                            <td>
                                <select name="newblockpervps" id="newblockperuser" onchange="CustomSelected(this);" style="width:99%"><option value="/48">/48</option><option value="/64">/64</option><option value="/80">/80</option><option value="/96">/96</option><option value="/112">/112</option><option value="/128">/128</option><option value="-1" id="customselected">Custom (Must be a number, EG: 10):</option></select><br>
                                <div id="custombox" style="display:none;">
                                    <input name="newblockcustomipv6" class="st-forminput" id="NewBlockCustomIPv6" style="width:95%" type="text">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div align="center" id="SubmitNewBlockWrapper"><a class="pure-button pure-button-primary button-blue" id="SubmitNewBlock">Add IP Block</a></div>
                </form>
			</div>
			<div id="DeleteForm" style="display:none;" align="center">
                <h3 class="title" align="center">Delete IP Block</h3>
                <form id="form3" name="form3" class="Delete noEnterSubmit">
                    <div class="formnote">Do you want to delete the IP block <a style="color:#737F89;" id="DeleteFormName"></a><a id="DeleteFormValue" style="display:none;"></a>?</div><br>
                    <div align="center" id="FormDelete" class="pure-g">
                        <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                            <a class="pure-button pure-button-primary button-red button-xlarge" id="ConfirmDelete" style="width:90%;">Yes</a>
                        </div>
                        <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
                            <a class="pure-button pure-button-primary button-blue button-xlarge" id="CancelDelete" style="width:90%;">No</a>
                        </div>
                    </div>
                </form>
			</div>
			<div id="EditForm" style="display:none;" align="center">
                <h3 class="title" align="center">Edit IP Block</h3>
                <form id="form1" name="form1" class="SubmitEditBlock noEnterSubmit pure-form pure-form-aligned">
                    <div class="pure-control-group">
                        <label for="#EditFormName">Block Name:</label>
                        <input name="ipblockname" class="st-forminput" id="EditFormName" value="" style="width:150px" type="text"><a id="EditFormValue" style="display:none;"></a>
                    </div>
                    <br>
                    <div align="center" id="FormEditBlockWrapper">
                        <a class="pure-button pure-button-primary button-blue" id="FormEditBlock">Update IP Block</a>
                    </div>
                </form>
            </div>
		{%/if}
	{%/if}
	
	<!--- If the Pool isset then display the pool settings --->
	{%if isset|Pool == true}
	
		<!--- If the Pool type is empty then this block is IPv4 --->
		{%if isempty|Type == true}
			<script type="text/javascript">
				$(document).ready(function() {
					$('#SubmitNewIP').click(function() {
						var ip = $("#SingleIPAdd").val();
						$('#SubmitNewIPWrapper a').html('Please Wait...');
						if(!ip){
							$('#SubmitNewIPWrapper a').html('Add Single IP');
						}
						else {
							$.modal.close();
							loading(1);
							$.getJSON("admin.php?view=ippools&type=0&pool={%?Pool}&action=add_ipv4&ip=" + ip,function(result){
                                loading(0);
								if(typeof(result.red) != "undefined" && result.red !== null) {
									$("#result-error").html(result.red);
									$("#result-error").show();
								} else {
									$("#result-success").html(result.content);
									$("#result-success").show();
									window.location.reload();
								}
							});
						}
					});
					$('#SubmitNewRange').click(function() {
						var start = $("#StartIPAdd").val();
						var end = $("#EndIPAdd").val();
						$('#SubmitNewRangeWrapper a').html('Please Wait...');
						if((!start) || (!end)){
							$('#SubmitNewRangeWrapper a').html('Add Range of IPs');
						}
						else {
							$.modal.close();
							loading(1);
							$.getJSON("admin.php?view=ippools&type=0&pool={%?Pool}&action=add_ipv4_range&start=" + start + "&end=" + end,function(result){
                                loading(0);
								if(typeof(result.red) != "undefined" && result.red !== null) {
									$("#result-error").html(result.red);
									$("#result-error").show();
								} else {
									$("#result-success").html(result.content);
									$("#result-success").show();
									window.location.reload();
								}
							});
						}
					});
					$(".DeleteIP").click(function() {
						var ip = $(this).attr('rel');
						var id = $(this).attr('value');
						$("#DeleteFormName").html(ip);
						$("#DeleteFormValue").html(id);
						$("#DeleteFormText").html("Do you really want to remove the IP: ");
						$("#DeleteFormType").html("remove_ipv4");
						$("#DeleteForm").modal();
					});
					 $("#AddIP").click(function(){
                        $("#NewIPForm").modal();
					});
					$("#ConfirmDelete").click(function() {
						var id = $("#DeleteFormValue").text();
						var type = $("#DeleteFormType").text();
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=ippools&type=0&pool={%?Pool}&action=" + type + "&id=" + id,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
						});
					});
					$("#CancelDelete").click(function() {
						$.modal.close();
					});
					$("#AddServer").click(function(){
						$("#NewServerForm").modal();
					});
					$('#SubmitServer').click(function() {
						var id = $("#SelectedServer").val();
						$('#SubmitNewServer a').html('Please Wait...');
						if(!id){
							$('#SubmitNewServer a').html('Add Server To Block');
						}
						else {
							$.modal.close();
							loading(1);
							$.getJSON("admin.php?view=ippools&type=0&pool={%?Pool}&action=add_server&id=" + id,function(result){
                                loading(0);
								if(typeof(result.red) != "undefined" && result.red !== null) {
									$("#result-error").html(result.red);
									$("#result-error").show();
								} else {
									$("#result-success").html(result.content);
									$("#result-success").show();
									window.location.reload();
								}
							});
						}
					});
					$(".DeleteServer").click(function() {
						var name = $(this).attr('rel');
						var id = $(this).attr('value');
						$("#DeleteFormName").html(name);
						$("#DeleteFormValue").html(id);
						$("#DeleteFormText").html("Remove the following server from this block: ");
						$("#DeleteFormType").html("remove_server");
						$("#DeleteForm").modal();
					});
				});
			</script>
			<br>
            <div class="tabs primarytabs">
                <div class="tab nth btn1 cur" onclick="showCon(1)"><span>IP Addresses</span><i class="fa fa-sitemap"></i></div>
                <div class="tab nth btn2" onclick="showCon(2)"><span>Servers</span><i class="fa fa-hdd-o"></i></div>
            </div>
            
            <div id="tabConWrap" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1-2">
                <div id="tabCon" class="con1">
                    <div id="tabConTxt">
                        <h3 class="title inlineB">{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if} IP Management</h3>
                        <div class="shortcuts-icons inlineB">
                            <a class="shortcut tips" title="Add IP Addresses" id="AddIP"><i class="fa fa-plus-circle"></i></a>
                        </div>
                        <table class="dataTables_wrapper" id="ListTable">
                            <thead>
                                <tr>
                                    <th width="40%"><div align="center">IP Address</div></th>
                                    <th width="30%"><div align="center">Owner</div></th>
                                    <th width="30%"><div align="center">Actions</div></th>
                                </tr>
                            </thead>
                            <tbody>                            
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
                                                    <a original-title="Delete" class="icon-button tips DeleteIP" rel="{%?ip[ip]}" value="{%?ip[id]}"><i class="fa fa-times-circle"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    {%/foreach}
                                {%/if}
                                {%if isempty|IPList == true}
                                    <tr>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                        <td colspan="3">
                                            <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IPs; Add one using the '+' above.</div>
                                        </td>
                                    </tr>
                                {%/if}
                            {%/if}
                            {%if isset|IPList == false}
                                <tr>
                                    <td style="display:none;"></td>
                                    <td style="display:none;"></td>
                                    <td colspan="3">
                                        <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IPs; Add one using the '+' above.</div>
                                    </td>
                                </tr>
                            {%/if}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tabCon" class="con2">
                    <div id="tabConTxt">
                        <h3 class="title inlineB">{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if} Server Management</h3>
                        <div class="shortcuts-icons inlineB">
                            <a class="shortcut" id="AddServer" title="Add Server"><i class="fa fa-plus-circle"></i></a>
                        </div>
                        <table class="dataTables_wrapper" id="ListTable">
                            <thead>
                                <tr>
                                    <th width="80%"><div align="center">Server</div></th>
                                    <th width="20%"><div align="center">Actions</div></th>
                                </tr>
                            </thead>
                            <tbody>
                            {%if isset|ServerList == true}
                                {%if isempty|ServerList == false}
                                    {%foreach server in ServerList}
                                        <tr>
                                            <td>{%?server[name]}</td>
                                            <td>
                                                <div align="center">
                                                    <a class="icon-button tips DeleteServer" rel="{%?server[name]}" value="{%?server[id]}"><i class="fa fa-times-circle"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    {%/foreach}
                                {%/if}
                                {%if isempty|ServerList == true}
                                    <tr>
                                        <td style="display:none;"></td>
                                        <td colspan="2">
                                            <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no servers assigned to this block, add one using the + above. (1)</div>
                                        </td>
                                    </tr>
                                {%/if}
                            {%/if}
                            {%if isset|ServerList == false}
                                <tr>
                                    <td style="display:none;"></td>
                                    <td colspan="2">
                                        <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no servers assigned to this block, add one using the + above. (2)</div>
                                    </td>
                                </tr>
                            {%/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
			<div id="NewIPForm" style="display:none;" align="center">
                <h3 class="title">Add A Single IP</h3>
                <form id="form1" name="form1" class="pure-form pure-form-aligned SubmitBlockForm noEnterSubmit">
                    <div class="pure-control-group">
                        <label for="SingleIPAdd">IP Address:</label>
                        <input name="singleip" class="st-forminput" id="SingleIPAdd" type="text" required><br>
                    </div>
                    <div align="center" id="SubmitNewIPWrapper"><a class="pure-button pure-button-primary button-blue" id="SubmitNewIP">Add Single IP</a></div>
                </form>
				<br>
                <h3 class="title">Add A Range of IPs</h3>
                <form id="form1" name="form1" class="pure-form pure-form-aligned SubmitBlockForm noEnterSubmit">
                    <div class="pure-control-group">
                        <label for="StartIPAdd">Start IP:</label>
                        <input name="startip" class="st-forminput" id="StartIPAdd" type="text" required>
                    </div>
                    <div class="pure-control-group">
                        <label for="EndIPAdd">End IP:</label>
                        <input name="endip" class="st-forminput" id="EndIPAdd" type="text" required>
                    </div>
                    <br>
                    <div align="center" id="SubmitNewRangeWrapper"><a class="pure-button pure-button-primary button-blue" id="SubmitNewRange">Add Range of IPs</a></div>
                </form>
			</div>
            <div id="DeleteForm" style="display:none;" align="center">
                <h3 class="title" align="center">Delete</h3>
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
			<div id="NewServerForm" style="display:none;" align="center">
                <h3 class="title">Add A Server to A Block</h3>
                <form id="form1" name="form1" class="pure-form pure-form-aligned SubmitBlockForm noEnterSubmit">
                    <div class="pure-control-group">
                        <label for="SelectedServer">Server to Add:</label>
                        <select id="SelectedServer" name="SelectedServer">
                            {%if isset|AvailableServers == true}
                                {%foreach Server in AvailableServers}
                                    <option value="{%?Server[id]}">{%?Server[name]}</option>
                                {%/foreach}
                            {%/if}
                            {%if isset|AvailableServers == false}
                                <option>No Servers Available</option>
                            {%/if}
                        </select>
                    </div>
                    <div align="center" id="SubmitNewServer"><a class="pure-button pure-button-primary button-blue" id="SubmitServer">Add Server to Block</a></div>
                </form>
			</div>
		{%/if}
		
		<!--- If the Type isset then this pool is IPv6 --->
		{%if isempty|Type == false}
			<script type="text/javascript">
				$(document).ready(function() {
					$(".DeleteIP").click(function() {
						var ip = $(this).attr('rel');
						var id = $(this).attr('value');
						$("#DeleteFormName").html(ip);
						$("#DeleteFormValue").html(id);
						$("#DeleteFormText").html("Do you really want to remove the IP: ");
						$("#DeleteFormType").html("remove_ipv4");
						$("#DeleteForm").modal();
					});
					$("#ConfirmDelete").click(function() {
						var id = $("#DeleteFormValue").text();
						var type = $("#DeleteFormType").text();
						$.modal.close();
						loading(1);
						$.getJSON("admin.php?view=ippools&type=1&pool={%?Pool}&action=" + type + "&id=" + id,function(result){
                            loading(0);
							if(typeof(result.red) != "undefined" && result.red !== null) {
								$("#result-error").html(result.red);
								$("#result-error").show();
							} else {
								$("#result-success").html(result.content);
								$("#result-success").show();
								window.location.reload();
							}
						});
					});
					$("#CancelDelete").click(function() {
						$.modal.close();
					});
					$("#AddServer").click(function(){
						$("#NewServerForm").modal();
					});
					$('#SubmitServer').click(function() {
						var id = $("#SelectedServer").val();
						$('#SubmitNewServer a').html('Please Wait...');
						if(!id){
							$('#SubmitNewServer a').html('Add Server To Block');
						}
						else {
							$.modal.close();
							loading(1);
							$.getJSON("admin.php?view=ippools&type=1&pool={%?Pool}&action=add_server&id=" + id,function(result){
                                loading(0);
								if(typeof(result.red) != "undefined" && result.red !== null) {
									$("#result-error").html(result.red);
									$("#result-error").show();
								} else {
									$("#result-success").html(result.content);
									$("#result-success").show();
									window.location.reload();
								}
							});
						}
					});
					$(".DeleteServer").click(function() {
						var name = $(this).attr('rel');
						var id = $(this).attr('value');
						$("#DeleteFormName").html(name);
						$("#DeleteFormValue").html(id);
						$("#DeleteFormText").html("Remove the following server from this block: ");
						$("#DeleteFormType").html("remove_server");
						$("#DeleteForm").modal();
					});
				});
			</script>
			<br>
            <div class="tabs primarytabs">
                <div class="tab nth btn1 cur" onclick="showCon(1)"><span>IP Addresses</span><i class="fa fa-sitemap"></i></div>
                <div class="tab nth btn2" onclick="showCon(2)"><span>Servers</span><i class="fa fa-hdd-o"></i></div>
            </div>
            
            <div id="tabConWrap" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1-2">
                <div id="tabCon" class="con1">
                    <div id="tabConTxt">
                        <h3 class="title inlineB">{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if} IP Management</h3>
                        <div class="shortcuts-icons inlineB">
                            <a class="shortcut tips" title="Add IP Addresses" id="AddIP"><i class="fa fa-plus-circle"></i></a>
                        </div>
                        <table class="dataTables_wrapper" id="ListTable">
                            <thead>
                                <tr>
                                    <th width="40%"><div align="center">IP Address</div></th>
                                    <th width="30%"><div align="center">Owner</div></th>
                                    <th width="30%"><div align="center">Actions</div></th>
                                </tr>
                            </thead>       
                            <tbody>
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
                                                    <a original-title="Delete" class="icon-button tips DeleteIP" style="padding-left:5px;padding-right:5px;cursor:pointer;" rel="{%?ip[ip]}" value="{%?ip[id]}"><i class="fa fa-times-circle"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    {%/foreach}
                                {%/if}
                                {%if isempty|IPList == true}
                                    <tr>
                                        <td style="display:none;"></td>
                                        <td style="display:none;"></td>
                                        <td colspan="3">
                                            <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IPs currently assigned to any VPS.</div>
                                        </td>
                                    </tr>
                                {%/if}
                            {%/if}
                            {%if isset|IPList == false}
                                <tr>
                                    <td style="display:none;"></td>
                                    <td style="display:none;"></td>
                                    <td colspan="3">
                                        <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no IPs currently assigned to any VPS.</div>
                                    </td>
                                </tr>
                            {%/if}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tabCon" class="con2">
                    <h3 class="title inlineB">{%if isset|BlockName == true}{%?BlockName}{%/if}{%if isset|BlockName == false}IP Block{%/if} Server Management</h3>
                    <div class="shortcuts-icons inlineB">
                        <a class="shortcut" id="AddServer" title="Add Server"><i class="fa fa-plus-circle"></i></a>
                    </div>
                    <table class="dataTables_wrapper" id="ListTable">
                        <thead>
                            <tr>
                                <th width="80%"><div align="center">Server</div></th>
                                <th width="20%"><div align="center">Actions</div></th>
                            </tr>
                        </thead>      
                        <tbody>                        
                        {%if isset|ServerList == true}
                            {%if isempty|ServerList == false}
                                {%foreach server in ServerList}
                                    <tr>
                                        <td>{%?server[name]}</td>
                                        <td>
                                            <div align="center">
                                                <a class="icon-button tips DeleteServer" rel="{%?server[name]}" value="{%?server[id]}"><i class="fa fa-times-circle"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                {%/foreach}
                            {%/if}
                            {%if isempty|ServerList == true}
                                <tr>
                                    <td style="display:none;"></td>
                                    <td colspan="2">
                                        <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no servers assigned to this block, add one using the + above. (1)</div>
                                    </td>
                                </tr>
                            {%/if}
                        {%/if}
                        {%if isset|ServerList == false}
                            <tr>
                                <td style="display:none;"></td>
                                <td colspan="2">
                                    <div align="center" style="height: 38px;padding: 0;line-height: 38px;">There are no servers assigned to this block, add one using the + above. (2)</div>
                                </td>
                            </tr>
                        {%/if}
                        </tbody>
                    </table>
                </div>
            </div>
			<div id="DeleteForm" style="display:none;" align="center">
                <h3 class="title" align="center">Delete</h3>
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
			<div id="NewServerForm" style="display:none;" align="center">
                <h3 class="title">Add A Server to A Block</h3>
                <form id="form1" name="form1" class="pure-form pure-form-aligned SubmitBlockForm noEnterSubmit">
                    <div class="pure-control-group">
                        <label for="SelectedServer">Server to Add:</label>
                        <select id="SelectedServer" name="SelectedServer">
                            {%if isset|AvailableServers == true}
                                {%foreach Server in AvailableServers}
                                    <option value="{%?Server[id]}">{%?Server[name]}</option>
                                {%/foreach}
                            {%/if}
                            {%if isset|AvailableServers == false}
                                <option>No Servers Available</option>
                            {%/if}
                        </select>
                    </div>
                    <div align="center" id="SubmitNewServer"><a class="pure-button pure-button-primary button-blue" id="SubmitServer">Add Server to Block</a></div>
                </form>
			</div>
		{%/if}
	{%/if}
{%/if}
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $('.dataTables_wrapper').dataTable({
        "dom": '<"table-top"lf>rt<"table-bottom"ip>',
        "pagingType": "full_numbers",
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "DisplayLength": 10,
        "stateSave": true,
        "paging": false,
        "language": {
            "emptyTable": "No Entries",
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