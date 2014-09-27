<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1", minimum-scale=1", user-scalable="no">
<title>{%?Title}</title>
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href='https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700'>
    <link rel="stylesheet" type="text/css" href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.0/animate.min.css'>
    <link rel="stylesheet" type="text/css" href='templates/{%?Template}/style/jquery.dataTables.min.css'>
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/modal.css">
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/grids-responsive-min.css">
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/forms-min.css">
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/buttons-min.css">
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/tables-min.css">
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/chosen.min.css">
    <link rel="stylesheet" type="text/css" href="templates/{%?Template}/style/style.css">

    <link rel="icon" type="image/png" href="templates/{%?Template}/img/tpl/favicon.ico">
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="templates/{%?Template}/js/uiScripts.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/jquery-settings.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/ajaxsbmt.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" defer="defer" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/jquery.simplemodal.1.4.4.min.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/chosen.jquery.min.js"></script>
</head>
<body class="login">
    <div class="logo" style="position: absolute;left: 50%;margin-left: -77px;margin-top: -100px;"></div>
    <div class="loginform" style="width: 387px;margin: 0 auto;">
        {%if isset|Errors == true}
            <div class="pure-u-1">
                <div class="alert errorbox">
                    {%foreach error in Errors}
                        {%?error[content]}
                    {%/foreach}
                </div>
            </div>
        {%/if}
        <br><br>
        <form id="form1" name="form1" method="post" action="activate.php?id={%?Id}&email={%?Email}&action=save">
            <p style="font-size: 16px;color: rgb( 255, 255, 255 );text-align: center;text-shadow: 1px 1.7px 3px rgba(0, 0, 0, 0.3);margin-top: 11px;margin-bottom: 11px">To finish activating your account, enter the password you would like. Minimum of 5 characters.</p>
            <input name="password" type="password" class="login-input-user" id="textfield" value="" placeholder="Password" autofocus="true"/>
            <input name="passwordagain" type="password" class="login-input-pass" id="textfield" value="" placeholder="Confirm Password"/>
            <button type="submit" name="submit" id="button" class="pure-button pure-button-primary">Set Password & Finish</button>
        </form>
    </div>
</body>
</html>