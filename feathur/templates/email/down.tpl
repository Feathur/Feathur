{%foreach entry in EmailVars}
	<div align="center">
		<table style="border:1px solid black;width:80%;">
			<tr><td align="center">
				<div style="width:95%" align="left">
					Hello,
					<br><br>
					Feathur has been unable to communicate with {%?entry[server]} for the last 5 minutes. This outage may be part of a planned maintenance, but if it isn't please look into it immediately.
					<br><br>
					Possible Reasons:
					<br><br>
					<ul>
						<li>Network Outage</li>
						<li>SSH Down or SSH Key removed</li>
						<li>Node Reboot</li>
						<li>Other</li>
					</ul>
					<br><br>
					Feathur Management Console
				</div>
			</td></tr>
		</table>
	</div>
{%/foreach}
