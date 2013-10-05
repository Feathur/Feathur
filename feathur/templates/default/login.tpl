<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{%?Title}</title>
    <link rel="stylesheet" type="text/css" href="templates/default/style/base.css" /> 
    <!--[if IE 7]>	  <link rel="stylesheet" type="text/css" href="templates/default/style/ie7-style.css" />	<![endif]-->
	<script type="text/javascript" src="templates/default/js/jquery.min.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery-settings.js"></script>
	<script type="text/javascript" src="templates/default/js/toogle.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.tipsy.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.uniform.min.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="templates/default/js/raphael.js"></script>
	<script type="text/javascript" src="templates/default/js/analytics.js"></script>
	<script type="text/javascript" src="templates/default/js/popup.js"></script>
	<script type="text/javascript" src="templates/default/js/fullcalendar.min.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.prettyPhoto.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.slider.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.ui.accordion.js"></script>
	<script type="text/javascript" src="templates/default/https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="templates/default/js/jquery.dataTables.js"></script>
</head>
<body>
	<div class="loginform">
		<div class="title" style="padding:5px;margin-top:0px;">
			<img src="templates/default/img/logo.png"/>
		</div>

		<div class="body">
			{%if isset|Errors == true}
				<div style="z-index: 670;" class="albox errorbox">
					{%foreach error in Errors}
						{%?error[result]}
					{%/foreach}
					<a original-title="close" href="#" class="close tips">close</a>
				</div>
			{%/if}
			<form id="form1" name="form1" method="post" action="index.php?action=login">
				<label class="log-lab">Email Address</label>
				<input name="email" type="text" class="login-input-user" id="textfield" value=""/>
	
				<label class="log-lab">Password</label>
				<input name="password" type="password" class="login-input-pass" id="textfield" value=""/>
	
				<input type="submit" name="submit" id="button" value="Login" class="button"/>
			</form>
		</div>
	</div>
</body>
</html>