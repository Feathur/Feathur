<?php
require_once('./includes/loader.php');
/*
 * FINISH HIM!
 */
@session_destroy();
die(header("Location: index.php", 200));