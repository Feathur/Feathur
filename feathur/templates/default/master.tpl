<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
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
        
		{%if UserPermissions == 7}
		{%if isset|PageType == true}
		<script type="text/javascript">
			$(document).ready(function(){
				$("#{%?PageType}").slideToggle();
			});
		</script>
		{%/if}
		{%/if}
		{%if UserPermissions == 0}
		{%if Request == view.php}
		<script type="text/javascript">
			$(document).ready(function(){
				$("#dashboard").slideToggle();
			});
		</script>
		{%/if}
		{%/if}
	</head>
    <body>
        <div class="wrapper">
        
            <!-- START SIDEBAR -->
            <div id="sidebar">
                <div id="sbtoggle"><i class="fa fa-bars"></i></div>
                <div id="sb-inner"><!-- START SB-INNER -->
                <!-- START HEADER -->
                <div id="header">
                    <!-- logo -->
                    <a href="main.php">
                        <div class="logo"></div>
                    </a>
                    <div class="smalltext">Panel Version {%?FeathurVersion}</div>
                    <!-- notifications -->
                    <div id="notifications">
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- END HEADER -->
                
                {%if isset|License == true}
                    {%if isempty|License == true}
                        {%if UserPermissions == 7}
                            <div align="center" id="licenseNotif" class="smalltext">
                                <a href="http://feathur.com" target="_blank">This copy of Feathur is unlicensed.<br>Consider purchasing a license.</a><!-- Please don't remove this /sadface -->
                            </div>
                            <br>
                        {%/if}
                    {%/if}
                {%/if}
                
                <!-- profile box -->
                <div id="profilebox">
                    <a href="#">
                    <b>{%?Username}</b>
                    </a>
                    <div class="profilemenu">
                        <ul>
                            <li><a href="profile.php">Account Settings</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
                {%if UserPermissions == 7}
                <!-- start searchbox -->
                <script type="text/javascript">
                    $(document).ready(function() {
                        $("#SearchSystem").keypress(function(event) {
                            if (event.which == 13) {
                                var search = $('#SearchSystem').val();
                                    window.location = "admin.php?view=list&type=search&search=" + search;
                            }
                        });
                    });
                </script>
                <input id="SearchSystem" type="text" name="search" class="input" value="{%if isset|Search == true}{%?Search}{%/if}" placeholder="Search for...">
                <!-- end searchbox -->
                {%/if}
                <!-- start sidemenu -->
                <div id="sidemenu">
                    <ul>
                        {%if UserPermissions != 7}
                        <li class="navcat">
                            <a href="dashboard.php" class="action" title="Dashboard"><i class="navIco fa fa-th-large"></i>Dashboard</a>
                            <ul class="navsub" id="dashboard">
                                {%if isset|UserVPS == true}
                                {%foreach server in UserVPS}
                                <li{%if isempty|server[viewing] == false} class="active"{%/if}>
                                <a href="view.php?id={%?server[id]}">
                                    <div class="navIco"></div>
                                    {%?server[hostname]}
                                </a>
                                </li>
                                {%/foreach}
                                {%/if}
                            </ul>
                        </li>
                        {%/if}
                        {%if UserPermissions == 7}
                        <li class="navcat">
                            <a href="#" class="action" title="Admin"><i class="navIco fa fa-desktop"></i>Admin</a>
                            <ul class="navsub" id="admin">
                                <li{%if Page == dashboard} class="active"{%/if}><a href="admin.php"><i class="navIco fa fa-th-large"></i>Dashboard</a></li>
                            </ul>
                        </li>
                        <li class="navcat">
                            <a href="#" class="action" title="Users"><i class="navIco fa fa-group"></i>Users</a>
                            <ul class="navsub" id="users">
                                <li{%if Page == listusers} class="active"{%/if}><a href="admin.php?view=list&type=users"><i class="navIco fa fa-list"></i>List Users</a></li>
                                <li{%if Page == adduser} class="active"{%/if}><a href="admin.php?view=adduser"><i class="navIco fa fa-plus-square"></i>Create User</a></li>
                            </ul>
                        </li>
                        <li class="navcat">
                            <a href="#" class="action" title="VPS"><i class="navIco fa fa-th"></i>VPS</a>
                            <ul class="navsub" id="vps">
                                <li{%if Page == listvps} class="active"{%/if}><a href="admin.php?view=list&type=vps"><i class="navIco fa fa-list"></i>List VPS</a></li>
                                <li{%if Page == create} class="active"{%/if}><a href="admin.php?view=createvps"><i class="navIco fa fa-plus-square"></i>Create VPS</a></li>
                            </ul>
                        </li>
                        <li class="navcat">
                            <a href="#" class="action" title="Servers"><i class="navIco fa fa-hdd-o"></i>Servers</a>
                            <ul class="navsub" id="servers">
                                <li{%if Page == listservers} class="active"{%/if}><a href="admin.php?view=list&type=servers"><i class="navIco fa fa-list"></i>List Servers</a></li>
                                <li{%if Page == addserver} class="active"{%/if}><a href="admin.php?view=addserver"><i class="navIco fa fa-plus-square"></i>Add Server</a></li>
                            </ul>
                        </li>
                        <li class="navcat">
                            <a href="#" class="action" title="Settings"><i class="navIco fa fa-cogs"></i>Settings</a>
                            <ul class="navsub" id="settings">
                                <li{%if Page == settings} class="active"{%/if}><a href="admin.php?view=settings"><i class="navIco fa fa-cogs"></i>Feathur Settings</a></li>
                                <li{%if Page == templates} class="active"{%/if}><a href="admin.php?view=templates"><i class="navIco fa fa-list-alt"></i>Template Manager</a></li>
                                <li{%if Page == ippools} class="active"{%/if}><a href="admin.php?view=ippools"><i class="navIco fa fa-table"></i>IP Pools</a></li>
                                <li{%if Page == update} class="active"{%/if}><a href="admin.php?view=update"><i class="navIco fa fa-download"></i>Update Settings</a></li>
                            </ul>
                        </li>
                        <li><a href="./phpmyadmin/" target="_blank"><i class="navIco fa fa-database"></i>PHPMyAdmin</a></li>
                        {%/if}
                        <li><a href="about.php"><i class="navIco fa fa-question-circle"></i>About Feathur</a></li>
                    </ul>
                </div><!-- end sidemenu -->
                <div id="footer"><!-- START FOOTER -->
                    <br /><br />
                    <div class="smalltext">Feathur Control Panel Copyright &copy; 2014 <a href="http://feathur.com" target="_blank">Feathur</a><br />All rights reserved.</div>
                </div><!-- END FOOTER -->
                <br>
                </div><!-- END SB-INNER -->
            </div><!-- END SIDEBAR -->
            
            <div id="right-wrap">
                <div id="page-wrapper">
                    <div id="page" class="pure-g">{%?Content}</div>
                </div>
                
                <div id="sidebar2">
                    <h2>Sidebar #2</h2>
                    <p>This should only show when needed.</p>
                </div>
                
            </div>
            
            <div id="loading"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
        </div>
    </body>
</html>