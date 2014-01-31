<script type="text/javascript">
	$(document).ready(function() {
		$("#AddUser").click(function() {
			$("#AddUserStatusNotice").css({visibility: "hidden"});
			$("#AddUserLoadingImage").css({visibility: "visible"});
			var username = $('#Username').attr('value');
			var email = $("#Email").attr('value');
			$.ajax({
				type: "POST",
				url: "admin.php?view=adduser&action=submituser",
				data: "email=" + email + "&username=" + username,
				success: function(data){
					var result = $.parseJSON(data);
					$("#AddUserStatus").html(result.content);
					$("#AddUserLoadingImage").css({visibility: "hidden"});
					$("#AddUserStatusNotice").css({visibility: "visible"});
					if(result.created == 1){
						$('#Email').val("");
						$('#Username').val("");
					}
				}
			});
		});
		$("#AddUserLoadingImage").css({visibility: "hidden"});
		$("#AddUserStatusNotice").css({visibility: "hidden"});
	});
</script>
<div align="center" style="width:100%">
	<div style="z-index: 670;width:50%;" class="albox succesbox" id="AddUserStatusNotice">
		<div id="AddUserStatus"></div>
	</div>
	<div align="center" id="AddUserLoadingImage"><img src="templates/default/img/loading/2.gif"></div>
	<br><br>
</div>
<div align="center">Feathur will email each added user with a unique one-time link to set their password.</div>
<br><br>
<div align="center">
	<div class="simplebox grid740" style="text-align:left;">
		<div class="titleh">
			<h3>Add User</h3>
		</div>
		<div class="body">
			<div class="st-form-line">	
				<span class="st-labeltext">Email:</span>	
				<input id="Email" type="text" name="email" style="width:500px;">
				<div class="clear"></div>
			</div>
			<div class="st-form-line">	
				<span class="st-labeltext">User Name:</span>	
				<input id="Username" type="text" name="username" style="width:500px;">
				<div class="clear"></div>
			</div>
			<div class="st-form-line">
				<div align="center"><button class="small blue" id="AddUser">Add User</button></div>
			</div>
		</div>
	</div>
</div>