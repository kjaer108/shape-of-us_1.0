<?php
if(defined('SITE_DOMAIN') && !is_localhost) {
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"],
        '.' . SITE_DOMAIN,
        $cookieParams["secure"],
        $cookieParams["httponly"]
    );
}

// Initiate session
session_start();