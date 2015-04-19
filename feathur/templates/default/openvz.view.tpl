{%foreach vps in VPS}
<script type="text/javascript">
    var cpuTime = [];
    var cpuUsage = [];
    var ramTime = [];
    var ramUsage = [];
    var topProcesseshtml = "";

    var offlineData = {load_average:"N/A",uptime:"N/A",hostname:"Use the returned \"hostname\" variable instead!",primary_ip:"N/A",operating_system:"N/A",percent_cpu:0,percent_ram:0,percent_swap:0,percent_disk:0,percent_bandwidth:0,top:[{"name":"N/A","cpu":"N/A","ram":"N/A"},{"name":"N/A","cpu":"N/A","ram":"N/A"},{"name":"N/A","cpu":"N/A","ram":"N/A"},{"name":"N/A","cpu":"N/A","ram":"N/A"},{"name":"N/A","cpu":"N/A","ram":"N/A"},]};

    //fills the area chart arrays with 50 blank pieces of data
    for(i = 0; i < 50; i++){
        cpuTime.push('N/A');
        cpuUsage.push(0);

        ramTime.push('N/A');
        ramUsage.push(0);
    }

    function getlength(number) {
        return number.toString().length;
    }

    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
        var dataCPU = new google.visualization.DataTable();
        var dataRAM = new google.visualization.DataTable();

        dataCPU.addColumn('string', 'time');
        dataCPU.addColumn('number', 'Usage');

        dataRAM.addColumn('string', 'time');
        dataRAM.addColumn('number', 'Usage');

        //Add the data to the chart to be drawn
        for(i = 0; i< cpuTime.length; i++) {
            dataCPU.addRow([cpuTime[i], cpuUsage[i]]);
        }
        for(i = 0; i< ramTime.length; i++) {
            dataRAM.addRow([ramTime[i], ramUsage[i]]);
        }

        //Chart options
        var optionsCPU = {
            areaOpacity: '1',
            chartArea: {
                backgroundColor: 'none',
            },
            backgroundColor: {
                stroke: 'transparent',
                strokeWidth: '0',
                fill: 'transparent',
            },
            legend: 'none',
            vAxis: {
                minValue: '100',
                baselineColor: 'transparent',
                textStyle: {color: '#fff',},
                gridlines: {color: '#2B4C63'},
            },
            tooltip: { isHtml: true },
            colors:['#01e8f6']
        };
        var optionsRAM = {
            areaOpacity: '1',
            chartArea: {
                backgroundColor: 'none',
            },
            backgroundColor: {
                stroke: 'transparent',
                strokeWidth: '0',
                fill: 'transparent',
            },
            legend: 'none',
            vAxis: {
                minValue: '100',
                baselineColor: 'transparent',
                textStyle: {color: '#fff',},
                gridlines: {color: '#2B4C63'},
            },
            tooltip: { isHtml: true },
            colors:['#f5c802']
        };

        //Set the charts' data sources
        var chartCPU = new google.visualization.AreaChart(document.getElementById('chart_cpu'));
        var chartRAM = new google.visualization.AreaChart(document.getElementById('chart_ram'));

        //Draw the charts
        chartCPU.draw(dataCPU, optionsCPU);
        chartRAM.draw(dataRAM, optionsRAM);
    }

	$(document).ready(function() {

        $("#sidebar2").css("display","block");

        //creates sidebar html with blank values
        var sidebarhtml = '<div class="sb-title"><div class="loadingtxt"></div></div><div class="sb-lightbg"><div id="title-cpu" class="sb-progresstitle">CPU Load:<span>%</span></div><div class="progressContain"><div id="progress-cpu" class="progressbar"><div id="progress-cpu-overlay" class="progress-overlay"></div></div></div><div id="title-ram" class="sb-progresstitle">RAM Usage:<span>%</span></div><div class="progressContain"><div id="progress-ram" class="progressbar"><div id="progress-ram-overlay" class="progress-overlay"></div></div></div><div id="title-swap" class="sb-progresstitle">SWAP Usage:<span>%</span></div><div class="progressContain"><div id="progress-swap" class="progressbar"><div id="progress-swap-overlay" class="progress-overlay"></div></div></div><div id="title-disk" class="sb-progresstitle">Disk Usage:<span>%</span></div><div class="progressContain"><div id="progress-disk" class="progressbar"><div id="progress-disk-overlay" class="progress-overlay"></div></div></div><div id="title-bandwidth" class="sb-progresstitle">Bandwidth:<span>%</span></div><div class="progressContain"><div id="progress-bandwidth" class="progressbar"><div id="progress-bandwidth-overlay" class="progress-overlay"></div></div></div></div><br><div class="sb-smalltitle">Top Processes</div><div id="topprocesses"><table class="small-striped"><thead><tr><td>Name</td><td width="20%">CPU</td><td width="20%">RAM</td></tr></thead><tbody><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr></tbody></table></div><br><div class="sb-smalltitle">CPU</div><div id="chart_cpu" style="height: 80px;"></div><br><div class="sb-smalltitle">RAM</div><div id="chart_ram" style="height: 80px;"></div>';

        <!-- If the vps is NOT suspended -->
        {%if vps[suspended] == 0}
        var loadingtxtTimer = 1;
        var normalLoading = "Loading.";
        loading(1);

        $(".loadingtxt").html(normalLoading);
        setInterval(function() {
            $(".loadingtxt").append(".");
            loadingtxtTimer++;
            if(loadingtxtTimer >= 3)
            {
                $(".loadingtxt").html(normalLoading);
                loadingtxtTimer = 0;
            }
        }, 500);

        {%/if}
        <!-- /if -->

        <!-- If the vps is suspended -->
        {%if vps[suspended] != 0}
        //creates sidebar html with blank values
        var sidebarhtml = '<div class="sb-title"><div class="statuscoloroffline"></div> {%?vps[hostname]}</div><div class="sb-lightbg"><div id="title-cpu" class="sb-progresstitle">CPU Load:<span>%</span></div><div class="progressContain"><div id="progress-cpu" class="progressbar"><div id="progress-cpu-overlay" class="progress-overlay"></div></div></div><div id="title-ram" class="sb-progresstitle">RAM Usage:<span>%</span></div><div class="progressContain"><div id="progress-ram" class="progressbar"><div id="progress-ram-overlay" class="progress-overlay"></div></div></div><div id="title-swap" class="sb-progresstitle">SWAP Usage:<span>%</span></div><div class="progressContain"><div id="progress-swap" class="progressbar"><div id="progress-swap-overlay" class="progress-overlay"></div></div></div><div id="title-disk" class="sb-progresstitle">Disk Usage:<span>%</span></div><div class="progressContain"><div id="progress-disk" class="progressbar"><div id="progress-disk-overlay" class="progress-overlay"></div></div></div><div id="title-bandwidth" class="sb-progresstitle">Bandwidth:<span>%</span></div><div class="progressContain"><div id="progress-bandwidth" class="progressbar"><div id="progress-bandwidth-overlay" class="progress-overlay"></div></div></div></div><br><div class="sb-smalltitle">Top Processes</div><div id="topprocesses"><table class="small-striped"><thead><tr><td>Name</td><td width="20%">CPU</td><td width="20%">RAM</td></tr></thead><tbody><tr><td>Process</td><td>CPU</td><td>RAM</td></tr><tr><td>Process</td><td>CPU</td><td>RAM</td></tr><tr><td>Process</td><td>CPU</td><td>RAM</td></tr><tr><td>Process</td><td>CPU</td><td>RAM</td></tr><tr><td>Process</td><td>CPU</td><td>RAM</td></tr></tbody></table></div><br><div class="sb-smalltitle">CPU</div><div id="chart_cpu" style="height: 80px;"></div><br><div class="sb-smalltitle">RAM</div><div id="chart_ram" style="height: 80px;"></div>';

        $(".loadingtxt").html("Unavailable");
        $("#vpsStatus").html("Suspended");

        //Progress bar text percentages
        $(".vpsStat:nth-child(1) span").html("Suspended");
        $(".vpsStat:nth-child(1)").removeClass("online, offline").addClass("offline");
        $("#title-cpu span, .vpsStat:nth-child(2) span").html("N/A");
        $("#title-ram span, .vpsStat:nth-child(3) span").html("N/A");
        $("#title-swap span, .vpsStat:nth-child(4) span").html("N/A");
        $("#title-disk span, .vpsStat:nth-child(5) span").html("N/A");
        $("#title-bandwidth span, .vpsStat:nth-child(6) span").html("N/A");

        {%/if}
        <!-- /if -->
        $("#sidebar2").html(sidebarhtml);

        var getBgColor = function(percent){
            if(percent <= 50)
            {
                return "rgb(0, 255, 224)";//blue
            }
            else if(percent <= 75)
            {
                return "rgb(0, 255, 31)";//green
            }
            else if(percent <= 90)
            {
                return "rgb(255, 204, 0)";//yellow
            }
            else if(percent <= 100)
            {
                return "rgb(255, 173, 0)";//orange
            }else if(percent > 100)
            {
                return "rgb(255, 0, 0)";//red
            }
        }

        {%if vps[suspended] == 0}
        var updateAllStats = function(theData, hostName){
            //All stats with html that needs to be updated should be updated here.
            $("#stat-loadAverage td.info").html(theData.load_average);
            $("#stat-uptime td.info").html(theData.uptime);
            $("#stat-hostname td.info").html(hostName);
            $("#stat-primaryIP td.info").html(theData.primary_ip);
            $("#stat-operatingSystem td.info").html(theData.operating_system);

            //Sidebar title (server type and address)
            $(".sb-title").html("<div class='statuscolor"+ ((theData.uptime != "N/A") ? "online" : "offline") +"'></div>VPS ("+hostName+")");

            //Progress bar text percentages
            $(".vpsStat:nth-child(1) span").html(((theData.uptime != "N/A") ? "Online" : "Offline"));
            $(".vpsStat:nth-child(1)").removeClass("online, offline").addClass(((theData.uptime != "N/A") ? "online" : "offline"));
            $("#title-cpu span, .vpsStat:nth-child(2) span").html(theData.percent_cpu+"%");
            $("#title-ram span, .vpsStat:nth-child(3) span").html(theData.percent_ram+"%");
            $("#title-swap span, .vpsStat:nth-child(4) span").html(theData.percent_swap+"%");
            $("#title-disk span, .vpsStat:nth-child(5) span").html(theData.percent_disk+"%");
            $("#title-bandwidth span, .vpsStat:nth-child(6) span").html(theData.percent_bandwidth+"%");

            //Progress bars
            $("#progress-cpu").css("width",theData.percent_cpu+"%");
            $("#progress-cpu").css("max-width","100%");
            $("#progress-ram").css("width",theData.percent_ram+"%");
            $("#progress-ram").css("max-width","100%");
            $("#progress-swap").css("width",theData.percent_swap+"%");
            $("#progress-swap").css("max-width","100%");
            $("#progress-disk").css("width",theData.percent_disk+"%");
            $("#progress-disk").css("max-width","100%");
            $("#progress-bandwidth").css("width",theData.percent_bandwidth+"%");
            $("#progress-bandwidth").css("max-width","100%");

            //Progress bar colors
            $("#progress-cpu-overlay").css("background",getBgColor(theData.percent_cpu));
            $("#progress-ram-overlay").css("background",getBgColor(theData.percent_ram));
            $("#progress-swap-overlay").css("background",getBgColor(theData.percent_swap));
            $("#progress-disk-overlay").css("background",getBgColor(theData.percent_disk));
            $("#progress-bandwidth-overlay").css("background",getBgColor(theData.percent_bandwidth));

            //Area Charts
            var date = new Date;
            var second = date.getSeconds();
            var minute = date.getMinutes();
            var hour = date.getHours();

            //Removes the earliest entry of the charts
            cpuTime.shift();
            cpuUsage.shift();
            ramTime.shift();
            ramUsage.shift();

            topProcesseshtml = "";

            for(i = 0; i < theData.top.length; i++){
                var index = i + 1;
                topProcesseshtml += "<tr><td>"+theData.top[i].name+"</td><td>"+theData.top[i].cpu+"</td><td>"+theData.top[i].ram+"</td></tr>";
                //console.log("Found data for top processes!");
                //console.log("Found stat data" + theData.top[i].name);
            };
            $("#topprocesses table tbody").html(topProcesseshtml);

            //Just makes the time somewhat pretty
            var timeformat = ":" + ((getlength(minute) == 1) ? "0" : "") + minute + ":" + ((getlength(second) == 1) ? "0" : "") + second;
            var time = ((hour < 12 || hour == 0) ?  hour + timeformat + " AM" : (hour % 12) + timeformat + " PM");

            cpuTime.push(time);
            ramTime.push(time);

            cpuUsage.push(theData.percent_cpu);
            ramUsage.push(theData.percent_ram);

            drawChart();
        };
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
                                    //console.log("Tried to load the statistics data 3 times but got nothing back. Please try again by reloading this page. Contact support if this error persists.");
                                    retryTimeoutSet = true;
                                    alert("Tried to load the statistics data 3 times but got nothing back. Please try again by reloading this page. Contact support if this error persists.");
                                    return;
                                }
                                //console.log("Failed to load data. Retrying.");
                                uptime();
                            } else {
                                //console.log("Hostname found!");
                                $("#vpsStatus").html('Currently '+ data.result.toUpperCase());
                                if(data.statistics){
                                    //console.log("Successfully loaded data!");
                                    updateAllStats(data.statistics.info, data.hostname);
                                } else {
                                    //console.log("No statistics data found!");
                                    updateAllStats(offlineData, data.hostname);
                                }
                                loading(0);
                            }
                        })
                        .fail(function() {
                            //console.log("Did not get data.");
                            receivedData = true;
                        });
                    });
                }else{
                    //console.log("Have not yet recieved data; Waiting.");
                }
            }
		}
		setInterval(uptime, 10000);
		uptime();
        {%/if}

        $(".GenericAction").click(function(e) {
			loading(1);
			var action = $(this).attr('value');
			$.getJSON("view.php?id={%?vps[id]}&action=" + action,function(result){
                loading(0);
                setNotice("#GeneralNotice",result.result, result.type);
				if(result.reload == 1){
					location.reload();
				} else {
					uptime();
				}
			});
		});

		$("#ChangePassword").click(function(e) {
            e.preventDefault();
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
		$("#ChangePrimaryIP").click(function(e) {
            e.preventDefault();
			loading(1);
			var ipaddress = $('#SelectedIP').prop('value');
			$.getJSON("view.php?id={%?vps[id]}&action=primaryip&ip=" + ipaddress,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#RDNSIP").change(function(e) {
            e.preventDefault();
			var ipid = $('#RDNSIP').prop('value');
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
				setNotice("#StatusNotice", result.result, result.type);
			});
		});
		{%if vps[ipv6] == 1}
			{%if isempty|IPv6Exist == true}
				$("#RequestBlock").click(function(e) {
                    e.preventDefault();
					loading(1)
					$.getJSON("view.php?id={%?vps[id]}&action=requestblock",function(result){
                        loading(0);
						$('#IPv6Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
						if(result.reload == 1){
							location.reload();
						}
					});
				});
			{%/if}
			{%if isempty|IPv6Exist == false}
				$("#AddIPv6").click(function(e) {
                    e.preventDefault();
					var blockid = $('#AddIPv6').prop('value');
					loading(1);
					$.getJSON("view.php?id={%?vps[id]}&action=addipv6&block=" + blockid,function(result){
                        loading(0);
						$('#IPv6Notice').html('<div class="alert ' + result.type + 'box">' + result.result + '</div>');
						if(result.reload == 1){
							location.reload();
						}
					});
				});
			{%/if}
		{%/if}
		$("#ChangeHostname").click(function(e) {
            e.preventDefault();
			loading(1);
			var hostname = $('#Hostname').val();
			$.getJSON("view.php?id={%?vps[id]}&action=hostname&hostname=" + hostname,function(result){
                loading(0);
				setNotice("#SettingsNotice",result.result, result.type);
			});
		});
		$("#TunTap").click(function(e) {
            e.preventDefault();
			var tuntap = $('#TunTapValue').text();
			loading(1);
			if (tuntap == 1) {
				$.getJSON("view.php?id={%?vps[id]}&action=tuntap&setting=0",function(result){
                    loading(0);
					setNotice("#SettingsNotice",result.result, result.type);
					$('#TunTapValue').html(0);
					$('#TunTapButton').addClass('button-green').removeClass('button-red');
					$('#TunTap').text("Enable Tun/Tap");
				});
			} else {
				$.getJSON("view.php?id={%?vps[id]}&action=tuntap&setting=1",function(result){
                    loading(0);
					setNotice("#SettingsNotice",result.result, result.type);
					$('#TunTapValue').html(1);
					$('#TunTapButton').addClass('button-red').removeClass('button-green');
					$('#TunTap').text("Disable Tun/Tap");
				});
			}
		});
		$("#PPP").click(function(e) {
            e.preventDefault();
			var ppp = $('#PPPValue').text();
			loading(1);
			if (ppp == 1) {
				$.getJSON("view.php?id={%?vps[id]}&action=ppp&setting=0",function(result){
                    loading(0);
					setNotice("#SettingsNotice",result.result, result.type);
					$('#PPPValue').html(0);
					$('#PPPButton').addClass('button-green').removeClass('button-red');
					$('#PPP').text("Enable PPP");
				});
			} else {
				$.getJSON("view.php?id={%?vps[id]}&action=ppp&setting=1",function(result){
                loading(0);
					setNotice("#SettingsNotice",result.result, result.type);
					$('#PPPValue').html(1);
					$('#PPPButton').addClass('button-red').removeClass('button-green');
					$('#PPP').text("Disable PPP");
				});
			}
		});
		$("#IPTables").click(function(e) {
            e.preventDefault();
			var iptables = $('#IPTablesValue').text();
			loading(1);
			if (iptables == 1) {
				$.getJSON("view.php?id={%?vps[id]}&action=iptables&setting=0",function(result){
                    loading(0);
					setNotice("#SettingsNotice",result.result, result.type);
					$('#IPTablesValue').html(0);
					$('#IPTablesButton').addClass('button-green').removeClass('button-red');
					$('#IPTables').text("Enable IP Tables");
				});
			} else {
				$.getJSON("view.php?id={%?vps[id]}&action=iptables&setting=1",function(result){
                    loading(0);
					setNotice("#SettingsNotice",result.result, result.type);
					$('#IPTablesValue').html(1);
					$('#IPTablesButton').addClass('button-red').removeClass('button-green');
					$('#IPTables').text("Disable IPTables");
				});
			}
		});
		$("#Rebuild").click(function(e) {
            e.preventDefault();
			loading(1);
			var template = $('#SelectedTemplate').prop('value');
			var rebuildpassword = $('#RebuildPassword').val();
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
								setNotice("#RebuildNotice",result.result, result.type);
							} else {
								$('#page').html(result.result);
                                $('#sidebar2').css("display","none");
                                $('#page-wrapper').css("width","100%");
							}
						}
					});
				} else {
                    setNotice("#RebuildNotice","You must enter a password to rebuild!", "error");
				}
			} else {
                setNotice("#RebuildNotice","You must check the verification box to rebuild!", "error");
			}
            loading(0);
		});
		$("#ConsoleInput").keypress(function(event) {
			if (event.which == 13) {
				$("#ConsoleLoading").css({visibility: "visible"});
				var input = $('#ConsoleInput').val();
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
			$("#UpdateVPS").click(function(e) {
                e.preventDefault();
				loading(1);
				var ram = $('#AdminRAM').val();
				var swap = $('#AdminSWAP').val();
				var disk = $('#AdminDisk').val();
				var cpuunits = $('#AdminCPUUnits').val();
				var cpulimit = $('#AdminCPULimit').val();
				var bandwidthlimit = $('#AdminBandwidthLimit').val();
				var ipv6allowed = $('#AdminIPv6Allowed').prop('value');
				var inodes = $('#AdminInodes').val();
				$.getJSON("view.php?id={%?vps[id]}&action=update&ram=" + ram + "&swap=" + swap + "&disk=" + disk + "&cpuunits=" + cpuunits + "&cpulimit=" + cpulimit + "&bandwidth=" + bandwidthlimit + "&inodes=" + inodes + "&ipv6allowed=" + ipv6allowed,function(result){
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
</script>
<div class="pure-u-1">
    <div class="tabs primarytabs">
        <div class="tab nth cur" data-tab="tab-1"><span>General</span><i class="fa fa-bar-chart"></i></div>
        <div class="tab nth" data-tab="tab-2"><span>Settings</span><i class="fa fa-cogs"></i></div>
        {%if vps[ipv6] == 1}{%if isempty|IPv6Exist == false}<div class="tab nth" data-tab="tab-3"><span>IPv6</span><i class="fa fa-table"></i></div>{%/if}{%/if}
        <div class="tab nth" data-tab="tab-4"><span>Rebuild</span><i class="fa fa-wrench"></i></div>
        <div class="tab nth" data-tab="tab-5"><span>Command Center</span><i class="fa fa-terminal"></i></div>
        <div class="tab nth" data-tab="tab-6"><span>Console</span><i class="fa fa-desktop"></i></div>
        {%if UserPermissions == 7}<div class="tab nth" data-tab="tab-7"><span>Admin</span><i class="fa fa-key"></i></div>{%/if}
    </div>

    <div id="mobileVPSStats">
        <div class="vpsStat pure-u-1-2">Server <span></span></div>
        <div class="vpsStat pure-u-1-2">CPU Load: <span></span></div>
        <div class="vpsStat pure-u-1-2">Ram Usage: <span></span></div>
        <div class="vpsStat pure-u-1-2">SWAP Usage: <span></span></div>
        <div class="vpsStat pure-u-1-2">Disk Usage: <span></span></div>
        <div class="vpsStat pure-u-1-2">Bandwidth: <span></span></div>
    </div>

    <div id="tabConWrap">
        <div class="tabCon tab-1 cur">
            <div id="tabConTxt" class="noBorder">
            	<div class="pure-u-1">
                	<div id="GeneralNotice"></div>
                </div>
                <div id="vpsStatus" class="pure-u-1" style="text-align:center; font-weight:bold;font-size: 14px;color:#a8a8a8;">Loading Status</div>
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
                            <div class="vpsbutton black GenericAction" value="kill"><i class="fa fa-times"></i><p class="vpsbtnname">Kill</p></div>
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
                    </div>
                </div>
                <div class="pure-u-1">
                    <div id="vpsStatus" class="pure-u-1" style="text-align:center; font-weight:bold;font-size: 14px;color:#a8a8a8;">VPS Information</div>
                    <br><br>
                    <div class="whitebox pure-u-1">
                        <table id="vpsinfo" class="striped-table" style="width:100%;">
                            <tr id="stat-loadAverage">
                                <td class="pure-u-1-2">Load Average:</td>
                                <td class="info pure-u-1-2"><span class="loadingtxt"></span></td>
                            </tr>
                            <tr id="stat-uptime">
                                <td class="pure-u-1-2">Uptime:</td>
                                <td class="info pure-u-1-2"><span class="loadingtxt"></span></td>
                            </tr>
                            <tr id="stat-hostname">
                                <td class="pure-u-1-2">Hostname:</td>
                                <td class="info pure-u-1-2"><span class="loadingtxt"></span></td>
                            </tr>
                            <tr id="stat-primaryIP">
                                <td class="pure-u-1-2">Primary IP:</td>
                                <td class="info pure-u-1-2"><span class="loadingtxt"></span></td>
                            </tr>
                            <tr id="stat-operatingSystem">
                                <td class="pure-u-1-2">Operating System:</td>
                                <td class="info pure-u-1-2"><span class="loadingtxt"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div id="vpsinfoProgressbars"></div>
                </div>
            </div>
         </div>

        <div class="tabCon tab-2">
            <div id="tabConTxt" class="whitebox">
            	<div class="pure-u-1">
			<div id="SettingsNotice"></div>
		</div>
                <form class="whitebox pure-form pure-form-stacked pure-u-1 pure-g">
                    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2 left">
                        <div class="pure-control-group">
                            <label for="password">New Root Password</label>
                            <input id="password" type="password" name="password" style="margin-right: 10px;" class="pure-u-sm-1 pure-u-lg-1-2">
                            <button class="pure-button button-large pure-button-primary" id="ChangePassword" style="margin-top: 4px;">Change Password</button>
                        </div>

                        <br>
                        <div class="pure-control-group">
                            <label for="SelectedIP">Primary IP:</label>
                            <select id="SelectedIP" style="margin-right: 10px;" class="pure-u-sm-1 pure-u-lg-1-2">
                                {%if isset|IPs == true}
                                    {%foreach ip in IPs}
                                        <option value="{%?ip[id]}" {%if ip[primary] == 1}selected="selected"{%/if}>{%?ip[ip]}</option>
                                    {%/foreach}
                                {%/if}
                            </select>
                            <button class="pure-button pure-button-primary button-large" id="ChangePrimaryIP" style="margin-top: 4px;">Change Primary IP</button>
                        </div>

                        <br>
                        <div class="pure-control-group">
                            <label for="Hostname">Hostname:</label>
                            <input id="Hostname" type="text" name="Hostname" value="{%?vps[hostname]}" style="margin-right: 10px;" class="pure-u-sm-1 pure-u-lg-1-2"/>
                            <button class="pure-button pure-button-primary button-large" id="ChangeHostname" style="margin-top: 4px;">Change Hostname</button>
                        </div>

                        <br>
                        <div class="pure-control-group pure-g">
                            <fieldset>
                                <div class="pure-u-1 pure-u-lg-1-2" style="float: left;">
                                    <label for="RDNSIP" class="inlineB pure-u-1" style="margin-right: 160px;">Select IP:</label><br>
                                    <select id="RDNSIP" style="margin-right: 10px;min-width: 100%;" class="pure-u-1">
                                        <option selected="selected">Select An IP</option>
                                        {%if isset|IPs == true}
                                            {%foreach ip in IPs}
                                                <option value="{%?ip[id]}">{%?ip[ip]}</option>
                                            {%/foreach}
                                        {%/if}
                                    </select>
                                </div>
                                <div class="pure-u-1 pure-u-lg-1-2">
                                    <label for="RDNSValue" class="inlineB pure-u-1">rDNS Entry:</label>
                                    <input id="RDNSValue" type="text" name="RDNSValue" style="margin-right: 10px;min-width: 100%;" class="pure-u-1"/>
                                    <div style="visibility:hidden;" id="RDNSButton">
                                        <button class="pure-button pure-button-primary button-large" id="UpdateRDNS" style="margin-top: 4px;">Update rDNS</button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 right">
                        <div id="TunTapValue" style="display:none;">
                                {%if isempty|vps[tuntap] == true}0{%/if}
                                {%if isempty|vps[tuntap] == false}1{%/if}
                        </div>
                        <div id="PPPValue" style="display:none;">
                            {%if isempty|vps[ppp] == true}0{%/if}
                            {%if isempty|vps[ppp] == false}1{%/if}
                        </div>
                        <div id="IPTablesValue" style="display:none;">
                            {%if isempty|vps[iptables] == true}0{%/if}
                            {%if isempty|vps[iptables] == false}1{%/if}
                        </div>

                        <div class="pure-control-group" style="display:block;">
                            <div class="pure-u-1 nofluid">
                                <div id="TunTapButton" class="pure-button button-inline button-xlarge button-{%if isempty|vps[tuntap] == true}green{%/if}{%if isempty|vps[tuntap] == false}red{%/if}">
                                    <a href="#" id="TunTap" style="color:#FFFFFF;" class="pure-u-1">
                                        {%if isempty|vps[tuntap] == true}Enable TunTap{%/if}
                                        {%if isempty|vps[tuntap] == false}Disable TunTap{%/if}
                                    </a>
                                </div>
                            </div><br>
                            <div class="pure-u-1 nofluid">
                                <div id="PPPButton" class="pure-button button-inline button-xlarge button-{%if isempty|vps[ppp] == true}green{%/if}{%if isempty|vps[ppp] == false}red{%/if}">
                                    <a href="#" id="PPP" style="color:#FFFFFF;" class="pure-u-1">
                                        {%if isempty|vps[ppp] == true}Enable PPP{%/if}
                                        {%if isempty|vps[ppp] == false}Disable PPP{%/if}
                                    </a>
                                </div>
                            </div><br>
                            <div class="pure-u-1 nofluid">
                                <div id="IPTablesButton" class="pure-button button-inline button-xlarge button-{%if isempty|vps[iptables] == true}green{%/if}{%if isempty|vps[iptables] == false}red{%/if}">
                                    <a href="#" id="IPTables" style="color:#FFFFFF;" class="pure-u-1">
                                        {%if isempty|vps[iptables] == true}Enable IP Tables{%/if}
                                        {%if isempty|vps[iptables] == false}Disable IP Tables{%/if}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
         </div>

        <div class="tabCon tab-3">
            <div id="tabConTxt" class="whitebox">
            {%if vps[ipv6] == 1}
			{%if isempty|IPv6Exist == false}
				<div id="tabs-3" style="height:600px;">
					<div class="pure-u-1">
						<div id="IPv6Notice"></div>
					</div>
					<br>
					<div style="z-index: 500;text-align:left;" class="notice">
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
									<div class="box-100">
										<div class="title">
											<h3>{%?block[prefix]}{%?block[size]} Management</h3>
											<div class="shortcuts-icons">
												<a class="shortcut tips" id="AddIPv6" title="Add IPv6 Address" value="{%?block[id]}"><i class="fa fa-plus-circle"></i></a>
											</div>
										</div>
										<table class="pure-table">
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
            </div>
        </div>
        <div class="tabCon tab-4">
            <div id="tabConTxt">
            	<div class="pure-u-1">
                	<div id="RebuildNotice"></div>
                </div>
                <div align="center" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1">
				<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2">
					<h3>Rebuild</h3>
                    <form class="pure-form pure-form-aligned outlined">
                        <div class="pure-control-group">
                            <label for="SelectedTemplate">Select Template:</label>
                            <select id="SelectedTemplate" class="chosen-select" style="height:38px;min-width:200px;">
                                {%if isset|Templates == true}
									{%foreach template in Templates}
										<option value="{%?template[id]}" {%if template[primary] == 1}selected="selected"{%/if}>{%?template[name]}</option>
									{%/foreach}
								{%/if}
                            </select>
                        </div>

                        <div class="pure-control-group">
                            <label for="RebuildPassword">New Root Password:</label>
                            <input id="RebuildPassword" type="password" name="password" style="min-width:200px;">
                        </div>

                        <div class="pure-controls" style="margin:0;">
                            <div class="formnote">
                                <label for="VerifyRebuild" class="pure-checkbox">
                                    <input type="checkbox" name="VerifyRebuild" id="VerifyRebuild" value="1"> I understand that this procedure will destroy my VPS and all data on it, and start with a desired template.
                                </label>
                                <br>
                                <button class="pure-button pure-button-primary button-xlarge button-red" id="Rebuild">Rebuild</button>
                            </div>
                        </div>
                    </form>
				</div>
			</div>
            </div>
        </div>
        <div class="tabCon tab-5">
            <div id="tabConTxt">
                <div align="center" style="width:100%">
				<div class="alert warningbox static-alert">
		            <p><b>Command Center Notes</b></p><p>You can use this command center to issue commands to your VPS even if you can't connect via SSH.<br><strong>Notice:</strong> Commands issued are not successive. Each command is executed independently.<br>You can issue successive commands by putting a ; between them. (Eg: cd /var; ls}<br>To start SSH on most systems you can type: service ssh start -OR- service sshd start</p>
				</div><br><br>
                <div class="whitebox">
                    <div class="pure-u-1">
                        <div style="height: 280px;background: #0c161c;color: #FFF;overflow-x: hidden;overflow-y: visible;padding: 10px;border-radius: 3px;box-sizing: border-box;text-align: left;" id="ConsoleOutput"></div>

                        <div style="background: #0c161c;color: #FFFFFF;height: 38px;border-radius: 3px 0px 0 3px;float: left;width: 27px;margin-top:6px;box-shadow: 1px 0 #050C11;">
                            <div style="font-size: 19px;padding-left: 4px;padding-top: 10px;text-align: center;visibility: hidden;z-index: -1;" id="ConsoleLoading">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                        </div>

                        <input id="ConsoleInput" class="pure-u-1" type="text" name="ConsoleInput" style="background: #0c161c;color: #fff;margin-top: 6px;height: 38px;float: right;width: calc(100% - 27px);border-radius:0 3px 3px 0 !important;" placeholder="Type commands here"/>
                    </div>
                </div>
			</div>
            </div>
        </div>

        <div class="tabCon tab-6">
            <div id="tabConTxt">
                <iframe src="console.php?id={%?vps[id]}" width="100%" height="500" frameborder="0" scrolling="no" style="background: #fff;"></iframe>
            </div>
        </div>
        {%if UserPermissions == 7}
        <div class="tabCon tab-7">
            <div id="tabConTxt" class="pure-g">
            	<div class="pure-u-1">
                	<div id="AdminNotice"></div>
                </div>
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
                        Transfer
                    </div>
                    <table class="pure-table" style="width:100%;">
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
                                <div align="center"><button class="pure-button button-blue pure-button-primary" id="TransferVPS">Transfer VPS</button></div>
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
</div>
{%/foreach}
