<script type="text/javascript">
	$(document).ready(function() {
		$("#adduser").click(function(e) {
            e.preventDefault();
			$("#status").css({display: "none"});
            loading(1);
			var username = $('#username').val();
			var email = $("#email").val();
			$.ajax({
				type: "POST",
				url: "admin.php?view=adduser&action=submituser",
				data: "email=" + email + "&username=" + username,
				success: function(data){
					var result = $.parseJSON(data);
                    $("#status").removeClass().addClass("alert "+((result.success == 0) ? "error" : "success")+"box static-alert");
                    alert("Result int: " + result.success);
					$("#status").html(result.content);
					loading(0);
					$("#status").css({display: "block"});
					if(result.created == 1){
						$('#email').val("");
						$('#username').val("");
					}
				}
			});
		});
		$("#Loading").css({display: "none"});
		$("#status").css({display: "none"});
	});
</script>
<div class="pure-u-sm-1 pure-u-md-1 pure-u-lg-1-2 pure-u-xl-1-2 nofluid">
    <form class="pure-u-1 pure-form pure-form-aligned">

        <h3 class="title">Add User</h3>

       <div class="pure-u-1">
            <div class="alert static-alert" id="status"></div>
        </div>
        <div class="pure-control-group">
            <label for="email">Email</label>
            <input id="email" type="email" placeholder="Email" required>
        </div>

        <div class="pure-control-group">
            <label for="username">Username</label>
            <input id="username" type="text" placeholder="Username" required>
        </div>

        <hr>
        
        <p class="formnote">Feathur will email each added user with a unique one-time link to set their password.</p>
        
        <div class="pure-u-1">
            <button type="submit" id="adduser" class="pure-button pure-button-primary centered">Create User</button>
        </div>
    </form>
</div>