<br><div align="center">First run the feathur installer, then fill out the form bellow.</div><br>
<div align="center">
	{%if isset|Errors == true}
		{%foreach error in Errors}
			<div style="z-index: 670;width:60%;height:25px;" class="albox small-{%?error[type]}">
				<div id="Status" style="padding:4px;padding-left:5px;width:95%;">{%?error[result]}</div>
				<div style="float:right;"><a href="#" onClick="return false;" style="margin:-3px;padding:0px;" class="small-close CloseToggle">x</a></div>
			</div>
		{%/foreach}
	{%/if}
	<form name="input" action="admin.php?view=addserver&action=submitserver" method="post">
		<div class="simplebox grid740" style="text-align:left;">
			<div class="titleh">
				<h3>Add Server</h3>
			</div>
			<div class="body">
				<div class="st-form-line">	
					<span class="st-labeltext">Name:</span>	
					<input id="name" type="text" name="name" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">	
					<span class="st-labeltext">IP/Hostname:</span>	
					<input id="Hostname" type="text" name="hostname" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">	
					<span class="st-labeltext">Super User (Usually root):</span>	
					<input id="Username" type="text" name="username" value="root" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">
					<span class="st-labeltext">SSH Key:</span>	
					<textarea name="key" class="st-forminput" id="key" style="width:510px" rows="3" cols="47"></textarea>
					<div style="z-index: 470;" class="clear"></div>
				</div>
				<div class="st-form-line">
					<span class="st-labeltext">Server Type</span>	
					<select name="type" id="ServerType" class="uniform">
						<option value="openvz">OpenVZ</option>
						<option value="kvm">KVM</option>
					</select>
				</div>
				<div class="st-form-line">	
					<span class="st-labeltext">Status URL (IF NOT DEFAULT):</span>	
					<input id="status" type="text" name="status" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">	
					<span class="st-labeltext">Location:</span>	
					<input id="location" type="text" name="location" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">	
					<span class="st-labeltext">Volume Group (KVM Only, Ex: vg_1232324):</span>	
					<input id="volume_group" type="text" name="volume_group" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">	
					<span class="st-labeltext">QEMU Path (KVM Only, leave blank for default):</span>	
					<input id="qemu" type="text" name="qemu" style="width:500px;">
					<div class="clear"></div>
				</div>
				<div class="st-form-line">
					<div align="center"><button class="small blue" id="AddServer">Add Server</button></div>
				</div>
			</div>
		</div>
	</form>
</div>