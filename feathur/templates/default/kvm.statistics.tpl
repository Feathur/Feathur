{%if isset|Statistics == true}
	{%foreach info in Statistics}
		{%if isset|iso_sync == true}
			{%if isempty|iso_sync == false}
				{%if isset|sync_error == true}
					{%if isempty|sync_error == false}
						<div align="center"><div class="albox warningbox" style="width:80%">Warning: Template syncing error. If this message persists for more than 5 minutes contact technical support.</div></div>
					{%/if}
					{%if isempty|sync_error == true}
						<div align="center">
							<div class="albox informationbox" style="width:80%">
								Template Sync Progress: 
								<div class="progress progress-success" style="margin-bottom:0;">
									<div class="bar" style="width: {%?info[percent_sync]}%">{%if info[percent_sync] > 25}{%?info[percent_sync]}%{%/if}</div>
								</div>
							</div>
						</div>
					{%/if}
				{%/if}
			{%/if}
		{%/if}
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
				<tr><td width="40%" style="padding:0px;margin:0px;padding-left:10px;"><strong>Bandwidth Usage:</strong></td>
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
					<td width="40%" style="padding:0px;margin:0px;padding-left:10px;"><strong>Gateway:</strong></td>
					<td width="60%" style="padding:0px;margin:0px;padding-left:10px;">{%?info[gateway]}</td>
				</tr>
				<tr style="padding:0px;margin:0px;">
					<td width="40%" style="padding:0px;margin:0px;padding-left:10px;"><strong>Subnet Mask:</strong></td>
					<td width="60%" style="padding:0px;margin:0px;padding-left:10px;">{%?info[netmask]}</td>
				</tr>
			</table>
		</div>
	{%/foreach}
{%/if}