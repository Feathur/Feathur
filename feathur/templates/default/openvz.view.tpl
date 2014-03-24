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
		$(function() {
			$("#tabs").tabs();
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
		$("#ChangePrimaryIP").click(function() {
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var ipaddress = $('#SelectedIP').attr('value');
			$.getJSON("view.php?id={%?vps[id]}&action=primaryip&ip=" + ipaddress,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');   
			});
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
		{%if vps[ipv6] == 1}
			{%if isempty|IPv6Exist == true}
				$("#RequestBlock").click(function() {
					$('#IPv6Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
					$.getJSON("view.php?id={%?vps[id]}&action=requestblock",function(result){
						$('#IPv6Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>'); 
						if(result.reload == 1){
							location.reload();
						}
					});
				});
			{%/if}
			{%if isempty|IPv6Exist == false}
				$("#AddIPv6").click(function() {
					var blockid = $('#AddIPv6').attr('value');
					$('#IPv6Notice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
					$.getJSON("view.php?id={%?vps[id]}&action=addipv6&block=" + blockid,function(result){
						$('#IPv6Notice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
						if(result.reload == 1){
							location.reload();
						}
					});
				});
			{%/if}
		{%/if}
		$("#ChangeHostname").click(function() {
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var hostname = $('#Hostname').attr('value');
			$.getJSON("view.php?id={%?vps[id]}&action=hostname&hostname=" + hostname,function(result){
				$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');  
			});
		});
		$("#TunTap").click(function() {
			var tuntap = $('#TunTapValue').text();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			if (tuntap == 1) {
				$.getJSON("view.php?id={%?vps[id]}&action=tuntap&setting=0",function(result){
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					$('#TunTapValue').html(0);
					$('#TunTap').addClass('button-green').removeClass('button-red');
					$('#TunTap').text("Enable Tun/Tap");
				});
			} else {
				$.getJSON("view.php?id={%?vps[id]}&action=tuntap&setting=1",function(result){
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					$('#TunTapValue').html(1);
					$('#TunTap').addClass('button-red').removeClass('button-green');
					$('#TunTap').text("Disable Tun/Tap");
				});
			}
		});
		$("#PPP").click(function() {
			var ppp = $('#PPPValue').text();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			if (ppp == 1) {
				$.getJSON("view.php?id={%?vps[id]}&action=ppp&setting=0",function(result){
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					$('#PPPValue').html(0);
					$('#PPP').addClass('button-green').removeClass('button-red');
					$('#PPP').text("Enable PPP");
				});
			} else {
				$.getJSON("view.php?id={%?vps[id]}&action=ppp&setting=1",function(result){
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					$('#PPPValue').html(1);
					$('#PPP').addClass('button-red').removeClass('button-green');
					$('#PPP').text("Disable PPP");
				});
			}
		});
		$("#IPTables").click(function() {
			var iptables = $('#IPTablesValue').text();
			$('#SettingNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			if (iptables == 1) {
				$.getJSON("view.php?id={%?vps[id]}&action=iptables&setting=0",function(result){
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					$('#IPTablesValue').html(0);
					$('#IPTables').addClass('button-green').removeClass('button-red');
					$('#IPTables').text("Enable IP Tables");
				});
			} else {
				$.getJSON("view.php?id={%?vps[id]}&action=iptables&setting=1",function(result){
					$('#SettingNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					$('#IPTablesValue').html(1);
					$('#IPTables').addClass('button-red').removeClass('button-green');
					$('#IPTables').text("Disable IPTables");
				});
			}
		});
		$(document).on("click", ".CloseToggle", function(){
			$('#SettingNotice').html('');
			$('#Notice').html('');
			$('#RebuildNotice').html();
			$('#AdminNotice').html();
			$('#IPv6Notice').html();
		});
		$("#Rebuild").click(function() {
			$('#ReloadNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var template = $('#SelectedTemplate').attr('value');
			var rebuildpassword = $('#RebuildPassword').attr('value');
			var verify = $('#VerifyRebuild').is(':checked');
			if(verify == 1) {
				if(rebuildpassword) {
					var beginrebuild = $.ajax({
						type: "POST",
						url: "view.php?id={%?vps[id]}&action=rebuild&template=" + template,
						data: "password=" + rebuildpassword,
						success: function(data){
							var result = $.parseJSON(data);
							if(result.type == 'error'){
								$('#RebuildNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
							} else {
								$('#page').html(result.result);
							}
						}
					});
				} else {
					$('#RebuildNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-error"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">You must enter a password to rebuild!</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
				}
			} else {
				$('#RebuildNotice').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-error"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">You must check the verification box to rebuild!</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
			}
		});
		$("#ConsoleInput").keypress(function(event) {
			if (event.which == 13) {
				$("#ConsoleLoading").css({visibility: "visible"});
				var input = $('#ConsoleInput').attr('value');
				var elem = document.getElementById('ConsoleOutput');
				$("#ConsoleInput").val("");
				if(input){
					$("#ConsoleOutput").append("# " + input + "<br>");
					elem.scrollTop = elem.scrollHeight;
				}
				$.getJSON("view.php?id={%?vps[id]}&action=console&command=" + input,function(result){
					$("#ConsoleOutput").append(result.result);
					$("#ConsoleLoading").css({visibility: "hidden"});
					elem.scrollTop = elem.scrollHeight;
				});
			}
		});
		{%if UserPermissions == 7}
			$("#UpdateVPS").click(function() {
				$('#AdminNotice').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				var ram = $('#AdminRAM').attr('value');
				var swap = $('#AdminSWAP').attr('value');
				var disk = $('#AdminDisk').attr('value');
				var cpuunits = $('#AdminCPUUnits').attr('value');
				var cpulimit = $('#AdminCPULimit').attr('value');
				var bandwidthlimit = $('#AdminBandwidthLimit').attr('value');
				var ipv6allowed = $('#AdminIPv6Allowed').attr('value');
				var inodes = $('#AdminInodes').attr('value');
				$.getJSON("view.php?id={%?vps[id]}&action=update&ram=" + ram + "&swap=" + swap + "&disk=" + disk + "&cpuunits=" + cpuunits + "&cpulimit=" + cpulimit + "&bandwidth=" + bandwidthlimit + "&inodes=" + inodes + "&ipv6allowed=" + ipv6allowed,function(result){
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
			{%if vps[ipv6] == 1}{%if isempty|IPv6Exist == false}<li><a href="#tabs-3">IPv6</a></li>{%/if}{%/if}
			<li><a href="#tabs-4">Rebuild</a></li>
			<li><a href="#tabs-5">Command Center</a></li>
			<li><a href="#tabs-6">Console</a></li>
			{%if UserPermissions == 7}<li><a href="#tabs-7">Admin</a></li>{%/if}
		</ul>
		<div id="tabs-1">
			<table>
				<tr>
					<td style="width:20px;">
						<div align="center" id="Status" style="width:30px;"><img src="templates/default/img/loading/6.gif" style="padding:0px;margin:0px;"></div>
					</td>
					<td width="30%" align="left">
						<h5 style="padding-left:10px;">VPS - OpenVZ<div id="VPSHostname">({%?vps[hostname]})</div></h5>
					</td>
					<td width="70%" align="right">
						<div id="Notice"></div>	
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<br><br>
						<div align="center" style="width:100%">
							{%if UserPermissions == 7}
								{%if vps[suspended] == 1}<div class="albox warningbox" style="width:50%;">This VPS is Suspended.</div>{%/if}
								{%if vps[suspended] == 2}<div class="albox warningbox" style="width:50%;">This VPS is Suspended by Feathur due to abuse.</div>{%/if}
								{%if vps[suspended] == 3}<div class="albox warningbox" style="width:50%;">This VPS is Suspended by Feathur due to possible spam.</div>{%/if}
							{%/if}
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
								<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
									<button class="large black GenericAction" style="width:100px;" value="kill"><div align="center"><span class="icon-remove icon-2x"></span><br>Kill</div></button>
								</div>
								{%if UserPermissions == 7}
									{%if vps[suspended] == 1}
										<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
											<button class="large red GenericAction" style="width:100px;" value="unsuspend"><div align="center"><span class="icon-unlock icon-2x"></span><br>Unsuspend</div></button>
										</div>
									{%/if}
									{%if vps[suspended] == 2}
										<div style="display:inline-block;vertical-align:middle;padding-left:15px;padding-right:15px;" align="center">
											<button class="large red GenericAction" style="width:100px;" value="unsuspend"><div align="center"><span class="icon-unlock icon-2x"></span><br>Unsuspend</div></button>
										</div>
									{%/if}
									{%if vps[suspended] == 3}
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
						</div>
						<br><br>
						<div align="center" style="width:100%">
							<div id="Statistics"><img src="templates/default/img/loading/7.gif" style="padding:0px;margin:0px;"></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="tabs-2" style="height:700px;">
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
			<div class="simplebox grid340-left">
				<div class="titleh">
					<h3>Root Password</h3>
				</div>
				<table class="tablesorter" style="height:130px;">
					<tr>
						<td width="50%">New Root Password:</td>
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
					<h3>Modules</h3>
				</div>
				<table class="tablesorter" style="height:130px;">
					<tr>
						<td width="40%" valign="center">
							<div align="center">
								TUN/TAP:
							</div>
						</td>
						<td width="60%">
							<div id="TunTapValue" style="display:none;">
								{%if isempty|vps[tuntap] == true}0{%/if}
								{%if isempty|vps[tuntap] == false}1{%/if}
							</div>
							<div align="center">
								<div id="TunTapButton">
									<a href="#" id="TunTap" class="button-{%if isempty|vps[tuntap] == true}green{%/if}{%if isempty|vps[tuntap] == false}red{%/if}" style="color:#FFFFFF;">
										{%if isempty|vps[tuntap] == true}Enable TunTap{%/if}
										{%if isempty|vps[tuntap] == false}Disable TunTap{%/if}
									</a>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td width="40%" valign="center">
							<div align="center">
								PPP:
							</div>
						</td>
						<td width="60%">
							<div id="PPPValue" style="display:none;">
								{%if isempty|vps[ppp] == true}0{%/if}
								{%if isempty|vps[ppp] == false}1{%/if}
							</div>
							
							<div align="center">
								<div id="PPPButton">
									<a href="#" id="PPP" class="button-{%if isempty|vps[ppp] == true}green{%/if}{%if isempty|vps[ppp] == false}red{%/if}" style="color:#FFFFFF;">
										{%if isempty|vps[ppp] == true}Enable PPP{%/if}
										{%if isempty|vps[ppp] == false}Disable PPP{%/if}
									</a>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td width="40%" valign="center">
							<div align="center">
								IP Tables:
							</div>
						</td>
						<td width="60%">
							<div id="IPTablesValue" style="display:none;">
								{%if isempty|vps[iptables] == true}0{%/if}
								{%if isempty|vps[iptables] == false}1{%/if}
							</div>
							<div align="center">
								<div id="IPTablesButton">
									<a href="#" id="IPTables" class="button-{%if isempty|vps[iptables] == true}green{%/if}{%if isempty|vps[iptables] == false}red{%/if}" style="color:#FFFFFF;">
										{%if isempty|vps[iptables] == true}Enable IP Tables{%/if}
										{%if isempty|vps[iptables] == false}Disable IP Tables{%/if}
									</a>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:100%;margin:10px;"></div>
			<div class="simplebox grid340-left">
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
			<div class="simplebox grid340-left">
				<div class="titleh">
					<h3>rDNS</h3>
				</div>
				<table class="tablesorter" style="height:120px;">
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
						<td colspan="2" style="height:100%;vertical-align:bottom;">
							<div align="center" style="visibility:hidden;" id="RDNSButton">
								<button class="small blue" id="UpdateRDNS">Update rDNS</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		{%if vps[ipv6] == 1}
			{%if isempty|IPv6Exist == false}
				<div id="tabs-3" style="height:600px;">
					<table style="width:100%;">
						<tr>
							<td width="25%" align="left">
							</td>
							<td width="70%" align="right">
								<div id="IPv6Notice"></div>
							</td>
						</tr> 
					</table>
					<br><br>
					<div style="z-index: 500;text-align:left;" class="simple-tips">
						<h2>IPv6 Management Notice</h2>
						<ul>
							<li>IPv6 functionality is still under development and may features may not work correctly all the time.</li>
							<li>If you remove an IPv6 address you can not add it again.</li>
							<li>IPv6 are assigned from the first subblock and are limited to 65,000.</li>
							<li>It is not recommended to assign more than 128 IPv6 to your VPS as it will slow down.</li>
						</ul>
						<a href="#" onClick="return false;" class="close tips" title="Close">close</a>
					</div>
					<br><br>
					{%if isempty|UserIPv6Block == true}
						<button class="small blue" id="RequestBlock">Request IPv6 Access</button>
					{%/if}
					{%if isempty|UserIPv6Block == false}
						<div align="center">
							{%foreach block in UserIPv6Block}
								{%if isempty|block[is_block] == false}
									<div class="simplebox grid740" style="width:700px;">
										<div class="titleh">
											<h3>{%?block[prefix]}{%?block[size]} Management</h3>
											<div class="shortcuts-icons">
												<a class="shortcut tips" id="AddIPv6" title="Add IPv6 Address" value="{%?block[id]}"><img src="./templates/default/img/icons/shortcut/addfile.png" width="25" height="25" alt="icon" /></a>
											</div>
										</div>
										<table class="tablesorter">
											<tr><td>
												<div align="center">
													Block Management Here
												</div>
											</td></tr>
										</table>
									</div>
								{%/if}
							{%/foreach}
						</div>
					{%/if}
				</div>
			{%/if}
		{%/if}
		<div id="tabs-4" style="height:600px;">
			<div align="center" style="width:100%">
				<table style="width:100%;">
					<tr>
						<td width="25%" align="left">
						</td>
						<td width="70%" align="right">
							<div id="RebuildNotice"></div>
						</td>
					</tr> 
				</table>
				<br><br>
				<div class="simplebox grid360">
					<div class="titleh">
						<h3>Rebuild</h3>
					</div>
					<table class="tablesorter">
						<tr>
							<td width="50%">Select Template:</td>
							<td width="50%">
								<select id="SelectedTemplate" style="width:100%">
									{%foreach template in Templates}
										<option value="{%?template[id]}" {%if template[primary] == 1}selected="selected"{%/if}>{%?template[name]}</option>
									{%/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td width="50%">New Root Password:</td>
							<td width="50%"><input id="RebuildPassword" type="password" name="password" /></td>
						</tr>
						<tr>
							<td colspan="2"><input type="checkbox" name="VerifyRebuild" id="VerifyRebuild" value="1">I understand that this procedure will destroy my VPS and all data on it, and start with a desired template.</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
									<button class="small blue" id="Rebuild">Rebuild</button>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div id="tabs-5">
			<div align="center" style="width:100%">
				<div style="z-index: 500;text-align:left;" class="simple-tips">
		                        <h2>Command Center Notes</h2>
					<ul>
						<li>You can use this command center to issue commands to your VPS even if you can't connect via SSH.</li>
						<li><strong>Notice:</strong> Commands issued are not successive. Each command is executed independently.</li>
						<li>You can issue successive commands by putting a ; between them. (Eg: cd /var; ls}</li>
						<li>To start SSH on most systems you can type: service ssh start -OR- service sshd start</li>
					</ul>
					<a href="#" onClick="return false;" class="close tips" title="Close">close</a>
				</div>
				<div class="simplebox grid740" style="width:700px;">
					<div class="titleh">
						<h3>Command Center</h3>
					</div>
					<table class="tablesorter">
						<tr>
							<td>
								<div style="background:#000000;color:#FFFFFF;height:20px;overflow-x:hidden;overflow-y:hidden;padding:5px;border-bottom:1px solid grey;">
									<div style="float:right;width:60px;z-index:-1;visibility:hidden;vertical-align:center;" align="right" id="ConsoleLoading"><img src="./templates/default/img/loading/9.gif"></div>
								</div>
								<div style="background:#000000;color:#FFFFFF;overflow-x:hidden;overflow-y:visible;height:300px;padding:5px;" id="ConsoleOutput"></div>
							</td>
						</tr>
						<tr>
							<td><input id="ConsoleInput" type="text" name="ConsoleInput" style="width:95%" /></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div id="tabs-6">
			<iframe src="console.php?id={%?vps[id]}" width="100%" height="500" frameborder="0" scrolling="no"></iframe>
		</div>
		{%if UserPermissions == 7}
			<div id="tabs-7" style="height:1500px">
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
					<table class="tablesorter" style="height:500px;">
						<tr>
							<td style="width:50%">RAM (MB):</td>
							<td><input id="AdminRAM" type="text" name="AdminRAM" value="{%?vps[ram]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">SWAP (MB):</td>
							<td><input id="AdminSWAP" type="text" name="AdminSWAP" value="{%?vps[swap]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">Disk (GB):</td>
							<td><input id="AdminDisk" type="text" name="AdminDisk" value="{%?vps[disk]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">CPU Units:</td>
							<td><input id="AdminCPUUnits" type="text" name="AdminCPUUnits" value="{%?vps[cpuunits]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">CPU Limit (100/core):</td>
							<td><input id="AdminCPULimit" type="text" name="AdminCPULimit" value="{%?vps[cpulimit]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">Bandwidth Limit (GB):</td>
							<td><input id="AdminBandwidthLimit" type="text" name="AdminBandwidthLimit" value="{%?vps[bandwidthlimit]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">Inodes (200,000 Default):</td>
							<td><input id="AdminInodes" type="text" name="AdminInodes" value="{%?vps[inodes]}" style="width:90%" /></td>
						</tr>
						<tr>
							<td style="width:50%">IPv6 Allowed:</td>
							<td>
								<select id="AdminIPv6Allowed">
									<option value="0" {%if isset|vps[ipv6] == true}{%if vps[ipv6] == 0}selected="selected"{%/if}{%/if}>No</option>
									<option value="1" {%if isset|vps[ipv6] == true}{%if vps[ipv6] == 1}selected="selected"{%/if}{%/if}>Yes</option>
								</select>
							</td>
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
				<div class="simplebox grid340-right">
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
				<div class="simplebox grid340-left">
					<div class="titleh">
						<h3>Transfer</h3>
					</div>
					<table class="tablesorter">
						<tr>
							<td style="width:50%">Select Destination</td>
							<td>
								<select name="TransferServer" id="TransferServer" style="width:90%;">
									{%foreach server in Servers}
										{%if isempty|server[current] == true}
											<option value="{%?server[id]}">{%?server[name]}</option>
										{%/if}
									{%/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center"><button class="small blue" id="TransferVPS">Transfer VPS</button></div>
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
								<input type="checkbox" name="VerifyTerminate" id="VerifyTerminate" value="1">I understand that this will completely destroy this poor users pitiful VPS.
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