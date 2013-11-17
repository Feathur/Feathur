{%foreach vps in VPS}
<script type="text/javascript">
	$(document).ready(function() {
		$(".GenericAction").click(function() {
			$('#Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var action = $(this).val();    
			$.getJSON("view.php?id={%?vps[id]}&action=" + action,function(result){
				$('#Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
				if(result.reload == 1){
					location.reload();
				} else {
					uptime();
				}
			});
		});
		function uptime() {
			$(function() {
				$.getJSON("view.php?id={%?vps[id]}&action=statistics",function(data){
					if (data.hostname === undefined){
						uptime();
					} else {
						$("#Statistics").html(data.content);
						$("#Status").html('<img src="./templates/status/' + data.result + '.png" style="width:100%;">');
						$("#VPSHostname").html('(' + data.hostname + ')');
					}
				});
			});
		}
		setInterval(uptime, 10000);
		uptime();
		$("#ChangePassword").click(function() {
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var password = $('#password').attr('value');
			$.ajax({
				type: "POST",
				url: "view.php?id={%?vps[id]}&action=password",
				data: "password=" + password,
				success: function(data){
					var result = $.parseJSON(data);
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');          
				}
			});
		});
		$("#Mount").click(function() {
			var template = $('#SelectedTemplate').val();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			$.getJSON("view.php?id={%?vps[id]}&action=mount&template=" + template,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
			});
		});
		$("#BootOrder").click(function() {
			var order = $('#SelectedOrder').val();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			$.getJSON("view.php?id={%?vps[id]}&action=bootorder&order=" + order,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
			});
		});
		$("#ChangeNIC").click(function() {
			var nic = $('#SelectedNIC').val();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			$.getJSON("view.php?id={%?vps[id]}&action=changenic&nic=" + nic,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
			});
		});
		$(function() {
			$("#tabs").tabs();
		});
		$("#RDNSIP").change(function() {
			var ipid = $('#RDNSIP').val();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			$.getJSON("view.php?id={%?vps[id]}&action=getrdns&ip=" + ipid,function(result){
				$('#RDNSValue').val(result.result);
				$('#SettingNotice').html('');
				$("#RDNSButton").css({visibility: "visible"});
			});
		});
		$("#UpdateRDNS").click(function() {
			var ipid = $('#RDNSIP').val();
			var rdns = $('#RDNSValue').val();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			$.getJSON("view.php?id={%?vps[id]}&action=setrdns&ip=" + ipid + "&hostname=" + rdns,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
			});
		});
		$("#ChangeHostname").click(function() {
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var hostname = $('#Hostname').attr('value');
			$.getJSON("view.php?id={%?vps[id]}&action=hostname&hostname=" + hostname,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
			});
		});
		$("#ChangePrimaryIP").click(function() {
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var ipaddress = $('#SelectedIP').attr('value');
			$.getJSON("view.php?id={%?vps[id]}&action=primaryip&ip=" + ipaddress,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');   
			});
		});
		$(document).on("click", ".CloseToggle", function(){
			$('#SettingNotice').html('');
			$('#Notice').html('');
			$('#RebuildNotice').html();
			$('#AdminNotice').html();
		});
		{%if UserPermissions == 7}
			$("#UpdateVPS").click(function() {
				$('#AdminNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				var ram = $('#AdminRAM').attr('value');
				var disk = $('#AdminDisk').attr('value');
				var cpulimit = $('#AdminCPULimit').attr('value');
				var bandwidthlimit = $('#AdminBandwidthLimit').attr('value');
				$.getJSON("view.php?id={%?vps[id]}&action=update&ram=" + ram + "&disk=" + disk + "&cpulimit=" + cpulimit + "&bandwidth=" + bandwidthlimit,function(result){
					$('#AdminNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
				});
			});
			$("#AddIPAddresses").click(function() {
				$('#AdminNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				var add = $('#AddIP').attr('value');
				$.getJSON("view.php?id={%?vps[id]}&action=addip&ip=" + add,function(result){
					$('#AdminNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					if(result.reload == 1){
						location.reload();
					}
				});
			});
			$("#RemoveIPAddress").click(function() {
				$('#AdminNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				var remove = $('#RemoveIP').attr('value');
				$.getJSON("view.php?id={%?vps[id]}&action=removeip&ip=" + remove,function(result){
					$('#AdminNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					if(result.reload == 1){
						location.reload();
					}
				});
			});
			$("#ManuallyAssignIP").click(function() {
				$('#AdminNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				var add = $('#AssignIP').attr('value');
				$.getJSON("view.php?id={%?vps[id]}&action=assignip&ip=" + add,function(result){
					$('#AdminNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					if(result.reload == 1){
						location.reload();
					}
				});
			});
			$("#Terminate").click(function() {
				$('#AdminNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				var verify = $('#VerifyTerminate').is(':checked');
				if(verify == 1) {
					$.getJSON("view.php?id={%?vps[id]}&action=terminate&verify=1",function(result){
						$('#AdminNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
						if(result.reload == 1){
							location.reload();
						}
					});
				} else {
					$('#AdminNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-error"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">You must check the verification box to terminate!</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
				}
			});
		{%/if}
	});
</script>
<br><br>
<div align="center">
	<div id="tabs" style="width:95%">
		<ul>
			<li><a href="#tabs-1">General</a></li>
			<li><a href="#tabs-2">Settings</a></li>
			{%if UserPermissions == 7}<li><a href="#tabs-3">Admin</a></li>{%/if}
		</ul>
		<div id="tabs-1" style="height:500px;">
			<table>
				<tr>
					<td style="width:20px;">
						<div align="center" id="Status" style="width:30px;"><img src="templates/default/img/loading/6.gif" style="padding:0px;margin:0px;"></div>
					</td>
					<td width="30%" align="left">
						<h5 style="padding-left:10px;">VPS - KVM<div id="VPSHostname">({%?vps[hostname]})</div></h5>
					</td>
					<td width="70%" align="right">
						<div id="Notice"></div>	
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<br><br>
						<div align="center" style="width:100%">
							{%if UserPermissions == 7}{%if vps[suspended] == 1}<div class="albox warningbox" style="width:50%;">This VPS is Suspended.</div>{%/if}{%/if}
							<div style="width:90%;white-space:nowrap;overflow:hidden;text-align:center;">
								<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
									<button class="large green GenericAction" style="width:100px;" value="boot"><div align="center"><span class="icon-play icon-2x"></span><br>Start</div></button>
								</div>
								<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
									<button class="large red GenericAction" style="width:100px;" value="shutdown"><div align="center"><span class="icon-stop icon-2x"></span><br>Stop</div></button>
								</div>
								<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
									<button class="large orange GenericAction" style="width:100px;" value="reboot"><div align="center"><span class="icon-refresh icon-2x"></span><br>Restart</div></button>
								</div>
								{%if UserPermissions == 7}
									{%if vps[suspended] == 1}
										<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
											<button class="large red GenericAction" style="width:100px;" value="unsuspend"><div align="center"><span class="icon-unlock icon-2x"></span><br>Unsuspend</div></button>
										</div>
									{%/if}
									{%if vps[suspended] == 0}
										<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
											<button class="large red GenericAction" style="width:100px;" value="suspend"><div align="center"><span class="icon-lock icon-2x"></span><br>Suspend</div></button>
										</div>
									{%/if}
								{%/if}
							</div>
							<br>
							<div style="z-index: 670;width:90%;height:25px;" class="albox small-caution">
								<div id="Status" style="padding:4px;padding-left:5px;width:100%;">
									<strong>Notice:</strong> Starting/Stopping from Feathur kills it immediately. Use shutdown -r now on your VPS if possible!
								</div>
							</div>
						</div>
						<br><br>
						<div align="center" style="width:100%">
							<div id="Statistics"></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="tabs-2" style="height:900px;">
			<table style="width:100%;">
				<tr>
					<td width="25%" align="left">
					</td>
					<td width="70%" align="right">
						<div id="SettingNotice"></div>
					</td>
				</tr> 
			</table>
			<br><br>
			<div style="z-index: 500;text-align:left;width:95%;margin:2px;" class="simple-tips">
	                        <h2>Setting Information</h2>
					<p>Most of the settings on this page are temporary in nature. They will only remain until your VPS is rebooted from Feathur. This is in an effort to help protect the security of your passwords and settings. Feathur does not store passwords in plain text. Sorry for any inconvenience...</p>
				<a href="#" onClick="return false;" class="close tips" title="Close">close</a>
			</div>
			<br><br>
			<div class="simplebox grid340-left">
				<div class="titleh">
					<h3>VNC Password</h3>
				</div>
				<table class="tablesorter">
					<tr>
						<td width="50%">VNC Server/Port:</td>
						<td width="50%">
							{%foreach data in UserVPSList}
								{%if isempty|data[this] == false}
									{%?data[server_ip]}:{%?vps[vnc_port]}
								{%/if}
							{%/foreach}
						</td>
					</tr>
					<tr>
						<td width="50%">New VNC Password:</td>
						<td width="50%"><input id="password" type="password" name="password"/></td>
					</tr>
					<tr>
						<td colspan="2" style="height:100%;vertical-align:bottom;">
							<div align="center">
								<button class="small blue" id="ChangePassword">Change Password</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="simplebox grid340-right">
				<div class="titleh">
					<h3>rDNS</h3>
				</div>
				<table class="tablesorter">
					<tr>
						<td width="40%">Select IP</td>
						<td width="60%">
							<select id="RDNSIP" style="width:100%">
								<option selected="selected">Select An IP</option>
								{%if isset|IPs == true}
									{%foreach ip in IPs}
										<option value="{%?ip[id]}">{%?ip[ip]}</option>
									{%/foreach}
								{%/if}
							</select>
						</td>
					</tr>
					<tr>
						<td>rDNS Entry:</td>
						<td><input id="RDNSValue" type="text" name="RDNSValue" /></td>
					</tr>
					<tr>
						<td colspan="2" style="height:100%;vertical-align:bottom;padding:5px;">
							<div align="center" style="visibility:hidden;" id="RDNSButton">
								<button class="small blue" id="UpdateRDNS">Update rDNS</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="simplebox grid340-left">
				<div class="titleh">
					<h3>Mount Disk</h3>
				</div>
				<table class="tablesorter">
					<tr>
						<td width="30%">Select ISO:</td>
						<td width="70%">
							<select id="SelectedTemplate" style="width:100%">
								{%if isset|Templates == true}
									{%foreach template in Templates}
										<option value="{%?template[id]}" {%if template[primary] == 1}selected="selected"{%/if}>{%?template[name]}</option>
									{%/foreach}
								{%/if}
								{%if isset|Templates == false}
									<option value="">None Available</option>
								{%/if}
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="center">
								<button class="small blue" id="Mount">Mount ISO</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="simplebox grid340-right">
				<div class="titleh">
					<h3>Boot Order</h3>
				</div>
				<table class="tablesorter">
					<tr>
						<td width="30%">Boot Order:</td>
						<td width="70%">
							<select id="SelectedOrder" style="width:100%">
								<option value="hd" {%if isset|vps[boot_order] == true}{%if vps[boot_order] == hd}selected="selected"{%/if}{%/if}>Hard Disk, CD-ROM</option>
								<option value="cd" {%if isset|vps[boot_order] == true}{%if vps[boot_order] == cd}selected="selected"{%/if}{%/if}>CD-ROM, Hard Disk</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="center">
								<button class="small blue" id="BootOrder">Change Boot Order</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="simplebox grid340-left">
				<div class="titleh">
					<h3>Hostname</h3>
				</div>
				<table class="tablesorter" style="height:120px;">
					<tr>
						<td width="40%">Hostname:</td>
						<td width="60%"><input id="Hostname" type="text" name="Hostname" value="{%?vps[hostname]}" /></td>
					</tr>
					<tr>
						<td colspan="2" style="height:100%;vertical-align:bottom;">
							<div align="center">
								<button class="small blue" id="ChangeHostname">Change Hostname</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:100%;margin:10px;"></div>
			<div class="simplebox grid340-right">
				<div class="titleh">
					<h3>Primary IP</h3>
				</div>
				<table class="tablesorter" style="height:120px;">
					<tr>
						<td width="40%">Primary IP</td>
						<td width="60%">
							<select id="SelectedIP" style="width:100%">
								{%if isset|IPs == true}
									{%foreach ip in IPs}
										<option value="{%?ip[id]}" {%if ip[primary] == 1}selected="selected"{%/if}>{%?ip[ip]}</option>
									{%/foreach}
								{%/if}
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="height:100%;vertical-align:bottom;">
							<div align="center">
								<button class="small blue" id="ChangePrimaryIP">Change Primary IP</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="simplebox grid340-right">
				<div class="titleh">
					<h3>Network Card</h3>
				</div>
				<table class="tablesorter">
					<tr>
						<td width="30%">Network Card:</td>
						<td width="70%">
							<select id="SelectedNIC" style="width:100%">
								<option value="rtl8139" {%if isset|vps[network_driver] == true}{%if vps[network_driver] == rtl8139}selected="selected"{%/if}{%/if}>Realtek 8139</option>
								<option value="e1000" {%if isset|vps[network_driver] == true}{%if vps[network_driver] == e1000}selected="selected"{%/if}{%/if}{%if isset|vps[network_driver] == false}selected="selected"{%/if}>Intel (Recommended)</option>
								<option value="virtio" {%if isset|vps[network_driver] == true}{%if vps[network_driver] == virtio}selected="selected"{%/if}{%/if}>VirtIO</option>
								<option value="ne2k_pci" {%if isset|vps[network_driver] == true}{%if vps[network_driver] == ne2k_pci}selected="selected"{%/if}{%/if}>Realtek 8029</option>
								<option value="pcnet" {%if isset|vps[network_driver] == true}{%if vps[network_driver] == pcnet}selected="selected"{%/if}{%/if}>PCNet</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="center">
								<button class="small blue" id="ChangeNIC">Change Network Card</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		{%if UserPermissions == 7}
			<div id="tabs-3" style="height:1500px">
				<table style="width:100%;">
					<tr>
						<td width="25%" align="left">
						</td>
						<td width="70%" align="right">
							<div id="AdminNotice"></div>
						</td>
					</tr> 
				</table>
				<br><br>
				{%foreach user in User}
					<div class="simplebox grid340-right">
						<div class="titleh">
							<h3>User Details</h3>
						</div>
						<table class="tablesorter" style="height:122px">
							<tr>
								<td style="width:40%;">User Name:</td>
								<td>
									<a href="admin.php?view=clients&id={%?user[id]}">{%?user[username]}</a>
								</td>
							</tr>
							<tr>
								<td style="width:40%;">User Email:</td>
								<td>
									{%?user[email_address]}
								</td>
							</tr>
							{%foreach data in UserVPSList}
								{%if isempty|data[this] == false}
									<tr>
										<td style="width:40%;">VPS CTID:</td>
										<td>
											{%?data[container_id]}
										</td>
									</tr>
									<tr>
										<td style="width:40%;">Hostnode:</td>
										<td>
											{%?data[server]}
										</td>
									</tr>
								{%/if}
							{%/foreach}
						</table>
					</div>
				{%/foreach}
				<div class="simplebox grid340-left">
					<div class="titleh">
						<h3>Edit VPS</h3>
					</div>
					<table class="tablesorter" style="height:300px;">
						<tr>
							<td style="width:50%">RAM (MB):</td>
							<td><input id="AdminRAM" type="text" name="AdminRAM" value="{%?vps[ram]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">Disk (GB):</td>
							<td><input id="AdminDisk" type="text" name="AdminDisk" value="{%?vps[disk]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">CPU Limit (1/core):</td>
							<td><input id="AdminCPULimit" type="text" name="AdminCPULimit" value="{%?vps[cpulimit]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">Bandwidth Limit (GB):</td>
							<td><input id="AdminBandwidthLimit" type="text" name="AdminBandwidthLimit" value="{%?vps[bandwidthlimit]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center"><button class="small blue" id="UpdateVPS">Update VPS</button></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="simplebox grid340-right">
					<div class="titleh">
						<h3>User VPS</h3>
					</div>
					<table class="tablesorter">
						{%if isset|UserVPSList == true}
							{%foreach data in UserVPSList}
								<tr>
									<td>
										<a href="view.php?id={%?data[id]}">{%?data[hostname]}</a>
									</td>
								</tr>
							{%/foreach}
						{%/if}
					</table>
				</div>
				<div class="simplebox grid340-left">
					<div class="titleh">
						<h3>Add IP Address</h3>
					</div>
					<table class="tablesorter" style="height:122px">
						<tr>
							<td style="width:50%">IP Addresses:</td>
							<td>
								<select name="AddIP" id="AddIP" style="width:90%;">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center"><button class="small blue" id="AddIPAddresses">Add IP Addresses</button></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="simplebox grid340-right">
					<div class="titleh">
						<h3>Remove An IP</h3>
					</div>
					<table class="tablesorter">
						<tr>
							<td style="width:50%">Select IP</td>
							<td>
								<select name="RemoveIP" id="RemoveIP" style="width:90%;">
									{%if isset|IPs == true}
										{%foreach ip in IPs}
											<option value="{%?ip[id]}">{%?ip[ip]}</option>
										{%/foreach}
									{%/if}
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center"><button class="small blue" id="RemoveIPAddress">Remove IP Address</button></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="simplebox grid340-left">
					<div class="titleh">
						<h3 class="title">Terminate VPS</h3>
					</div>
					<table class="hide-message tablesorter">
						<tr>
							<td>
								<input type="checkbox" name="VerifyTerminate" id="VerifyTerminate" value="1"> I understand that this will completely destroy this poor users pitiful VPS.
							</td>
						</tr>
						<tr>
							<td>
								<div align="center"><button class="small orange" id="Terminate">Terminate</button></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="simplebox grid340-right">
					<div class="titleh">
						<h3>Manually Assign IP</h3>
					</div>
					<table class="tablesorter">
						<tr>
							<td style="width:50%">Assign IP:</td>
							<td>
								<input id="AssignIP" type="text" name="AssignIP" value="" style="width:90%" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center"><button class="small blue" id="ManuallyAssignIP">Add IP Address</button></div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		{%/if}
	</div>
</div>
{%/foreach}