{%foreach entry in EmailVars}
	<div align="center">
		<table style="border:1px solid black;width:80%;">
			<tr><td align="center">
				<div style="width:95%" align="left">
					Hello,
					<br><br>
					This is an activation email for Feathur. If you recently ordered a VPS this is your activation email. As such, click the link bellow to activate your account and begin managing any VPS under your account. If you have any questions please open a support ticket with your service provider.
					<br><br>
					<div align="center">
						<a href="http://{%?PanelURL}/activate.php?id={%?entry[activation_code]}&email={%?entry[email]}" target="_blank">Click Here To Activate Your Account</a>
					</div>
				</div>
			</td></tr>
		</table>
	</div>
{%/foreach}
