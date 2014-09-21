{%foreach vps in VPS}
	<script type="text/javascript">
		$(document).ready(function() {
			var counttx = 0;
			var counterrx=setInterval(timerrx, 1000);
			function timerrx() {
				counttx=counttx+1;
				$('#timer').html(counttx);
			}
			var rebuildcheck = function(){
                $.getJSON("view.php?id={%?vps[id]}&action=rebuildcheck",function(result){
                    if(result.reload == 1){
                        location.reload();
                        counttx=0;
                    } else {
                        counttx=0;
                    }
                });
			}
			$("#Cancel").click(function() {
				loading(1);
				$.getJSON("view.php?id={%?vps[id]}&action=cancelrebuild",function(result){
                    loading(0);
					if(result.reload == 1){
						location.reload();
						counttx=0;
					}
				});
			});
			setInterval(rebuildcheck, 5000);
		});
	</script>
	<br>
	<div class="pure-u-1 pure-g">
        <div class="whitebox">
            <div class="pure-u-1">
                <div class="alert warningbox">Your VPS Is Currently Being Rebuilt</div>
            </div>
            <br><br>
            <div class="pure-u-1">
                <div class="formnote">Your VPS is being rebuilt. This page will update approximately every 5 seconds...</div>
            </div>
            
            <br>
            <div class="pure-u-1">
                <h3 align="center">
                    <i class="fa fa-clock-o"></i> Last update: <a id="timer" style="style-space: nowrap">0</a> seconds ago
                    <br>
                    <img align="center" src="templates/{%?Template}/img/tpl/load-1.gif">
                </h3>
                
                <hr>
                
                <div align="center">
                    <p class="formnote">
                        <strong>NOTE: Cancelling a rebuild will not save your data, once the process starts your data is gone.<br>If you cancel the rebuild before it is complete you will need to restart it again.</strong>
                        <br><br>
                        <a href="#" id="Cancel" class="pure-button pure-button-primary button-red">Abort / Cancel Rebuild</a>
                    </p>
                </div>
            </div>
        </div>
	</div>
{%/foreach}