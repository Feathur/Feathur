{%if isset|Statistics == true}
	{%foreach server in Statistics}
		<div class="simplebox grid360-{%if isempty|server[type] == true}left{%/if}{%if isempty|server[type] == false}right{%/if}">
			<div class="titleh">
				<h3><img src="./templates/status/{%if isempty|server[status] == true}offline{%/if}{%if isempty|server[status] == false}online{%/if}.png" style="width:10px;height:10px;">{%?server[name]}</h3>
			</div>
			<div class="body padding10">
				<div style="width:49%;float:left;border-right:1px solid grey;">
					Load: {%?server[load_average]}
				</div>
				<div style="width:49%;float:right;border-left:1px solid grey;">
					Uptime: {%?server[uptime]}
				</div>
				<br>
				<div style="width:49%;float:left;border-right:1px solid grey;">
					Memory Usage: {%?server[ram_usage]}%
				</div>
				<div style="width:49%;float:right;border-left:1px solid grey;">
					Disk Usage: {%?server[disk_usage]}%
				</div>
			</div>
		</div>
	{%/foreach}
{%/if}
{%if isset|Statistics == false}
	<br><br>
	<div align="center">
		Add a server to Feathur so updates will appear here.
	</div>
{%/if}