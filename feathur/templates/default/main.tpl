{%if isset|UserVPS == true}
<br><br>
<script type="text/javascript">
	$(document).ready(function() {
		{%foreach server in UserVPS}
			$.getJSON("view.php?id={%?server[id]}&action=statistics",function(result){
					$('#UpDown{%?server[id]}').html('<img src="templates/{%?Template}/img/tpl/' + result.result + '.png" style="width:21px;height:21px;">');
			});
		{%/foreach}
	});
</script>
    <div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2">
        <div class="table-top">
            Your VPS
        </div>
        <table class="pure-table">
            <thead>
                <tr>
                    <th width="10%"><div align="center">U/D</div></th>
                    <th width="25%"><div align="center">Hostname</div></th>
                    <th width="20%"><div align="center">Server</div></th>
                    <th width="15%"><div align="center">Type</div></th>
                    <th width="20%"><div align="center">Primary IP</div></th>
                    <th width="10%"><div align="center">View</div></th>
                </tr>
            </thead>
            <tbody>
                {%foreach server in UserVPS}
                    <tr>
                        <td><div align="center"><div id="UpDown{%?server[id]}" style="width:21px;"></div></div></td>
                        <td><a href="view.php?id={%?server[id]}">{%?server[hostname]}</a></td>
                        <td><div align="center">{%?server[server_name]}</div></td>
                        <td><div align="center">{%?server[type]}</div></td>
                        <td><div align="center">{%?server[primary_ip]}</div></td>
                        <td><div align="center"><a href="view.php?id={%?server[id]}" class="pure-button button-small button-blue">View</a></div></td>
                    </tr>
                {%/foreach}
            </tbody>
        </table>
        <div class="table-bottom" style="min-height: 1px;"></div>
    </div>
{%/if}