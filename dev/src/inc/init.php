<?php

define("SITE_DOMAIN", "shapeofus.eu");
define("is_localhost", in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));


// Include config file
$config = include(__DIR__."/config.php");

// Include Zandora System files
require_once __DIR__."/session.php";
require_once __DIR__."/system.php";
require_once __DIR__."/database.php";
