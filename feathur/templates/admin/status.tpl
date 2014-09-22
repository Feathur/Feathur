<div class="pure-u-1">
    <div class="inset-text" style="line-height: 29px;">Welcome to Feathur. On this page, you can view the statuses of all of the servers tied to this account. Click on one to view more information.
    </div>
</div>
<br>
<div class="pure-u-1 well">
    <h2 class="timer"><i class="fa fa-clock-o"></i> Status last updated <a id="timer" style="white-space:nowrap;">0</a> seconds ago</h2>
    <div id="timerStats" class="smalltext"><span></span> of <span></span> servers online&nbsp;&nbsp;-&nbsp;&nbsp;<span></span> with high bandwidth usage&nbsp;&nbsp;-&nbsp;&nbsp;<span></span> servers on account</div>
</div>

<div id="errorcontain" class="pure-u-1 pure-g">
    <div class="pure-u-md-1 pure-u-lg-1 pure-u-xl-1-3">
        <div class="alert errorbox static-alert" style="display: none;">
            Server(s) Down: <p class="inlineB"></p>
        </div>
    </div>
    <div class="pure-u-md-1 pure-u-lg-1 pure-u-xl-1-3">
        <div class="alert warningbox 1 static-alert" style="display: none;">
            High bandwidth usage on: <p class="inlineB"></p>
        </div>
    </div>
    {%if isempty|TemplatesRedone == true}
        <div class="pure-u-md-1 pure-u-lg-1 pure-u-xl-1-3">
            <div class="alert warningbox 2 static-alert">
                We have updated the template system. As part of that unfortunately all existing template database entries have been removed from the system. The table has been backed up as 'templates_old'. Any templates on the hard disk have been left where they are for the time being. The new system uses URL based downloads instead of rsync or localized downloads. You can disable this message in <a href="admin.php?view=settings">settings</a>.
                <br>
            </div>
        </div>
    {%/if}
</div>

<div id="serverstatscontain" class="pure-u-1">

<script type="text/javascript">
    var prevServerCount = 0;
    var curServerCount = 0;

    loading(1);
	var counttx = 0;
	google.load('visualization', '1.0', {'packages': ['corechart']});
    $(document).ready(function () {
		function timerrx() {
            counttx = counttx + 1;
            $('#timer').html(counttx);
        }
		function drawChart(val) {
            //if(curServerCount < 51 || curServerCount > prevServerCount) {
                // Create the data table.
                chartdat = google.visualization.arrayToDataTable([
                    ['A', 'B'],
                    ['Used', val.ram_usage ],
                    ['Free', val.ram_free ]
                ]);
                // Create the data table.
                chartdat2 = google.visualization.arrayToDataTable([
                    ['A', 'B'],
                    ['Used', val.disk_usage ],
                    ['Free', val.disk_free ]
                ]);

                // Set chart options
                chartopt = {
                    title: '',
                    legend: 'none',
                    pieSliceText: 'none',
                    enableInteractivity: 'false',
                    backgroundColor: 'transparent',
                    chartArea: {width: '105', height: '105'},
                    pieSliceBorderColor: 'transparent',
                    slices: {
                        0: { color: '#7ce2e8' },
                        1: { color: '#335367' }
                    }
                };
                // Set chart options
                chartopt2 = {
                    title: '',
                    legend: 'none',
                    pieSliceText: 'none',
                    enableInteractivity: 'false',
                    backgroundColor: 'transparent',
                    chartArea: {width: '105', height: '105'},
                    pieSliceBorderColor: 'transparent',
                    slices: {
                        0: { color: '#7ce2e8' },
                        1: { color: '#335367' }
                    }
                };
                
                //Don't draw graphs if the screen isn''t big enough to show them
                if($(window).width() > 529) {
                    var chart = new google.visualization.PieChart(document.getElementById('memChart' + val.id));
                    chart.draw(chartdat, chartopt);
                    var chart2 = new google.visualization.PieChart(document.getElementById('diskChart' + val.id));
                    chart2.draw(chartdat2, chartopt2);
                }
            //}
		}
		
		function uptime() {
			$.getJSON("admin.php?json-variables=1", function (result) {
                var addserverhtml = '<a href="admin.php?view=addserver"><div id="addServer"><b>+</b></div></a>';
                var html = '';
                var srvtype = ''
                var ipcount = 0;
				var servercount = result.servers.length;
                curServerCount = servercount;
                var serversoffline = [];
                var bandwidthstr = '';
                var servershighbandwidth = [];
				if (servercount == 0) {
                    html += '<br><br>'
                        + '<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-3">'
                        + '<div align="center" class="whitebox">'
                        + '<p>Add a server to Feathur so updates will appear here.</p>'
                        + '</div></div>';
                    return;
                } else {
					$.each(result.servers, function (i, val) {
                        //alert(val.load);
                        srvtype = val.type;
                        ipcount = val.ip_count;
                        if(srvtype == "openvz"){srvtype = "OpenVZ"}else{srvtype = "KVM"}
                        if(ipcount == null){ipcount = 0}
						if (val.status == true) {
                            var bandwidthstr = val.bandwidth;
                            bandwidthstr = bandwidthstr.replace("Mbps","");
                            if(bandwidthstr >= 100){servershighbandwidth.push(val.name);}
                            if(ipcount == null){ipcount = 0}
							html += '<!--div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-3"--><a href="admin.php?view=list&type=search&search=server='+val.id+'"><div class="fluidbox statusbox">'
								+ '<div style="width: 190px;float: left;">'
								+ '<div class="statuscoloronline">'
								+ '</div>'
								+ '<b><h3>' + val.name + '</b> (IPs: ' + ipcount + ')</h3><b>Virtualization: ' + srvtype + '</b><br><br>'
								+ '<div class="statusTxtInfo" style="width: 100%;">'
								+ 'Load: <b class="inlineB"><p>' + val.load_average + '</p></b><br>'
								+ 'Bandwidth: <b class="inlineB"><p>' + val.bandwidth + '</p></b><br>'
								+ 'Uptime: <b class="inlineB"><p>' + val.uptime + '</p></b>'
								+ '</div></div>'
								+ '<div style="float:right;">'
								+ '<div class="statuschartname inlineB">Memory</div><div class="statuschartname inlineB">Disk</div><br>'
								+ '<div class="statuschartpercent"><p class="srvstatmemusg">' + val.ram_usage + '%</p>Used</div><div class="statuschartpercent"><p class="srvstatdskusg">' + val.disk_usage + '%</p>Used</div><br>'
								+ '<div id="memChart' + val.id + '" class="statuschart inlineB" style="width: 105px; height: 105px;"><div class="statuschartinner"></div></div>'
								+ '<div id="diskChart' + val.id + '" class="statuschart inlineB" style="width: 105px; height: 105px;"><div class="statuschartinner"></div></div>'
								+ '</div></div></div><!--/div-->';
						} else {
                            serversoffline.push(val.name);
							html += '<!--div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-3"--><a href="admin.php?view=list&type=search&search=server='+val.id+'"><div class="fluidbox statusbox animated fadeIn">'
								+ '<div style="width: 190px;float: left;">'
								+ '<div class="statuscoloroffline"></div>'
								+ '<b><h3>' + val.name + '</b> (IPs: ' + ipcount + ')</h3><b>Virtualization: ' + srvtype + '</b><br><br>'
								+ '<div class="statusTxtInfo">'
								+ 'Load: <b>0.00</b><br>'
								+ 'Bandwidth: <b>0.00 Mbps</b><br>'
								+ 'Uptime: <b>OFFLINE</b>'
								+ '</div>'
								+ '</div>'
								+ ' <div style="float:right;">'
								+ '<div class="statuschartname">Memory</div><div class="statuschartname inlineB">Disk</div><br>'
								+ '<div class="statuschartpercent null inlineB"><b>N/A</b></div><div class="statuschartpercent null inlineB"><b>N/A</b></div><br>'
								+ '<div class="nullChart statuschart inlineB" style="width: 105px; height: 105px;margin-right: 3px;"><div style="display:none"></div><div style="display:none"></div><div></div></div>'
								+ '<div class="nullChart statuschart inlineB" style="width: 105px; height: 105px;"><div style="display:none"></div><div style="display:none"></div><div></div></div>'
								+ '</div></div></div><!--/div-->';
						}
					});
				}
                
                if(serversoffline != "")
                {
                    $("#errorcontain .errorbox p.inlineB").empty();
                    $("#errorcontain .errorbox p.inlineB").html(serversoffline.join(", "));
                    $("#errorcontain .errorbox").css("display","inline-block");
                }else{
                    $("#errorcontain .errorbox").css("display","none");
                }
                if(servershighbandwidth != "")
                {
                    $("#errorcontain .warningbox.1 p.inlineB").empty();
                    $("#errorcontain .warningbox.1 p.inlineB").html(servershighbandwidth.join(", "));
                    $("#errorcontain .warningbox.1").css("display","inline-block");
                }else{
                    $("#errorcontain .warningbox.1").css("display","none");
                }
                html += addserverhtml;
                $("#serverstatscontain").empty();
                $("#serverstatscontain").html(html);
				if (servercount != 0) {
					$.each(result.servers, function (i, val) {
						if (val.status == true) {
							drawChart(val);
						}
					});
				}
                $("#timerStats span:nth-child(1)").html(servercount - serversoffline.length);
                $("#timerStats span:nth-child(2), #timerStats span:nth-child(4)").html(servercount);
                $("#timerStats span:nth-child(3)").html(servershighbandwidth.length);
			});
            counttx = 0;
            loading(0);
            prevServerCount = curServerCount;
		}
		
		uptime();
		setInterval(uptime, 10000);
        setInterval(timerrx, 1000);
	});
</script>
{%if isset|Statistics == false}
<br><br>
<div align="center" class="pure-u-1">
    Add a server to Feathur so updates will appear here.
</div>
{%/if}
</div>