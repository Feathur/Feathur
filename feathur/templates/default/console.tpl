<html>
<head>
	<link rel="stylesheet" type="text/css" href="templates/default/style/reset.css" /> 
    <link rel="stylesheet" type="text/css" href="templates/default/style/fonts/fontawesome/css/font-awesome.min.css" /> 
    <link rel="stylesheet" type="text/css" href="templates/default/style/root.css" /> 
    <link rel="stylesheet" type="text/css" href="templates/default/style/grid.css" /> 
    <link rel="stylesheet" type="text/css" href="templates/default/style/typography.css" /> 
    <link rel="stylesheet" type="text/css" href="templates/default/style/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="templates/default/style/jquery-plugin-base.css" />
	<link rel="stylesheet" type="text/css" href="templates/default/style/basic.css" />
    <!--[if IE 7]>	  <link rel="stylesheet" type="text/css" href="templates/default/style/ie7-style.css" />	<![endif]-->
	<!--[if lt IE 7]> <link type='text/css' href='css/basic_ie.css' rel='stylesheet' media='screen' /> <![endif]-->
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
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="templates/default/js/jquery.simplemodal.js"></script>
	<script type="text/javascript" src="templates/default/js/basic.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.dataTables.js"></script>
	<script type="text/javascript" src="templates/default/js/jquery.form.js"></script>
	<script type="text/javascript" src="templates/default/js/ajaxsubmit.js"></script>
	<script type="text/javascript" src="templates/default/js/plupload.full.js"></script>
</head>
<body style="background:#FFFFFF;">
{%foreach vps in VPS}
	<div align="center" style="height:500px;verical-align:center;">
		{%if isempty|connect == true}
			<br><br>
			<form action="console.php?id={%?vps[id]}&action=connect" method="post">
				<div class="simplebox grid360">
					<div class="titleh">
						<h3>SSH Java Console</h3>
					</div>
					<table class="tablesorter">
						<tr>
							<td width="40%">Hostname / IP:</td>
							<td width="60%"><input id="Hostname" type="text" name="hostname" value="{%?vps[primary_ip]}" style="width:100%" /></td>
						</tr>
						<tr>
							<td width="40%">Port (Default 22):</td>
							<td width="60%"><input id="Port" type="text" name="port" value="22" style="width:100%" /></td>
						</tr>
						<tr>
							<td colspan="2">
								<div align="center">
									<button class="button small orange" id="StartConsole" type="submit">Start Java Console</button>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</form>
		{%/if}
		
		{%if isempty|connect == false}
			  <applet width="640" height="480" archive="SSHTermApplet-signed.jar,SSHTermApplet-jdkbug-workaround-signed.jar,SSHTermApplet-jdk1.3.1-dependencies-signed.jar" code="com.sshtools.sshterm.SshTermApplet" codebase="./includes/library/sshterm/" style="border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1">
			  	<param name="sshapps.connection.host" value="{%?Hostname}">
			  	<param name="sshapps.connection.port" value="{%?Port}">
			  	<param name="sshapps.connection.userName" value="root">
			  	<param name="sshapps.connection.authenticationMethod" value="password">
			  	<param name="sshapps.connection.connectImmediately" value="true">
			  </applet>
		{%/if}
	</div>
{%/foreach}
</body>
</html>