{%if isset|Type == false}
	<div align="center">
		<br><br>
		<table style="border:0px solid white;width:50%;height:100px;">
			<tr>
				<td width="40%" align="center">
					<a class="button-blue" href="admin.php?view=ippools&type=0">IPv4 Pools</a>
				</td>
				<td width="40%" align="center">
					<a class="button-gray" href="admin.php?view=ippools&type=1">IPv6 Pools</a>
				</td>
			</tr>
		</table>
	</div>
{%/if}
{%if isset|Type == true}
	{%if isempty|Type == true}
		<br><br>
		<div align="center">
			<h4>IPv4 Pools</h4>
			{%if isset|BlockList == true}
				{%if BlockList == false}
					<br><br>
					<div align="center">There are currently no pools defined.</div>
				{%/if}
			{%/if}
			{%if isset|BlockList == false}
				<br><br>
				<div align="center">There are currently no pools defined.</div>
			{%/if}
		</div>
	{%/if}
	{%if isempty|Type == false}
		<br><br>
		<div align="center">
			<h4>IPv6 Pools</h4>
						{%if isset|BlockList == true}
				{%if BlockList == false}
					<br><br>
					<div align="center">There are currently no pools defined.</div>
				{%/if}
			{%/if}
			{%if isset|BlockList == false}
				<br><br>
				<div align="center">There are currently no pools defined.</div>
			{%/if}
		</div>
	{%/if}
{%/if}