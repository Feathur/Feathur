<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#usertable, #vpstable, #servertable').dataTable({
            "dom": '<"table-top"lf>rt<"table-bottom"ip>',
			"pagingType": "full_numbers",
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"DisplayLength": 10,
			"stateSave": true,
			"language": {
                "emptyTable": "No Entries",
                "paginate": {
                    "previous": "‹",
                    "next": "›",
                    "last": "»",
                    "first": "«",
                }
			}
		});
        
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] == "search"){
                $("#SearchSystem").val(pair[1]);
                $(".searchTerm").html("\""+pair[1]+"\"");
            }
        }
	});
</script>
	{%if isset|Result == true}
		{%if isset|VPSCount == true}
			<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
				<h3>VPS</h3>
                <table id="vpstable">
                    <thead>
                        <tr>
                            <th width="30%"><div align="center">Hostname</div></th>
                            <th width="25%"><div align="center">User</div></th>
                            <th width="25%"><div align="center">Primary IP</div></th>
                            <th width="10%"><div align="center">Type</div></th>
                        </tr>
                    </thead>
                    {%foreach entry in Result}
                        {%if entry[result_type] == vps}
                            <tr>
                                <td><a href="view.php?id={%?entry[id]}">{%?entry[hostname]}</a></td>
                                <td><div align="center"><a href="admin.php?view=list&type=search&search=user={%?entry[user_id]}">{%?entry[username]}</a></div></td>
                                <td><div align="center">{%?entry[primary_ip]}</div></td>
                                <td><div align="center">{%?entry[type]}</div></td>
                            </tr>
                        {%/if}
                    {%/foreach}
                </table>
            </div>
		{%/if}
		{%if isset|UserCount == true}
        
			<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
				<h3>Users</h3>
                <table id="usertable">
                    <thead>
                        <tr>
                            <th width="40%"><div align="center">Client Username</div></th>
                            <th width="30%"><div align="center">Email Address</div></th>
                            <th width="30%"><div align="center">View VPS</div></th>
                        </tr>
                    </thead>
                    {%foreach entry in Result}
                        {%if entry[result_type] == user}
                            <tr>
                                <td><a href="admin.php?view=list&type=search&search=user={%?entry[id]}">{%?entry[username]}</a></td>
                                <td><div align="center">{%?entry[email_address]}</div></td>
                                <td><div align="center"><a href="admin.php?view=list&type=search&search=user={%?entry[id]}">Client VPS</a></div></td>
                            </tr>
                        {%/if}
                    {%/foreach}
                </table>
            </div>
		{%/if}
		{%if isset|ServerCount == true}
			<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
				<h3>Servers</h3>
                <table id="servertable">
                    <thead>
                        <tr>
                            <th width="60%"><div align="center">Server Name</div></th>
                            <th width="20%"><div align="center">IP Address</div></th>
                            <th width="20%"><div align="center">Type</div></th>
                        </tr>
                    </thead>
                    {%foreach entry in Result}
                        {%if entry[result_type] == server}
                            <tr>
                                <td><a href="admin.php?view=list&type=search&search=server={%?entry[id]}">{%?entry[name]}</a></td>
                                <td><div align="center">{%?entry[ip_address]}</div></td>
                                <td><div align="center">{%?entry[type]}</div></td>
                            </tr>
                        {%/if}
                    {%/foreach}
                </table>
            </div>
		{%/if}
	{%/if}
	{%if isset|ServerCount == false}
		{%if isset|UserCount == false}
			{%if isset|VPSCount == false}
				{%if isset|Result == false}
					<div class="formnote pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2" style="margin:0.5em;">
                        Unfortunately, no results were returned for your query of <span class="searchTerm">""</span>. Try searching again?
                    </div>
				{%/if}
			{%/if}
		{%/if}
	{%/if}