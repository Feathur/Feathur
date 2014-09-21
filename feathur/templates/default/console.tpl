<html style="box-shadow: none;">
<head>
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

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="templates/{%?Template}/js/uiScripts.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/jquery-settings.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/ajaxsbmt.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" defer="defer" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/jquery.simplemodal.1.4.4.min.js"></script>
    <script type="text/javascript" defer="defer" src="templates/{%?Template}/js/chosen.jquery.min.js"></script>
</head>
<body>
{%foreach vps in VPS}
	<div align="center" style="verical-align:center; background: #fff;" class="pure-u-sm-1 pure-u-md-1 pure-u-lg-l pure-u-xl-1">
		{%if isempty|connect == true}
			<form action="console.php?id={%?vps[id]}&action=connect" method="post" class="pure-form pure-form-aligned outlined">
                <h3>SSH Java Console</h3>
                <div class="pure-control-group">
                    <label for="Hostname">Hostname / IP:</label>
                    <input id="Hostname" type="text" name="hostname" value="{%?vps[primary_ip]}" />
                </div>
                <div class="pure-control-group">
                    <label for="Hostname">Port (Default 22):</label>
                    <input id="Port" type="text" name="port" value="22" />
                </div>
                <br>
                <div class="pure-u-1">
                    <button class="pure-button pure-button-primary button-orange button-xlarge" id="StartConsole" type="submit">Start Java Console</button>
                </div>
			</form>
		{%/if}
		
		{%if isempty|connect == false}
			  <applet width="640" height="480" archive="SSHTermApplet-signed.jar,SSHTermApplet-jdkbug-workaround-signed.jar,SSHTermApplet-jdk1.3.1-dependencies-signed.jar" code="com.sshtools.sshterm.SshTermApplet" codebase="./includes/library/sshterm/" style="background: #fff; border-style: solid; border-width: 1; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1">
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