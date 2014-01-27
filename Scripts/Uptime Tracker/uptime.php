<?php
require_once('./includes/loader.php');

echo Templater::AdvancedParse($sTemplate->sValue.'/uptime', $locale->strings, array());