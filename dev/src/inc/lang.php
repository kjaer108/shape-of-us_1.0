<?php
function getLanguage() {
    global $language_support;

    // Check session first
    if (isset($_SESSION['lang'])) {
        return $_SESSION['lang'];
    }

    // Check cookies if session is not set
    if (isset($_COOKIE['lang'])) {
        $_SESSION['lang'] = $_COOKIE['lang']; // Sync cookie to session
        return $_COOKIE['lang'];
    }

    // Check browser settings (optional)
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);

    $lang = in_array($browserLang, $language_support) ? $browserLang : 'en';

    // Save the detected default language
    setLanguage($lang);

    return $lang;
}

function setLanguage($lang) {
    global $language_support;

    if (in_array($lang, $language_support)) {
        $_SESSION['lang'] = $lang;
        setcookie('lang', $lang, time() + (365 * 24 * 60 * 60), "/"); // Store for 1 year
    }
}

function __($key) {
    global $DeepL;
    global $page;

    return $DeepL->gettext($key, $page["sourcelang"]);
}
?>
