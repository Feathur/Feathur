<form class="pure-form-aligned">

    <div class="pure-control-group">
        <label for="ram">Select Template:</label>
        <select id="template" name="template">
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
    </div>
    
    <div class="pure-control-group">
        <label for="ram">RAM (MB):</label>
        <input id="ram" type="text" name="ram" value="256">
    </div>
    
    <div class="pure-control-group">
        <label for="ram">Disk (GB):</label>
        <input id="disk" type="text" name="disk" value="10">
    </div>
    
    <div class="pure-control-group">
        <label for="ram">CPU Limit (1 per Core):</label>
        <input id="cpulimit" type="text" name="cpulimit" value="1">
    </div>
    
    <div class="pure-control-group">
        <label for="ram">Bandwidth Limit (GB):</label>
        <input id="bandwidthlimit" type="text" name="bandwidthlimit" value="1024">
    </div>
    
    <div class="pure-control-group">
        <label for="ram">IP Addresses:</label>
        <select name="ipaddresses" id="ipaddresses">
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
    </div>
    
    <div class="pure-control-group">
        <label for="ram">IPv6 Allowed:</label>
        <select name="ipv6allowed" id="ipv6allowed">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </div>
    
    <div class="pure-control-group">
        <label for="ram">Hostname (optional):</label>
        <input id="hostname" type="text" name="hostname" value="server.example.com">
    </div>
    
    <div class="pure-control-group">
        <label for="ram">Nameserver (optional):</label>
        <input id="nameserver" type="text" name="nameserver" value="8.8.8.8">
    </div>
    
    <div class="pure-control-group">
        <label for="ram">VNC Password (optional):</label>
        <input id="password" type="password" name="password" value="">
    </div>

    <div class="pure-controls">
        <input id="Create" type="button" value="Create VPS" class="pure-button pure-button-primary button-green"/>
    </div>
    
</form>

<div id="update"></div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#Create").click(function() {
			$("#Create").css({visibility: "hidden"});
			loading(1);
			var user = $('#user').val();
			var server = $('#ServerSelection').val();
			var template = $('#template').val();
			var ram = $('#ram').val();
			var disk = $('#disk').val();
			var cpulimit = $('#cpulimit').val();
			var bandwidthlimit = $('#bandwidthlimit').val();
			var ipaddresses = $('#ipaddresses').val();
			var hostname = $('#hostname').val();
			var nameserver = $('#nameserver').val();
			var password = $('#password').val();
			var ipv6allowed = $('#ipv6allowed').val();
			var beginbuild = $.ajax({
				type: "POST",
				url: "admin.php?view=createvps&action=create",
				data: "password=" + password + "&user=" + user + "&server=" + server + "&template=" + template + "&ram=" + ram + "&disk=" + disk + "&cpulimit=" + cpulimit + "&bandwidthlimit=" + bandwidthlimit + "&ipaddresses=" + ipaddresses + "&hostname=" + hostname + "&nameserver=" + nameserver + "&password=" + password + "&ipv6allowed=" + ipv6allowed,
				success: function(data){
                    loading(0);
					var result = $.parseJSON(data);
					$('#update').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
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