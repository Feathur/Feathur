{%foreach vps in VPS}
	<script type="text/javascript">
		$(document).ready(function() {
			$(function() {
				$("#tabs").tabs();
			});
			var counttx = 0;
			var counterrx=setInterval(timerrx, 1000);
			function timerrx() {
				counttx=counttx+1;
				$('#timer').html(counttx);
			}
			function rebuildcheck() {
				$(function() {
					$.getJSON("view.php?id={%?vps[id]}&action=rebuildcheck",function(result){
						if(result.reload == 1){
							location.reload();
							counttx=0;
						} else {
							counttx=0;
						}
					});
				});
			}
			$("#Cancel").click(function() {
				$('#Cancel').html('<img src="templates/default/img/loading/9.gif" style="padding:0px;margin:0px;" id="LoadingImage">');
				$.getJSON("view.php?id={%?vps[id]}&action=cancelrebuild",function(result){
					if(result.reload == 1){
						location.reload();
						counttx=0;
					}
				});
			});
			setInterval(rebuildcheck, 5000);
		});
	</script>
	<br><br>
	<div align="center">
		<div id="tabs" style="width:95%">
			<ul>
				<li><a href="#tabs-1">Rebuild</a></li>
			</ul>
			<div id="tabs-1">
				<div align="center"><div class="albox warningbox" style="width:50%">Your VPS Is Currently Being Rebuilt</div></div>
				<div align="center">
					Your VPS is being rebuilt. This page will update approximately every 5 seconds...
				</div>
				<br><div align="center" style="width:30px;display:inline;white-space:nowrap;">Last update: <a id="timer" style="white-space:nowrap;">0</a> seconds ago</div>
				<br><img src="templates/default/img/loading/7.gif">
				<br><br>
				<div align="center"><a href="#" id="Cancel" class="button-red" style="color:#FFFFFF;">Abort / Cancel Rebuild</a><br><br>
				<strong>NOTE: Cancelling a rebuild will not save your data, once the process starts your data is gone.<br>If you cancel the rebuild before it is complete you will need to restart it again.</strong></div>
			</div>
		</div>
	</div>
{%/foreach}