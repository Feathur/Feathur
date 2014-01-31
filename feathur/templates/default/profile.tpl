<script type="text/javascript">
	$(document).ready(function() {
		$("#ChangePassword").click(function() {
			$("#SettingStatusNotice").css({visibility: "hidden"});
			$("#SettingLoadingImage").css({visibility: "visible"});
			var password = $('#password').attr('value');
			var passwordagain = $('#passwordagain').attr('value');
			$.ajax({
				type: "POST",
				url: "profile.php?action=password",
				data: "password=" + password + "&passwordagain=" + passwordagain,
				success: function(data){
					var result = $.parseJSON(data);
					$("#SettingStatus").html(result.content); 
					$("#SettingLoadingImage").css({visibility: "hidden"});
					$("#SettingStatusNotice").css({visibility: "visible"});              
				}
			});
		});
		$("#ChangeUsername").click(function() {
			$("#SettingStatusNotice").css({visibility: "hidden"});
			$("#SettingLoadingImage").css({visibility: "visible"});
			var username = $('#username').attr('value');
			$.ajax({
				type: "POST",
				url: "profile.php?action=username",
				data: "username=" + username,
				success: function(data){
					var result = $.parseJSON(data);
					$("#SettingStatus").html(result.content); 
					$("#SettingLoadingImage").css({visibility: "hidden"});
					$("#SettingStatusNotice").css({visibility: "visible"});              
				}
			});
		});
		$("#SettingLoadingImage").css({visibility: "hidden"});
		$("#SettingStatusNotice").css({visibility: "hidden"});
	});
</script>
<div align="center" style="padding:10px;">
	<div align="center" style="width:100%">
		<div style="z-index: 670;width:50%;" class="albox succesbox" id="SettingStatusNotice">
			<div id="SettingStatus"></div>
		</div>
		<div align="center" id="SettingLoadingImage"><img src="templates/default/img/loading/2.gif"></div>
		<br><br>
	</div>
	<div class="simplebox grid360-left">
		<div class="titleh">
			<h3>Change User Password</h3>
		</div>
		<table class="tablesorter">
			<tr>
				<td width="40%">New Password:</td>
				<td width="60%"><input id="password" type="password" name="password" style="width:90%;" /></td>
			</tr>
			<tr>
				<td width="40%">Password (Again):</td>
				<td width="60%"><input id="passwordagain" type="password" name="passwordagain" style="width:90%;" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<div align="center">
						<button class="small blue" id="ChangePassword">Change Password</button>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="simplebox grid360-right">
		<div class="titleh">
			<h3>Change Name</h3>
		</div>
		<table class="tablesorter">
			<tr>
				<td width="40%">New Name:</td>
				<td width="60%"><input id="username" type="text" name="username" value="{%?Username}" style="width:90%;" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<div align="center">
						<button class="small blue" id="ChangeUsername">Change Name</button>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>