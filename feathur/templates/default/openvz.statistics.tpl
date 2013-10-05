{%if isset|Statistics == true}
	{%foreach info in Statistics}
	<div class="simplebox grid340-left">
		<div class="titleh">
			<h3>VPS Statistics</h3>
		</div>
		<table class="tablesorter">
			<tr>
				<td width="40%" style="padding:0px;margin:0px;"><div align="center"><strong>RAM Usage:</strong></div></td>
				<td width="60%" rowspan="2" style="border-bottom: 2px solid #CBDAE8;">
					<div class="progress progress-{%if info[percent_ram] < 50}success{%/if}{%if info[percent_ram] < 80}{%if info[percent_ram] > 50}warning{%/if}{%/if}{%if info[percent_ram] < 101}{%if info[percent_ram] > 79}danger{%/if}{%/if}{%if info[percent_ram] > 100}danger progress-striped active{%/if}" style="margin-bottom:0;">
						<div class="bar" style="width: {%?info[percent_ram]}%">{%if info[percent_ram] > 100}<div align="left" style="padding-left:100px;">{%/if}{%if info[percent_ram] > 20}{%?info[percent_ram]}%{%/if}{%if info[percent_ram] > 50} ~ {%?info[used_ram]}MB / {%?info[total_ram]}MB{%/if}{%if info[percent_ram] > 100}</div>{%/if}</div>
					</div>
				</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;border-bottom: 2px solid #CBDAE8;"><div align="center">{%?info[used_ram]}MB / {%?info[total_ram]}MB</div></td></tr>
			<tr>
				<td width="40%" style="padding:0px;margin:0px;"><div align="center"><strong>SWAP Usage:</td>
				<td width="60%" rowspan="2" style="border-bottom: 2px solid #CBDAE8;">
					<div class="progress progress-{%if info[percent_swap] < 50}success{%/if}{%if info[percent_swap] < 80}{%if info[percent_swap] > 50}warning{%/if}{%/if}{%if info[percent_swap] < 101}{%if info[percent_swap] > 79}danger{%/if}{%/if}{%if info[percent_swap] > 100}danger progress-striped active{%/if}" style="margin-bottom:0;">
						<div class="bar" style="width: {%?info[percent_swap]}%">{%if info[percent_swap] > 100}<div align="left" style="padding-left:100px;">{%/if}{%if info[percent_swap] > 20}{%?info[percent_swap]}%{%/if}{%if info[percent_swap] > 50} ~ {%?info[used_swap]}MB / {%?info[total_swap]}MB{%/if}</div>{%if info[percent_swap] > 100}</div>{%/if}
					</div>
				</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;border-bottom: 2px solid #CBDAE8;"><div align="center">{%?info[used_swap]}MB / {%?info[total_swap]}MB</div></td></tr>
			<tr>
				<td width="40%" style="padding:0px;margin:0px;"><div align="center"><strong>Disk Usage:</strong></div></td>
				<td width="60%" rowspan="2" style="border-bottom: 2px solid #CBDAE8;">
					<div class="progress progress-{%if info[percent_disk] < 50}success{%/if}{%if info[percent_disk] < 80}{%if info[percent_disk] > 50}warning{%/if}{%/if}{%if info[percent_disk] < 101}{%if info[percent_disk] > 79}danger{%/if}{%/if}{%if info[percent_disk] > 100}danger progress-striped active{%/if}" style="margin-bottom:0;">
						<div class="bar" style="width: {%?info[percent_disk]}%">{%if info[percent_disk] > 100}<div align="left" style="padding-left:100px;">{%/if}{%if info[percent_disk] > 20}{%?info[percent_disk]}%{%/if}{%if info[percent_disk] > 50} ~ {%?info[used_disk]}GB / {%?info[total_disk]}GB{%/if}{%if info[percent_disk] > 100}</div>{%/if}</div>
					</div>
				</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;border-bottom: 2px solid #CBDAE8;"><div align="center">{%?info[used_disk]}GB / {%?info[total_disk]}GB</div></td></tr>
			<tr>
				<td width="40%" style="padding:0px;margin:0px;"><div align="center"><strong>CPU Usage:</strong></div></td>
				<td width="60%" rowspan="2" style="border-bottom: 2px solid #CBDAE8;">
					<div class="progress progress-{%if info[percent_cpu] < 50}success{%/if}{%if info[percent_cpu] < 80}{%if info[percent_cpu] > 50}warning{%/if}{%/if}{%if info[percent_cpu] < 101}{%if info[percent_cpu] > 79}danger{%/if}{%/if}{%if info[percent_cpu] > 100}danger progress-striped active{%/if}" style="margin-bottom:0;">
						<div class="bar" style="width: {%?info[percent_cpu]}%">{%if info[percent_cpu] > 100}<div align="left" style="padding-left:100px;">{%/if}{%if info[percent_cpu] > 20}{%?info[percent_cpu]}%{%/if}{%if info[percent_cpu] > 100}</div>{%/if}</div>
					</div>
				</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;border-bottom: 2px solid #CBDAE8;"><div align="center">{%?info[used_cores]} / {%?info[total_cores]}</div></td></tr>
			<tr><td width="40%" style="padding:0px;margin:0px;"><div align="center"><strong>Bandwidth Usage:</strong></div></td>
				<td width="60%" rowspan="2">
					<div class="progress progress-{%if info[percent_bandwidth] < 50}success{%/if}{%if info[percent_bandwidth] < 80}{%if info[percent_bandwidth] > 50}warning{%/if}{%/if}{%if info[percent_bandwidth] < 101}{%if info[percent_bandwidth] > 79}danger{%/if}{%/if}{%if info[percent_bandwidth] > 100}danger progress-striped active{%/if}" style="margin-bottom:0;">
						<div class="bar" style="width: {%?info[percent_bandwidth]}%">{%if info[percent_bandwidth] > 25}{%?info[percent_bandwidth]}%{%/if}</div>
					</div>
				</td>
			</tr>
			<tr><td width="40%" style="padding:0px;margin:0px;"><div align="center">{%?info[bandwidth_usage]} / {%?info[bandwidth_limit]}</div></td></tr>
		</table></div>
		<div class="simplebox grid340-right">
			<div class="titleh">
				<h3>VPS Information</h3>
			</div>
			<table class="tablesorter">
				<tr>
					<td width="40%"><strong>Load Average:</strong></td>
					<td width="60%">{%?info[load_average]}</td>
				</tr>
				<tr>
					<td width="40%"><strong>Uptime:</strong></td>
					<td width="60%">{%?info[uptime]}</td>
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
				<tr>
					<td width="40%"><strong>Operating System:</strong></td>
					<td width="60%">{%?info[operating_system]}</td>
				</tr>
			</table>
		</div>
	{%/foreach}
{%/if}