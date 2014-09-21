<div class="st-form-line">	
	<span class="st-labeltext">Select Template:</span>	
	<select id="template" name="template" style="width:520px;">
		<option value="0">--- Choose One ---</option>
		<option value="">None</option>
		{%if isset|TemplateList == true}
			{%foreach template in TemplateList}
				<option value="{%?template[id]}">{%?template[name]}</option>
			{%/foreach}
		{%/if}
		{%if isset|TemplateList == false}
			<option>No templates for this server, please add one. (Settings => Template Manager)</option>
		{%/if}
	</select>
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">RAM (MB):</span>	
	<input id="ram" type="text" name="ram" value="256" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">Disk (GB):</span>	
	<input id="disk" type="text" name="disk" value="10" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">CPU Limit (1 per Core):</span>	
	<input id="cpulimit" type="text" name="cpulimit" value="1" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">Bandwidth Limit (GB):</span>	
	<input id="bandwidthlimit" type="text" name="bandwidthlimit" value="1024" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">IP Addresses:</span>	
	<select name="ipaddresses" id="ipaddresses" style="width:520px;">
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
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">IPv6 Allowed:</span>	
	<select name="ipv6allowed" id="ipv6allowed" style="width:520px;">
		<option value="0">No</option>
		<option value="1">Yes</option>
	</select>
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">Hostname (optional):</span>	
	<input id="hostname" type="text" name="hostname" value="server.example.com" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">Nameserver (optional):</span>	
	<input id="nameserver" type="text" name="nameserver" value="8.8.8.8" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line">	
	<span class="st-labeltext">VNC Password (optional):</span>	
	<input id="password" type="password" name="password" value="" style="width:500px;">
	<div class="clear"></div>
</div>
<div class="st-form-line" align="center">
	<input id="Create" type="button" value="Create" class="small blue"/>
	<br><br>
	<div id="update"></div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#Create").click(function() {
			$("#Create").css({visibility: "hidden"});
			$('#update').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
			var user = $('#user').attr('value');
			var server = $('#ServerSelection').attr('value');
			var template = $('#template').attr('value');
			var ram = $('#ram').attr('value');
			var disk = $('#disk').attr('value');
			var cpulimit = $('#cpulimit').attr('value');
			var bandwidthlimit = $('#bandwidthlimit').attr('value');
			var ipaddresses = $('#ipaddresses').attr('value');
			var hostname = $('#hostname').attr('value');
			var nameserver = $('#nameserver').attr('value');
			var password = $('#password').attr('value');
			var ipv6allowed = $('#ipv6allowed').attr('value');
			var beginbuild = $.ajax({
				type: "POST",
				url: "admin.php?view=createvps&action=create",
				data: "password=" + password + "&user=" + user + "&server=" + server + "&template=" + template + "&ram=" + ram + "&disk=" + disk + "&cpulimit=" + cpulimit + "&bandwidthlimit=" + bandwidthlimit + "&ipaddresses=" + ipaddresses + "&hostname=" + hostname + "&nameserver=" + nameserver + "&password=" + password + "&ipv6allowed=" + ipv6allowed,
				success: function(data){
					var result = $.parseJSON(data);
					$('#update').html('<div style="z-index: 670;width:60%;height:25px;" class="albox small-' + result.type + '"><div id="Status" style="padding:4px;padding-left:5px;width:95%;">' + result.result + '</div><div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div></div>');
					if(result.reload == 1){
						window.location = "view.php?id=" + result.vps;
					} else {
						$("#Create").css({visibility: "visible"});
					}
				}
			});
		});
	});
</script>