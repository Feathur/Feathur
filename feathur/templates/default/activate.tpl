<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{%?Title}</title>
		<link rel="stylesheet" type="text/css" href="templates/new/style/reset.css">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700' rel='stylesheet' type='text/css'>
        <link href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.0/animate.min.css' rel='stylesheet' type='text/css'>
        <link href='https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="templates/new/style/style.css">

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
        <script type="text/javascript" src="templates/new/js/jquery-settings.js"></script>
        <script type="text/javascript" src="templates/new/js/uiScripts.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
</head>
<body>
	<div class="loginform">
		<div class="title">
			<img src="templates/default/img/logo.png" width="112" height="35" />
		</div>

		<div class="body">
			{%if isset|Errors == true}
				<div style="z-index: 670;" class="albox errorbox">
					{%foreach error in Errors}
						{%?error[content]}
					{%/foreach}
					<a original-title="close" href="#" class="close tips">close</a>
				</div>
			{%/if}
			<div align="center">To finish activating your account enter the password you would like. Minimum of 5 characters.</div>
			<br><br>
			<form id="form1" name="form1" method="post" action="activate.php?id={%?Id}&email={%?Email}&action=save">
				<label class="log-lab">Password:</label>
				<input name="password" type="password" class="login-input-user" id="textfield" value=""/>
	
				<label class="log-lab">Password (Again):</label>
				<input name="passwordagain" type="password" class="login-input-pass" id="textfield" value=""/>
	
				<input type="submit" name="submit" id="button" value="Set Password & Finish" class="button"/>
			</form>
		</div>
	</div>
</body>
</html>