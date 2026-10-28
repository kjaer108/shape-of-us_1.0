<?php
function isSupportedLanguage($lang) {
    global $language_support;
    return is_string($lang) && in_array($lang, $language_support, true);
}

/**
 * Write the persistent language cookie. Only ever used for EXPLICIT choices.
 */
function setLangCookie($lang) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $options = [
        'expires'  => time() + (365 * 24 * 60 * 60),
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
    if (defined('SITE_DOMAIN') && !(defined('IS_LOCALHOST') && IS_LOCALHOST)) {
        $options['domain'] = '.' . SITE_DOMAIN;
    }
    if (!headers_sent()) setcookie('lang', $lang, $options);
    $_COOKIE['lang'] = $lang;
}

/**
 * Language requested by the URL.
 * The Apache rewrite rules put the path-prefix language FIRST in QUERY_STRING and QSA appends the
 * visitor's own query string after it (e.g. "lang=de&lang=fr" for /de/about?lang=fr).
 * PHP's $_GET keeps the last value, so read the raw query string and use the first supported one.
 */
function getUrlLanguage() {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    if ($qs !== '') {
        foreach (explode('&', $qs) as $pair) {
            $kv = explode('=', $pair, 2);
            if (urldecode($kv[0]) === 'lang' && isSupportedLanguage(urldecode($kv[1] ?? ''))) {
                return urldecode($kv[1]);
            }
        }
        return null;
    }
    return (isset($_GET['lang']) && isSupportedLanguage($_GET['lang'])) ? $_GET['lang'] : null;
}

/**
 * Resolve the language once per request.
 * Precedence (first valid wins):
 *   1. URL language (?lang=xx, also supplied by the Apache rewrite rules) = explicit, saved
 *   2. cookie (written only by explicit choices)
 *   3. session (explicit choice or cached auto-detection)
 *   4. Accept-Language (session only, no cookie)
 *   5. 'en'
 */
function getLanguage() {
    static $resolved = null;
    if ($resolved !== null) return $resolved;

    // 1. Language in the URL (query string or rewritten path prefix)
    $urlLang = getUrlLanguage();
    if ($urlLang !== null) {
        setLanguage($urlLang);
        return $resolved = $urlLang;
    }

    // 2. Previously selected language: cookie first, then session
    if (isSupportedLanguage($_COOKIE['lang'] ?? null)) {
        return $resolved = $_SESSION['lang'] = $_COOKIE['lang'];
    }
    if (isSupportedLanguage($_SESSION['lang'] ?? null)) {
        return $resolved = $_SESSION['lang'];
    }

    // 3. Browser language, 4. English. Session only, no cookie.
    return $resolved = $_SESSION['lang'] = detectBrowserLanguage() ?? 'en';
}

function detectBrowserLanguage() {
    global $language_support;

    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($header === '') return null;

    // Parse "en-US,da;q=0.8,fr;q=0.5": honour q-values, skip q=0 and malformed entries
    $candidates = [];
    foreach (explode(',', $header) as $i => $part) {
        $bits = array_map('trim', explode(';', $part));
        $tag = strtolower($bits[0]);
        if (!preg_match('/^[a-z]{2,3}(-[a-z0-9]{2,8})*$/', $tag)) continue; // also drops "*"

        $q = 1.0;
        if (isset($bits[1])) {
            if (!preg_match('/^q=(0(\.\d{0,3})?|1(\.0{0,3})?)$/i', $bits[1], $m)) continue; // invalid q
            $q = (float)$m[1];
        }
        if ($q <= 0) continue;

        $candidates[] = ['q' => $q, 'i' => $i, 'lang' => substr($tag, 0, strcspn($tag, '-'))];
    }
    usort($candidates, function ($a, $b) {
        return $b['q'] <=> $a['q'] ?: $a['i'] <=> $b['i'];
    });

    foreach ($candidates as $c) {
        if (isSupportedLanguage($c['lang'])) return $c['lang'];
    }
    return null;
}


/**
 * Store an explicit language choice (session + persistent cookie).
 * Returns true on success, false if the language is unsupported.
 */
function setLanguage($lang) {
    if (!isSupportedLanguage($lang)) return false;

    $_SESSION['lang'] = $lang;
    setLangCookie($lang);
    return true;
}

function __($key) {
    global $DeepL;

    return $DeepL->gettext($key);
}
?>
