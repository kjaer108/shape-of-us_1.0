<?php
$currentUrlWithoutParams = strtok($_SERVER["REQUEST_URI"], '?');
$absoluteUrlWithoutParams = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$currentUrlWithoutParams";
?>
<!DOCTYPE html>
<html lang="<?=$selectedLang?>">
<head>
    <meta charset="utf-8">
    <base href="<?=BASE_URL?>">

    <!-- Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">

    <!-- Language -->
<?php
if (isset($page["translate"]) && $page["translate"] == 1) {
    // ✅ Define $currentLangPattern before it's used
    $currentLangPattern = '@/(' . implode('|', $language_support) . ')/@';

    foreach ($language_support as $lang) {
        if (IS_LOCALHOST) {
            echo '<link rel="alternate" hreflang="' . $lang . '" href="' . $absoluteUrlWithoutParams . '?lang=' . $lang . '" />' . PHP_EOL;
        } else {
            // Detect if the URL already contains a language prefix
            if (preg_match($currentLangPattern, parse_url($absoluteUrlWithoutParams, PHP_URL_PATH), $matches)) {
                // Replace existing language prefix with the correct one
                $updatedUrl = preg_replace($currentLangPattern, "/$lang/", $absoluteUrlWithoutParams, 1);
            } else {
                // No language prefix found, prepend the new language
                $updatedUrl = "https://shapeofus.eu/$lang" . parse_url($absoluteUrlWithoutParams, PHP_URL_PATH);
            }

            // Ensure no double slashes
            $updatedUrl = preg_replace('@(?<!:)//+@', '/', $updatedUrl);

            echo '<link rel="alternate" hreflang="' . $lang . '" href="' . $updatedUrl . '" />' . PHP_EOL;
        }
    }

    // ✅ x-default handling (Fixes error)
    $xDefaultLang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
    $xDefaultLang = in_array($xDefaultLang, $language_support) ? $xDefaultLang : 'en';

    // Remove existing language prefix before setting x-default
    $cleanUrl = preg_replace($currentLangPattern, "/", $absoluteUrlWithoutParams, 1);
    $xDefaultUrl = "https://shapeofus.eu/$xDefaultLang" . parse_url($cleanUrl, PHP_URL_PATH);

    // Ensure no extra slashes in x-default URL
    $xDefaultUrl = preg_replace('@(?<!:)//+@', '/', $xDefaultUrl);

    echo '<link rel="alternate" hreflang="x-default" href="' . $xDefaultUrl . '" />' . PHP_EOL;
}
?>

    <!-- SEO Meta Tags -->
    <title>Zandora | Shape of Us - Coming Soon</title>
    <meta name="description" content="Zandora envisions a world where everyone is equipped with the knowledge and resources to understand, explore, and embrace their sexuality.">

    <!-- Webmanifest + Favicon / App icons -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
<?php if (!IS_LOCALHOST) { ?>
    <link rel="manifest" href="/manifest.json">
<?php } ?>

    <link rel="icon" type="image/png" href="assets/app-icons/icon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="assets/app-icons/icon-180x180.png">

    <!-- Preloaded local web font (Clash Display + Satoshi) -->
    <link rel="preload" href="assets/fonts/clash-display-variable.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="assets/fonts/satoshi-variable.woff2" as="font" type="font/woff2" crossorigin>

    <!-- Font icons -->
    <link rel="preload" href="assets/icons/zandora-icons.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="assets/icons/zandora-icons.min.css">

    <!-- Vendor styles -->
    <link rel="stylesheet" href="assets/vendor/choices.js/public/assets/styles/choices.min.css">

    <!-- Bootstrap + Site styles -->
    <link rel="preload" href="assets/css/styles.min.css" as="style">
    <link rel="stylesheet" href="assets/css/styles.min.css">
    <link rel="stylesheet" href="assets/css/shapeofus.css">
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FD0JD8KJEE"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-FD0JD8KJEE');
</script>
