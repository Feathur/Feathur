{%foreach entry in EmailVars}
	<div align="center">
		<table style="border:1px solid black;width:80%;">
			<tr><td align="center">
				<div style="width:95%" align="left">
					Hello,
					<br><br>
					This is a password reset email for Feathur. If you requested this reset click the link below and set your password, otherwise disregard this email:
					<br><br>
					<div align="center">
						<a href="http://{%?PanelURL}/activate.php?id={%?entry[forgot_code]}&email={%?entry[email]}" target="_blank">Click Here To Set Your Password</a>
					</div>
				</div>
			</td></tr>
		</table>
	</div>
{%/foreach}
