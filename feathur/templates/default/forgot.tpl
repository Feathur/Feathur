<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1", minimum-scale=1", user-scalable="no">
        
		<title>{%?Title}</title>
        
        <link rel="stylesheet" href="templates/new/style/base-min.css" type="text/css">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700' rel='stylesheet' type='text/css'>
        <link href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.0/animate.min.css' rel='stylesheet' type='text/css'>
        <link href='https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="templates/new/style/modal.css">
        <link rel="stylesheet" href="templates/new/style/grids-responsive-min.css">
        <link rel="stylesheet" href="templates/new/style/forms-min.css" type="text/css">
        <link rel="stylesheet" href="templates/new/style/buttons-min.css" type="text/css">
        <link rel="stylesheet" href="templates/new/style/tables-min.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="templates/new/style/chosen.min.css">
        <link rel="stylesheet" type="text/css" href="templates/new/style/normalize.css">
        <link rel="stylesheet" type="text/css" href="templates/new/style/style.css">

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
        <script type="text/javascript" src="templates/new/js/jquery-settings.js"></script>
        <script type="text/javascript" src="templates/new/js/ajaxsbmt.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="templates/new/js/jquery.simplemodal.1.4.4.min.js"></script>
        <script type="text/javascript" src="templates/new/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="templates/new/js/uiScripts.js"></script>
	</head>
	<body class="login">
        <div id="containlogin">
            <div class="logo" style="position: absolute;left: 50%;margin-left: -77px;margin-top: -100px;"></div>
            <div class="loginform" style="width: 387px;margin: 0 auto;">
                <form id="form1" name="form1" method="post" action="index.php?action=forgot">
                    <p style="font-size: 16px;color: rgb( 255, 255, 255 );text-align: center;text-shadow: 1px 1.7px 3px rgba(0, 0, 0, 0.3);margin-top: 11px;margin-bottom: 11px">Enter your email address below.</p>
                    <input name="email" type="text" class="login-input-user" id="textfield" value="" placeholder="Email" autofocus="true"/>
                    <br>
                    <div class="pure-u-1 text-centered"><button type="submit" name="submit" id="button" class="pure-button pure-button-primary" style="margin-top: 7px;margin-right: 10px;width: auto !important;">Request Password Reset</button>
                    <a href="/" style="color: #fff;margin-left: 10px;height: 47px;display: inline-block;" class="forgotpwdlnk">Back to Login</a></div>
                    </div>
                </form>
                <br>
                {%if isset|Errors == true}
                <div style="margin:10px auto;z-index: -2;" class="alert errorbox static-alert">
                    {%foreach error in Errors}
                    {%?error[result]}
                    {%/foreach}
                </div>
                <br>
                {%/if}
            </div>
            
            <div class="formCopyrightNotice">Copyright© 2014 Feathur - All Rights Reserved</div>
            
        </div>
		<div class="copyright">Copyright© 2014 Feathur - All Rights Reserved</div>
	</body>
</html>