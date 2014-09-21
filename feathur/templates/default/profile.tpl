<script type="text/javascript">
	$(document).ready(function() {
        
        var setNotice = function(content){
            $('#notice p').html(content);
        };
    
		$("#ChangePassword").click(function(e) {
            e.preventDefault();
			loading(1);
			var password = $('#password').val();
            
			var passwordagain = $('#passwordagain').val();
			$.ajax({
				type: "POST",
				url: "profile.php?action=password",
				data: "password=" + password + "&passwordagain=" + passwordagain,
				success: function(data){
					var result = $.parseJSON(data);
                    $("#notice").removeClass().addClass("alert "+((result.success == 0) ? "error": "success")+"box");
					setNotice(result.content);
                    loading(0);
				}
			});
		});
        
		$("#ChangeUsername").click(function(e) {
            e.preventDefault();
            loading(1);
			var username = $('#username').val();
			$.ajax({
				type: "POST",
				url: "profile.php?action=username",
				data: "username=" + username,
				success: function(data){
					var result = $.parseJSON(data);
                    $("#notice").removeClass().addClass("alert "+((result.success == 0) ? "error": "success")+"box");
					setNotice(result.content);
                    $("#username").val(username);
                    $("#profilebox a:first-child b").html(username);
					loading(0);
				}
			});
		});
		$("#notice").css({display: "none"});
	});
</script>
<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2 nofluid">
    <div class="alert successbox" id="notice">
        <p></p>
    </div>
</div>
<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
    <form class="pure-form pure-form-aligned">
        <h3 class="title">Change User Password</h3>
        <div class="pure-control-group">
            <label for="user">New Password:</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div class="pure-control-group">
            <label for="user">New Password Again:</label>
            <input id="passwordagain" type="password" name="passwordagain" required>
        </div>
        <br>
        <div align="center">
            <button type="submit" class="pure-button pure-button-primary" id="ChangePassword">Change Password</button>
        </div>
        <br>
    </form>
</div>
<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1 pure-u-xl-1-2">
    <form class="pure-form pure-form-aligned" autocomplete="off">
        <h3 class="title">Change Name</h3>
        <div class="pure-control-group">
            <label for="user">New Name:</label>
            <input id="username" type="text" name="username" value="{%?Username}" autocomplete="off" required>
        </div>
        <br>
        <div align="center">
            <button type="submit" class="pure-button pure-button-primary" id="ChangeUsername">Change Name</button>
        </div>
        <br>
    </form>
</div>