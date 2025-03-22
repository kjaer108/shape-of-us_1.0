<?php
$url_map = [
    // Landing pages
    "index" => ["/", "", "/", ""],  // get_url("index")

    // Form pages
    "form-step" => ["form.php", "step=#", "form", "?step=#"],  // get_url("form-step", 1)
];


// URL HELPER FUNCTION
function get_url($key, $params = [], $filters = '') {
    global $url_map, $selectedLang;

    if (!isset($url_map[$key])) return '#';

    if (!is_array($params)) $params = [$params];

    $map = $url_map[$key];
    $offset = IS_LOCALHOST ? 0 : 2;

    $page = $map[0 + $offset];
    $param_template = $map[1 + $offset];

    // Replace # placeholders with actual params
    $i = 0;
    while (strpos($param_template, '#') !== false) {
        $param_template = preg_replace('/#/', isset($params[$i]) ? $params[$i] : '', $param_template, 1);
        $i++;
    }

    // Build base URL
    $url = IS_LOCALHOST
        ? BASE_URL . $page . ($param_template ? '?' . $param_template : '')
        : '/' . $selectedLang . '/' . $page . ($param_template ? '/' . $param_template : '');

    // Add language param on localhost
    if (IS_LOCALHOST) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . 'lang=' . $selectedLang;
    } else {
        if (substr($url, -1) !== '/' && strpos($url, '?') === false) {
            $url .= '/';
        }
    }

    // Add filters
    if ($filters) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . $filters;
    }

    return $url;
}