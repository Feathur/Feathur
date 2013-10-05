<?php
include('./includes/loader.php');
session_destroy();
header("Location: index.php");