{%if isset|Statistics == true}
	{%foreach info in Statistics}
	<div class="simplebox grid340-left">
		<div class="titleh">
			<h3>VPS Statistics</h3>
		</div>
		<table class="tablesorter">
			<tr>
				<td width="40%"><strong>System RAM:</strong></td>
				<td width="60%">{%?info[ram]} MB</td>
			</tr>
			<tr>
				<td width="40%"><strong>System Disk:</strong></td>
				<td width="60%">{%?info[disk]} GB</td>
			</tr>
			<tr>
				<td width="40%"><strong>CPU Cores:</strong></td>
				<td width="60%">{%?info[cpulimit]} Cores</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;"><div align="center"><strong>Bandwidth Usage:</strong></div></td>
				<td width="60%" rowspan="2">
					<div class="progress progress-{%if info[percent_bandwidth] < 50}success{%/if}{%if info[percent_bandwidth] < 80}{%if info[percent_bandwidth] > 50}warning{%/if}{%/if}{%if info[percent_bandwidth] < 101}{%if info[percent_bandwidth] > 79}danger{%/if}{%/if}{%if info[percent_bandwidth] > 100}danger progress-striped active{%/if}" style="margin-bottom:0;">
						<div class="bar" style="width: {%?info[percent_bandwidth]}%">{%if info[percent_bandwidth] > 25}{%?info[percent_bandwidth]}%{%/if}</div>
					</div>
				</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;"><div align="center">{%?info[bandwidth_usage]} / {%?info[bandwidth_limit]}</div></td></tr>
		</table>
	</div>
		<div class="simplebox grid340-right">
			<div class="titleh">
				<h3>VPS Information</h3>
			</div>
			<table class="tablesorter">
				<tr>
					<td width="40%"><strong>Mounted ISO:</strong></td>
					<td width="60%">{%?info[template]}</td>
				</tr>
				<tr>
					<td width="40%"><strong>Hostname:</strong></td>
					<td width="60%">{%?info[hostname]}</td>
				</tr>
				<tr>
					<td width="40%"><strong>Primary IP:</strong></td>
					<td width="60%">{%?info[primary_ip]}</td>
				</tr>
				<tr>
					<td width="40%" style="padding:0px;margin:0px;">Gateway:</td>
					<td width="60%">{%?info[gateway]}</td>
				</tr>
				<tr>
					<td width="40%" style="padding:0px;margin:0px;">Subnet Mask:</td>
					<td width="60%">{%?info[netmask]}</td>
				</tr>
			</table>
		</div>
	{%/foreach}
{%/if}