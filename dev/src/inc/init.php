<?php

// Start timer for debugging load time
$time_start = microtime(true);

// Are we debugging?
$debug = true;
if (!isset($no_debug)) $no_debug = false;
if ($no_debug) $debug = false; // $no_debug overrides the manual setting

define("SITE_DOMAIN", "shapeofus.eu");
define("IS_LOCALHOST", in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

// Include config file
$config = include(__DIR__."/config.php");


// Include Zandora System files
require_once __DIR__."/session.php";
require_once __DIR__."/data.php";
require_once __DIR__."/system.php";
require_once __DIR__."/database.php";
require_once __DIR__."/navigation.php";
require_once __DIR__."/lang.php";

require_once __DIR__."/../classes/DeepL.php";
$DeepL = new translate(
    getLanguage(),
$page['name'] ?? NULL,
true);

// Set user IP
if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
    if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
        $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
        define("user_ip", trim($addr[0]));
    } else {
        define("user_ip", $_SERVER['HTTP_X_FORWARDED_FOR']);
    }
}
else {
    define("user_ip", $_SERVER['REMOTE_ADDR']);
}

$selectedLang = getLanguage();
//$selectedLang = "en"; // TODO: Remove this line when the language selection is implemented

$countryCode = strtolower(get_country_from_ip(user_ip)) ?? "dk";

// Include arrays with translations
require_once __DIR__."/../../locale/".$selectedLang."/language_".$selectedLang.".php";


// Resolve BASE_URL and base_path
if (IS_LOCALHOST) {
    define("BASE_URL", "http://localhost:63342/shape-of-us_1.0/dev/");
} else {
    define("BASE_URL", "https://".SITE_DOMAIN."/");
}


// Set user IP
if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
    if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
        $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
        define("USER_IP", trim($addr[0]));
    } else {
        define("USER_IP", $_SERVER['HTTP_X_FORWARDED_FOR']);
    }
}
else {
    define("USER_IP", $_SERVER['REMOTE_ADDR']);
}

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent);


/* *************************************************************************
    Get page data
   ************************************************************************* */

if (isset($page) && $page) {
    $sql = "SELECT `title`, `title_elements`, `head_title`, `head_desc`, `noindex`, `nofollow`, `canonical`, `modals`, `include_js`, `vendor_js` FROM `sou_pagedata` WHERE `pagename` LIKE :pagename";
    $params = [
        ":pagename" => $page["name"]
    ];
    $page_data = pdo_get_row($pdo, $sql, $params);

    $page["title"] = $page_data["title"] ?? "Grab a title";
    $page["head_title"] = isset($page_data["head_title"]) ? dgettext("seo", $page_data["head_title"]) : "Zandora - Empowering your sexuality";
    $page["head_desc"] = isset($page_data["head_desc"]) ? dgettext("seo", $page_data["head_desc"]) : "Zandora - Empowering your sexuality";
    $page["noindex"] = $page_data["noindex"] ?? NULL;
    $page["nofollow"] = $page_data["nofollow"] ?? NULL;
    $page["canonical"] = $page_data["canonical"] ?? NULL;
    $page["modals"] = $page_data["modals"] ?? NULL;
    $page["include_js"] = $page_data["include_js"] ?? NULL;
    $page["vendor_js"] = $page_data["vendor_js"] ?? NULL;
    $page["title_elements"] = $page_data["title_elements"] ?? NULL;

    unset($page_data);
}

