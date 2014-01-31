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
						<a href="{%?PanelMode}{%?PanelURL}/activate.php?id={%?entry[activation_code]}&email={%?entry[email]}">Click Here To Activate Your Account</a>
					</div>
					<br><br>
					If for whatever reason the link above does not display correctly, please copy and paste the URL below into your browser:
					<br><br>
					{%?PanelMode}{%?PanelURL}/activate.php?id={%?entry[activation_code]}&email={%?entry[email]}
					<br><br><hr>
					If you feel this message was sent in error please contact your VPS service provider.
				</div>
			</td></tr>
		</table>
	</div>
{%/foreach}
