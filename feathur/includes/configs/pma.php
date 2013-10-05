<?php
session_set_cookie_params(0, '/', '', 0);
session_name('feathur_auth');
session_start();
if($_SESSION['permissions'] == 7){
	$_SESSION['PMA_single_signon_password'] = 'databasepasswordhere';
	$_SESSION['PMA_single_signon_user'] = "root";
} else {
	die("Not a privledged user!");
}
$_SESSION['PMA_single_signon_host'] = 'localhost';
session_write_close();
if(!isset($_GET['server'])){
	header('Location: ./index.php?server=1');
}
?>
