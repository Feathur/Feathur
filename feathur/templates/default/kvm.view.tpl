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
		$(function() {
			$("#tabs").tabs();
		});
	});
</script>
<br><br>
<div align="center">
	<div id="tabs" style="width:95%">
		<ul>
			<li><a href="#tabs-1">General</a></li>
			<li><a href="#tabs-2">Settings</a></li>
		</ul>
		<div id="tabs-1" style="height:300px;">
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
				<table class="tablesorter" style="height:130px;">
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
					<h3>Mount Disk</h3>
				</div>
				<table class="tablesorter">
					<tr>
						<td width="30%">Select ISO:</td>
						<td width="70%">
							<select id="SelectedTemplate" style="width:100%">
								{%foreach template in Templates}
									<option value="{%?template[id]}" {%if template[primary] == 1}selected="selected"{%/if}>{%?template[name]}</option>
								{%/foreach}
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
			<div class="simplebox grid340-left">
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
		</div>
	</div>
</div>
{%/foreach}