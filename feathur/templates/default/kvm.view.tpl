{%foreach vps in VPS}
<script type="text/javascript">
	$(document).ready(function() {
		$(".GenericAction").click(function() {
			loading(1);
			var action = $(this).attr('value');    
			$.getJSON("view.php?id={%?vps[id]}&action=" + action,function(result){
                loading(0);
				setNotice("#Notice",result.result, result.type);
				if(result.reload == 1){
					location.reload();
				} else {
					uptime();
				}
			});
		});
        var bwVal = $('#AdminBandwidthLimit').prop('value');
        bwVal = bwVal.replace(".0000","");
        $('#AdminBandwidthLimit').val(bwVal);
        var retryTimeout = 0;
        var retryTimeoutSet = false;
        var receivedData = true;
		function uptime() {
            if(retryTimeoutSet == false) {
                if(receivedData){
                    $(function() {
                        receivedData = false;
                        $.getJSON("view.php?id={%?vps[id]}&action=statistics",function(data){
                            receivedData = true;
                            if (data.hostname === undefined){
                                retryTimeout++;
                                if(retryTimeout >= 3){
                                    console.log("Tried to load the data 3 times. Not trying again until page reload.");
                                    retryTimeoutSet = true;
                                    alert("Tried to load the data 3 times, but got nothing back. Not trying again until page reload.");
                                    return;
                                }
                                console.log("Failed to load data. Retrying.");
                                uptime();
                            } else {
                                console.log("Hostname found!");
                                if(data.content){
                                    console.log("Successfully loaded data!");
                                    updateAllStats(data, data.hostname);
                                } else {
                                    console.log("No statistics data found!");
                                    updateAllStats(offlineData, data.hostname);
                                }
                            }
                            
                        })
                        .fail(function() {
                            console.log("Did not get data.");
                            receivedData = true;
                        });
                    });
                }else{
                    console.log("Have not yet recieved data; Waiting.");
                }
            };
		}
		setInterval(uptime, 10000);
		uptime();
		$("#ChangePassword").click(function() {
			loading(1);
			var password = $('#password').val();
			$.ajax({
				type: "POST",
				url: "view.php?id={%?vps[id]}&action=password",
				data: "password=" + password,
				success: function(data){
                    loading(0);
					var result = $.parseJSON(data);
					setNotice("#SettingsNotice",result.result, result.type);
				}
			});
		});
		$("#Mount").click(function() {
			var template = $('#SelectedTemplate').prop('value');
			loading(1);
			$.getJSON("view.php?id={%?vps[id]}&action=mount&template=" + template,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#BootOrder").click(function() {
			var order = $('#SelectedOrder').prop('value');
			loading(1);
			$.getJSON("view.php?id={%?vps[id]}&action=bootorder&order=" + order,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#ChangeNIC").click(function() {
			var nic = $('#SelectedNIC').prop('value');
			loading(1);
			$.getJSON("view.php?id={%?vps[id]}&action=changenic&nic=" + nic,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#ChangeDisk").click(function() {
			var disk = $('#SelectedDisk').prop('value');
			loading(1);
			$.getJSON("view.php?id={%?vps[id]}&action=changedisk&disk=" + disk,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#RDNSIP").change(function(e) {
            e.preventDefault();
			var ipid = $('#RDNSIP').val();
			loading(1);
			$.getJSON("view.php?id={%?vps[id]}&action=getrdns&ip=" + ipid,function(result){
                loading(0);
				$('#RDNSValue').val(result.result);
				$("#RDNSButton").css({visibility: "visible"});
			});
		});
		$("#UpdateRDNS").click(function(e) {
            e.preventDefault();
			var ipid = $('#RDNSIP').prop('value');
			var rdns = $('#RDNSValue').val();
			loading(1);
			$.getJSON("view.php?id={%?vps[id]}&action=setrdns&ip=" + ipid + "&hostname=" + rdns,function(result){
                loading(0);
				setNotice("#SettingsNotice", result.result, result.type);
			});
		});
		$("#ChangeHostname").click(function(e) {
            e.preventDefault();
			loading(1);
			var hostname = $('#Hostname').val();
			$.getJSON("view.php?id={%?vps[id]}&action=hostname&hostname=" + hostname,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#ChangePrimaryIP").click(function(e) {
            e.preventDefault();
			loading(1);
			var ipaddress = $('#SelectedIP').prop('value');
			$.getJSON("view.php?id={%?vps[id]}&action=primaryip&ip=" + ipaddress,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		{%if UserPermissions == 7}
			$("#UpdateVPS").click(function(e) {
                e.preventDefault();
				loading(1);
				var ram = $('#AdminRAM').val();
				var disk = $('#AdminDisk').val();
				var cpulimit = $('#AdminCPULimit').val();
				var bandwidthlimit = $('#AdminBandwidthLimit').val();
				var ipv6allowed = $('#AdminIPv6Allowed').prop('value');
				$.getJSON("view.php?id={%?vps[id]}&action=update&ram=" + ram + "&disk=" + disk + "&cpulimit=" + cpulimit + "&bandwidth=" + bandwidthlimit + "&ipv6allowed=" + ipv6allowed,function(result){
                    loading(0);
                    setNotice("#AdminNotice",result.result, result.type);
				});
			});
			$("#AddIPAddresses").click(function(e) {
                e.preventDefault();
				loading(1);
				var add = $('#AddIP').prop('value');
				$.getJSON("view.php?id={%?vps[id]}&action=addip&ip=" + add,function(result){
                    loading(0);
					setNotice("#AdminNotice",result.result, result.type);
					if(result.reload == 1){
						location.reload();
					}
				});
			});
			$("#RemoveIPAddress").click(function(e) {
                e.preventDefault();
				loading(1);
				var remove = $('#RemoveIP').prop('value');
				$.getJSON("view.php?id={%?vps[id]}&action=removeip&ip=" + remove,function(result){
                    loading(0);
					setNotice("#AdminNotice",result.result, result.type);
					if(result.reload == 1){
						location.reload();
					}
				});
			});
			$("#ManuallyAssignIP").click(function(e) {
                e.preventDefault();
                loading(1);
				var add = $('#AssignIP').val();
				$.getJSON("view.php?id={%?vps[id]}&action=assignip&ip=" + add,function(result){
                    loading(0);
					setNotice("#AdminNotice",result.result, result.type);
					if(result.reload == 1){
						location.reload();
					}
				});
			});
			$("#Terminate").click(function(e) {
                e.preventDefault();
				loading(1);
				var verify = $('#VerifyTerminate').is(':checked');
				if(verify == 1) {
					$.getJSON("view.php?id={%?vps[id]}&action=terminate&verify=1",function(result){
                        loading(0);
						setNotice("#AdminNotice",result.result, result.type);
						if(result.reload == 1){
							location.reload();
						}
					});
				} else {
                    loading(0);
                    setNotice("#AdminNotice","You must check the verification box to terminate!", "error");
				}
			});
		{%/if}
	});
    
    var updateAllStats = function(theData){
        
        var offlineData = {"content":{"ram":"N\/A","disk":"N\/A","cpulimit":"N\/A","bandwidth_usage":"N\/A","bandwidth_limit":"N\/A","percent_bandwidth":"N\/A","template":"N\/A","hostname":"!Use returned hostname var!","primary_ip":"N\/A","gateway":"N\/A","netmask":"N\/A","mac":"N\/A","iso_sync":null,"sync_error":null,"percent_sync":null}};
        
        //stat table cells
        $('.stat-ram').html(theData.content.info.ram + " MB");
        $('.stat-disk').html(theData.content.info.disk + " GB");
        $('.stat-cores').html(theData.content.info.cpulimit);
        $('.stat-bwusage').html(theData.content.info.bandwidth_usage);
        
        //Info table cells
        $('.info-mounted').html(theData.content.info.template);
        $('.info-hostname').html(theData.hostname);
        $('.info-ip').html(theData.content.info.primary_ip);
        $('.info-gateway').html(theData.content.info.gateway);
        $('.info-netmask').html(theData.content.info.netmask);
        
        //Status
        $("#Status").html('<img src="./templates/{%?Template}/img/tpl/' + theData.result + '.png" style="width:21px;">');
        
        //Hostname
        $("#VPSHostname").html('('+theData.hostname+')');
        
    };
    
    var prevTab=1;
    var numOfTabs = 10;
    var showCon = function(i){
        if(i != prevTab){
            $(".tab").removeClass("cur")
            $(".tab.btn"+prevTab).removeClass("cur");
            $(".tab.btn"+i).addClass("cur");
            for(var n=1;n<numOfTabs;n++){
                    $("#tabCon.con"+n).hide();
            }
            $("#tabCon.con"+i).show();
            $("#tabConWrap").css("height",$("#tabCon.con"+i).height() + "px");
            prevTab=i;
        }
    };
</script>



<div class="pure-u-1">
    <div class="tabs primarytabs">
        <div class="tab nth btn1 cur" onclick="showCon(1)"><span>General</span><i class="fa fa-bar-chart"></i></div>
        <div class="tab nth btn2" onclick="showCon(2)"><span>Settings</span><i class="fa fa-cogs"></i></div>
        {%if UserPermissions == 7}<div class="tab nth btn3" onclick="showCon(3)"><span>Admin</span><i class="fa fa-key"></i></div>{%/if}
    </div>
      
    <div id="tabConWrap">
        <div id="tabCon" class="con1">
            <div id="tabConTxt" class="noBorder">
                <div id="GeneralNotice" class="pure-u-1"></div>
                <div id="vpsStatus" class="pure-u-1" style="text-align:center; font-weight:bold;font-size: 14px;color:#a8a8a8;"><div id="Status" style="position:absolute;margin-left: -26px;margin-top: -4px;width:21px;" class="inlineB"></div> VPS - KVM <div id="VPSHostname" class="inlineB">({%?vps[hostname]})</div></div>
                <div class="pure-u-1">
                    {%if UserPermissions == 7}
                        <div class="errorcontain nofluid pure-u1">
                            {%if vps[suspended] == 1}<div class="alert errorbox static-alert">This VPS is Suspended.</div>{%/if}
                            {%if vps[suspended] == 2}<div class="alert errorbox static-alert">This VPS is Suspended by Feathur due to abuse.</div>{%/if}
                            {%if vps[suspended] == 3}<div class="alert errorbox static-alert">This VPS is Suspended by Feathur due to possible spam.</div>{%/if}
                        </div>
                        <br>
                    {%/if}
                    <div>
                        <div class="pure-u-1 whitebox" style="display: flex;display: -webkit-flex;justify-content: space-around;-webkit-justify-content: space-around;padding-bottom: 1.6em;align-content: space-around;flex-wrap: wrap;-webkit-flex-wrap: wrap;">
                            <div class="vpsbutton green GenericAction" value="boot"><i class="fa fa-play"></i><p class="vpsbtnname">Start</p></div>
                            <div class="vpsbutton orange GenericAction" value="shutdown"><i class="fa fa-stop"></i><p class="vpsbtnname">Stop</p></div>
                            <div class="vpsbutton blue GenericAction" value="reboot"><i class="fa fa-refresh"></i><p class="vpsbtnname">Restart</p></div>
                            {%if UserPermissions == 7}
                                {%if vps[suspended] == 1}
                                    <div class="vpsbutton red GenericAction" value="unsuspend"><i class="fa fa-unlock"></i><p class="vpsbtnname">Unsuspend</p></div>
                                {%/if}
                                {%if vps[suspended] == 2}
                                    <div class="vpsbutton red GenericAction" value="unsuspend"><i class="fa fa-unlock"></i><p class="vpsbtnname">Unsuspend</p></div>
                                {%/if}
                                {%if vps[suspended] == 3}
                                    <div class="vpsbutton red GenericAction" value="unsuspend"><i class="fa fa-unlock"></i><p class="vpsbtnname">Unsuspend</p></div>
                                {%/if}
                                {%if vps[suspended] == 0}
                                    <div class="vpsbutton red GenericAction" value="suspend"><i class="fa fa-lock"></i><p class="vpsbtnname">Suspend</p></div>
                                {%/if}
                            {%/if}
                        </div>
                        <br>
                        <div class="formnote">Notice: Starting/Stopping from Feathur kills it immediately. Use shutdown -r now on your VPS if possible!</div>
                        
                    </div>
                </div>
                <div class="pure-u-1">
                    <br><br>
                        <div id="Statistics">
                            <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 pull-left">
                                <div class="whitebox">
                                    <div class="title">
                                        <h3>VPS Statistics</h3>
                                    </div>
                                    <table id="vpsstats" class="striped-table" style="width:100%;">
                                        <tr>
                                            <td class="pure-u-1-2"><strong>System RAM:</strong></td>
                                            <td class="pure-u-1-2 stat-ram">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>System Disk:</strong></td>
                                            <td class="pure-u-1-2 stat-disk">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>CPU Cores:</strong></td>
                                            <td class="pure-u-1-2 stat-cores">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>Bandwidth Usage:</strong></td>
                                            <td class="pure-u-1-2 stat-bwusage">
                                                Loading..
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 pull-right">
                                <div class="whitebox">
                                    <div class="title">
                                        <h3>VPS Information</h3>
                                    </div>
                                    <table id="vpsinfo" class="striped-table" style="width:100%;">
                                        <tr>
                                            <td class="pure-u-1-2"><strong>Mounted ISO:</strong></td>
                                            <td class="pure-u-1-2 info-mounted">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>Hostname:</strong></td>
                                            <td class="pure-u-1-2 info-hostname">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>Primary IP:</strong></td>
                                            <td class="pure-u-1-2 info-ip">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>Gateway:</strong></td>
                                            <td class="pure-u-1-2 info-gateway">Loading..</td>
                                        </tr>
                                        <tr>
                                            <td class="pure-u-1-2"><strong>Subnet Mask:</strong></td>
                                            <td class="pure-u-1-2 info-netmask">Loading..</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <div id="vpsinfoProgressbars"></div>
                </div>
            </div>
        </div>
        
        <div id="tabCon" class="con2" style="display:none">
            <div id="tabConTxt" class="noBorder">
                <div class="pure-u-1">
                    <div id="SettingsNotice"></div>
                    <h2 class="title">Setting Information</h2>
                    <div class="formnote"><p>Most of the settings on this page are temporary in nature. They will only remain until your VPS is rebooted from Feathur. This is in an effort to help protect the security of your passwords and settings. Feathur does not store passwords in plain text. Sorry for any inconvenience...</p></div>
                </div>
                </div>
                <br><br>
                <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 pull-left inlineB">
                    <div class="pure-u-1">
                        <div class="table-top">VNC Password</div>
                        <table class="pure-table" style="width: 100%;">
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
                                <td width="50%"><input id="password" type="password" name="password" style="width:90%"/></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <button class="pure-button pure-button-primary" id="ChangePassword" style="margin-top: 4px;">Change Password</button>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                    <div class="pure-u-1">
                        <div class="table-top">rDNS</div>
                        <table class="pure-table" style="width:100%;">
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
                                <td><input id="RDNSValue" type="text" name="RDNSValue" style="width:90%"/></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <div align="center" style="visibility:hidden;" id="RDNSButton">
                                        <button class="pure-button pure-button-primary" id="UpdateRDNS">Update rDNS</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                    
                    <div class="pure-u-1">
                        <div class="table-top">Mount Disk</div>
                        <table class="pure-table" style="width:100%;">
                            <tr>
                                <td width="30%">Select ISO:</td>
                                <td width="70%">
                                    <select id="SelectedTemplate" style="width:100%">
                                        <option value="0">--- UNMOUNT / EJECT ALL ---</option>
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
                                <td colspan="2" style="text-align:center">
                                    <div align="center">
                                        <button class="pure-button pure-button-primary" id="Mount">Mount ISO</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                    <div class="pure-u-1">
                        <div class="table-top">Boot Order</div>
                        <table class="pure-table" style="width:100%;">
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
                                <td colspan="2" style="text-align:center">
                                    <div align="center">
                                        <button class="pure-button pure-button-primary" id="BootOrder">Change Boot Order</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                </div>
                <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 pull-right inlineB">
                    <div class="pure-u-1">
                        <div class="table-top">Hostname</div>
                        <table class="pure-table" style="width:100%;">
                            <tr>
                                <td width="40%">Hostname:</td>
                                <td width="60%"><input id="Hostname" type="text" name="Hostname" value="{%?vps[hostname]}" style="width:90%"/></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <div align="center">
                                        <button class="pure-button pure-button-primary" id="ChangeHostname">Change Hostname</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                    <div class="pure-u-1">
                        <div class="table-top">Primary IP</div>
                        <table class="pure-table" style="width:100%;">
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
                                <td colspan="2" style="text-align:center">
                                    <div align="center">
                                        <button class="pure-button pure-button-primary" id="ChangePrimaryIP">Change Primary IP</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                    <div class="pure-u-1">
                        <div class="table-top">Network Card</div>
                        <table class="pure-table" style="width:100%;">
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
                                <td colspan="2" style="text-align:center">
                                    <div align="center">
                                        <button class="pure-button pure-button-primary" id="ChangeNIC">Change Network Card</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                    <div class="pure-u-1">
                        <div class="table-top">Disk Driver</div>
                        <table class="pure-table" style="width:100%;">
                            <tr>
                                <td width="30%">Disk Driver:</td>
                                <td width="70%">
                                    <select id="SelectedDisk" style="width:100%">
                                        <option value="scsi" {%if isset|vps[disk_driver] == true}{%if vps[disk_driver] == scsi}selected="selected"{%/if}{%/if}>SCSI</option>
                                        <option value="ide" {%if isset|vps[disk_driver] == true}{%if vps[disk_driver] == ide}selected="selected"{%/if}{%/if}{%if isset|vps[disk_driver] == false}selected="selected"{%/if}>IDE (Recommended)</option>
                                        <option value="virtio" {%if isset|vps[disk_driver] == true}{%if vps[disk_driver] == virtio}selected="selected"{%/if}{%/if}>VirtIO</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <div align="center">
                                        <button class="pure-button pure-button-primary" id="ChangeDisk">Change Disk Driver</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="table-bottom" style="min-height:1px;"></div>
                    </div>
                </div>
            </div>
        
        {%if UserPermissions == 7}
        <div id="tabCon" class="con3" style="display:none">
            <div id="tabConTxt" class="noBorder">
                <div id="AdminNotice"></div>
                <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1-2 left">
                    {%foreach user in User}
                        <div class="table-top">
                            User Details
                        </div>
                        <table class="pure-table" style="width: 100%;">
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
                        <div class="table-bottom" style="min-height:1px;"></div>
                    {%/foreach}
                    
                    <div class="table-top">Edit VPS</div>
                    <table class="pure-table" style="width:100%;">
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
                            <td style="width:50%">IPv6 Allowed:</td>
                            <td>
                                <select id="AdminIPv6Allowed" style="width: 93%;">
                                    <option value="0" {%if isset|vps[ipv6] == true}{%if vps[ipv6] == 0}selected="selected"{%/if}{%/if}>No</option>
                                    <option value="1" {%if isset|vps[ipv6] == true}{%if vps[ipv6] == 1}selected="selected"{%/if}{%/if}>Yes</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div align="center"><button class="pure-button button-blue pure-button-primary" id="UpdateVPS">Update VPS</button></div>
                            </td>
                        </tr>
                    </table>
                    <div class="table-bottom" style="min-height:1px;"></div>
                    
                    <div class="table-top">
                        User VPS
                    </div>
                    <table class="pure-table" style="width:100%;">
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
                    <div class="table-bottom" style="min-height:1px;"></div>
                </div>
                <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1-2 right">
                    <div class="table-top">
                        Add IP Address
                    </div>
                    <table class="pure-table" style="width:100%;">
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
                                <div align="center"><button class="pure-button button-blue pure-button-primary" id="AddIPAddresses">Add IP Addresses</button></div>
                            </td>
                        </tr>
                    </table>
                    <div class="table-bottom" style="min-height:1px;"></div>
                    <div class="table-top">
                        Remove An IP
                    </div>
                    <table class="pure-table" style="width:100%;">
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
                                <div align="center"><button class="pure-button button-orange pure-button-primary" id="RemoveIPAddress">Remove IP Address</button></div>
                            </td>
                        </tr>
                    </table>
                    <div class="table-bottom" style="min-height:1px;"></div>
                    <div class="table-top">
                        Manually Assign IP
                    </div>
                    <table class="pure-table" style="width:100%;">
                        <tr>
                            <td style="width:50%">Assign IP:</td>
                            <td>
                                <input id="AssignIP" type="text" name="AssignIP" value="" style="width:90%" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div align="center"><button class="pure-button button-blue pure-button-primary" id="ManuallyAssignIP">Add IP Address</button></div>
                            </td>
                        </tr>
                    </table>
                    <div class="table-bottom" style="min-height:1px;"></div>
                    <div class="table-top">
                        Terminate VPS
                    </div>
                    <table class="hide-message pure-table" style="width:100%;">
                        <tr>
                            <td>
                                <label for="VerifyTerminate">
                                    <input type="checkbox" name="VerifyTerminate" id="VerifyTerminate" value="1" class="pure-checkbox"><b> I understand that this will completely destroy this poor user's pitiful VPS.</b>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div align="center"><button class="pure-button button-red pure-button-primary" id="Terminate">Terminate</button></div>
                            </td>
                        </tr>
                    </table>
                    <div class="table-bottom" style="min-height:1px;"></div>
                </div>
			</div>
        </div>
		{%/if}
        </div>
    </div><!-- End tabConWrap -->
</div>
{%/foreach}