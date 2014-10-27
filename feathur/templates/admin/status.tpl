<div class="pure-u-1">
    <div class="inset-text" style="line-height: 29px;">Welcome to Feathur. On this page, you can view the statuses of all of the servers. Click on one to view more information.
    </div>
</div>
<br>
<div class="pure-u-1 well">
    <h2 class="timer"><i class="fa fa-clock-o"></i> Status last updated <a id="timer" style="white-space:nowrap;">0</a> seconds ago</h2>
    <div id="timerStats" class="smalltext"><span></span> of <span></span> servers online&nbsp;&nbsp;-&nbsp;&nbsp;<span></span> with high bandwidth usage&nbsp;&nbsp;-&nbsp;&nbsp;<span></span> servers total</div>
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
    
    var addserverhtml = '<a href="admin.php?view=addserver"><div id="addServer"><b>+</b></div></a>';
    
    var getBgColor = function(percent){
        if(percent <= 50){return "rgb(0, 255, 224)";} //blue
        else if(percent <= 75){return "rgb(0, 255, 31)";} //green
        else if(percent <= 90){return "rgb(255, 204, 0)";} //yellow
        else if(percent <= 100){return "rgb(255, 173, 0)";} //orange
        else if(percent > 100){return "rgb(255, 0, 0)";} //red
    }
    
    loading(1);
	var counttx = 0;
    $(document).ready(function () {
		function timerrx() {
            counttx = counttx + 1;
            $('#timer').html(counttx);
        }
		
		function uptime() {
            if(!$('#serverstatscontain a[href*="addserver"]').length){$("#serverstatscontain").html(addserverhtml)};
			$.getJSON("admin.php?json-variables=1", function (result) {
                var html = '';
                var srvtype = ''
                var ipcount = 0;
				var servercount = result.servers.length;
                curServerCount = servercount;
                var serversoffline = [];
                var bandwidthstr = '';
                var servershighbandwidth = [];
                
				if (servercount == 0) {
                    html += addserverhtml;
                    return;
                } else {
					$.each(result.servers, function (i, val) {
                        //alert(val.load);
                        srvtype = val.type;
                        ipcount = val.ip_count;
                        if(srvtype == "openvz"){srvtype = "OpenVZ"}else if(srvtype == "kvm"){srvtype = "KVM"}else{srvtype = srvtype};
                        if(ipcount == null){ipcount = 0}
                        
                        //Check if statusbox is in DOM already, if not, create it
                        if($(".statusbox.status-"+val.id+"").length) {
                            //console.log("found element for ID:"+val.id);
                            if (val.status == true) { // If online replace old data with new data
                                var bandwidthstr = val.bandwidth;
                                if(bandwidthstr >= 100){servershighbandwidth.push(val.name);}
                                $(".statusbox.status-"+val.id+" .statuscolor").attr("class", "statuscolor statuscoloronline");
                                $(".statusbox.status-"+val.id+" .stat-virtualization").html(srvtype);
                                $(".statusbox.status-"+val.id+" .stat-ram").html("RAM: "+val.ram_usage+"%");
                                $(".statusbox.status-"+val.id+" .stat-disk").html("Disk: "+val.disk_usage+"%");
                                $(".statusbox.status-"+val.id+" .stat-name").html(val.name+" (IPs: "+ipcount+")");
                                $(".statusbox.status-"+val.id+" .mem-prog").css("width",val.ram_usage+"%");
                                $(".statusbox.status-"+val.id+" .disk-prog").css("width",val.disk_usage+"%");
                                $(".statusbox.status-"+val.id+" .stat-load").html(val.load_average);
                                $(".statusbox.status-"+val.id+" .stat-bandwidth").html(val.bandwidth);
                                $(".statusbox.status-"+val.id+" .stat-uptime").html(val.uptime);
                                $(".statusbox.status-"+val.id+" .progress-ram-overlay").css("background",getBgColor(val.ram_usage));
                                $(".statusbox.status-"+val.id+" .progress-disk-overlay").css("background",getBgColor(val.disk_usage));
                            } else {// If offline replace with filler data unless the data is able to be found
                                serversoffline.push(val.name);
                                $(".statusbox.status-"+val.id+" .statuscolor").attr("class", "statuscolor statuscoloroffline");
                                $(".statusbox.status-"+val.id+" .stat-virtualization").html(srvtype);
                                $(".statusbox.status-"+val.id+" .stat-ram").html("RAM: N/A");
                                $(".statusbox.status-"+val.id+" .stat-disk").html("Disk: N/A");
                                $(".statusbox.status-"+val.id+" .stat-name").html(val.name+" (IPs: "+ipcount+")");
                                $(".statusbox.status-"+val.id+" .mem-prog").css("width","0%");
                                $(".statusbox.status-"+val.id+" .disk-prog").css("width","0%");
                                $(".statusbox.status-"+val.id+" .stat-load").html("0.00");
                                $(".statusbox.status-"+val.id+" .stat-bandwidth").html("0.00 Mbps");
                                $(".statusbox.status-"+val.id+" .stat-uptime").html("OFFLINE");
                                $(".statusbox.status-"+val.id+" .progress-ram-overlay").css("background",getBgColor(0));
                                $(".statusbox.status-"+val.id+" .progress-disk-overlay").css("background",getBgColor(0));
                            }
                        }else{ //Creating box, couldn't find one already created. This is the base html for the status boxes. Changes here that you expect to have automatically update will need to be added above.
                            //console.log("Could NOT find element for ID:"+val.id);
                            if (val.status == true) {
                                var bandwidthstr = val.bandwidth;
                                if(bandwidthstr >= 100){servershighbandwidth.push(val.name);}
                                bandwidthstr = bandwidthstr.replace("Mbps","");
                                boxhtml = '<a href="admin.php?view=list&type=search&search=server='+val.id+'"><div class="fluidbox statusbox status-'+val.id+'">'
                                    + '<div class="statuscolor statuscoloronline">'
                                    + '</div>'
                                    + '<div class="pure-u-1-2" style="float:right;">'
                                    + '<h3 style="text-align:center;" class="status-virtualization">Virtualization: ' + srvtype + '</h3>'
                                    + '<div><span class="stat-ram">RAM: '+val.ram_usage+'%</span><div class="progressContain"><div class="mem-prog progressbar" style="max-width: 100%;width:'+val.ram_usage+'%;"><div class="progress-overlay progress-ram-overlay" style="background:'+getBgColor(val.ram_usage)+'"></div></div></div></div>'
                                    + '<div><span class="stat-disk">Disk: ' + val.disk_usage + '%</span><div class="progressContain"><div class="disk-prog progressbar" style="max-width: 100%;width:'+val.disk_usage+'%;"><div class="progress-overlay progress-disk-overlay" style="background:'+getBgColor(val.disk_usage)+'"></div></div></div></div>'
                                    + '</div>'
                                    + '<div class="statusTxtInfo pure-u-1-2" style="float:left">'
                                    + '<h3 class="stat-name">' + val.name + ' (IPs: ' + ipcount + ')</h3>'
                                    + 'Load: <span class="stat-load">' + val.load_average + '</span><br>'
                                    + 'Bandwidth: <span class="stat-bandwidth">' + val.bandwidth + '</span><br>'
                                    + 'Uptime: <span class="stat-uptime">' + val.uptime + '</span>'
                                    + '</div>'
                                    + '</div></div><!--/div-->';
                                $(boxhtml).insertBefore('a[href*="addserver"]');
                            } else {
                                serversoffline.push(val.name);
                                boxhtml = '<a href="admin.php?view=list&type=search&search=server='+val.id+'"><div class="fluidbox statusbox status-'+val.id+'">'
                                    + '<div class="statuscolor statuscoloroffline">'
                                    + '</div>'
                                    + '<div class="pure-u-1-2" style="float:right;">'
                                    + '<h3 style="text-align:center;" class="status-virtualization">Virtualization: ' + srvtype + '</h3>'
                                    + '<div><span class="stat-ram">RAM: N/A</span><div class="progressContain"><div class="mem-prog progressbar" style="max-width: 100%;width:0%;"><div class="progress-overlay progress-ram-overlay"></div></div></div></div>'
                                    + '<div><span class="stat-disk">Disk: N/A</span><div class="progressContain"><div class="disk-prog progressbar" style="max-width: 100%;width:0%;"><div class="progress-overlay progress-disk-overlay"></div></div></div></div>'
                                    + '</div>'
                                    + '<div class="statusTxtInfo pure-u-1-2" style="float:left">'
                                    + '<h3 class="stat-name">' + val.name + ' (IPs: ' + ipcount + ')</h3>'
                                    + 'Load: <span class="stat-load">0.00</span><br>'
                                    + 'Bandwidth: <span class="stat-bandwidth">0.00 Mbps</span><br>'
                                    + 'Uptime: <span class="stat-uptime">OFFLINE</span>'
                                    + '</div>'
                                    + '</div></div><!--/div-->';
                                $(".statusbox.status-"+val.id+" .progress-ram-overlay").css("background",getBgColor(0));
                                $(".statusbox.status-"+val.id+" .progress-disk-overlay").css("background",getBgColor(0));
                                $(boxhtml).insertBefore('a[href*="addserver"]');
                            }
                        }
					});
				}
                
                if(serversoffline.length !== 0)
                {
                    $("#errorcontain .errorbox p.inlineB").empty();
                    $("#errorcontain .errorbox p.inlineB").html(serversoffline.join(", "));
                    $("#errorcontain .errorbox").css("display","inline-block");
                }else{
                    $("#errorcontain .errorbox").css("display","none");
                }
                if(servershighbandwidth.length !== 0)
                {
                    $("#errorcontain .warningbox.1 p.inlineB").empty();
                    $("#errorcontain .warningbox.1 p.inlineB").html(servershighbandwidth.join(", "));
                    $("#errorcontain .warningbox.1").css("display","inline-block");
                }else{
                    $("#errorcontain .warningbox.1").css("display","none");
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
<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-3 whitebox" style="display: block;margin: 0 auto;">
    <p class="alert warningbox static-alert">Whoops! There aren't any servers to show!<br>Add a server to feathur to have it shown here.</p>
    <br>
    <a href="admin.php?view=addserver" style="display: block;margin: 0 auto;"><div id="addServer"><b>+</b></div></a>
</div>
{%/if}
</div>