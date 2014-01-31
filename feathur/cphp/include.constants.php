<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if($_CPHP !== true) { die(); }

define("CPHP_SETTING_TIMEZONE",						1100	);

define("CPHP_VARIABLE_SAFE",						1260	);
define("CPHP_VARIABLE_UNSAFE",						1261	);

define("CPHP_INSERTMODE_INSERT",					1270	);
define("CPHP_INSERTMODE_UPDATE",					1271	);

define("CPHP_REGEX_EMAIL",							"/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i");
