<?php
// Assumes: table sou_settings(id PK, settings_json JSON NOT NULL, updated_at TIMESTAMP ...)
// One global row with id = 1

/**
 * Convert a dot path (signup.country_locale_favorites.dk)
 * to a safe MySQL JSON path ($."signup"."country_locale_favorites"."dk")
 *
 * // Set the DK favorites list
 * update_setting(
 *      'signup.country_locale_favorites.dk',
 *      ['da','en-GB','de','fr','es','it','pl','cs']
 * );
 *
 * // Read it back
 * $favs = get_setting('signup.country_locale_favorites.dk', []);
 *
 * // Bulk update DK and DE in one statement
 * update_settings_bulk([
 *      'signup.country_locale_favorites.dk' => ['da','en-GB','de','fr','es','it','pl','cs'],
 *      'signup.country_locale_favorites.de' => ['de','en-GB','fr','it','pl','es','cs','da'],
 * ]);
 *
 */
function _json_path_from_dot(string $path): string {
    $parts = array_map('trim', explode('.', $path));
    $quoted = array_map(fn($p) => '"'.str_replace('"','\"',$p).'"', $parts);
    return '$.' . implode('.', $quoted);
}

function _ensure_row_exists(PDO $pdo): void {
    // Create the singleton row if it isn't there yet
    $pdo->exec("INSERT IGNORE INTO sou_settings (id, settings_json) VALUES (1, JSON_OBJECT())");
}

/**
 * Read a setting by dot-path, with process-local cache.
 */
function get_setting(string $path, $default = null) {
    global $pdo;
    static $cache = null;

    // Load settings once and cache in memory
    if ($cache === null) {
        $stmt = $pdo->query("SELECT settings_json FROM sou_settings WHERE id = 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cache = json_decode($row['settings_json'] ?? '{}', true);
    }

    // Walk through the nested array using dot notation
    $value = $cache;
    foreach (explode('.', $path) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            $value = null;
            break;
        }
        $value = $value[$part];
    }

    // Fallback 1: if the specific value was not found
    if ($value === null) {
        // Check if it’s a country_locale_favorites path — fallback to default
        if (preg_match('/^form\.country_locale_favorites\.[a-z]{2}$/i', $path)) {
            $defaultPath = 'form.country_locale_favorites.default';
            $value = $cache['form']['country_locale_favorites']['default'] ?? null;
        }
    }

    // Fallback 2: if still nothing, return provided $default or []
    if ($value === null) {
        $value = $default ?? [];
    }

    return $value;
}


/**
 * Update a single setting by dot-path. $value can be scalar/array/object.
 * Returns true on success.
 */
function update_setting(string $path, $value): bool {
    global $pdo;
    static $cache = null;

    _ensure_row_exists($pdo);

    $jsonPath = _json_path_from_dot($path);

    // Prepare JSON value for MySQL
    $json = json_encode($value, JSON_UNESCAPED_UNICODE);
    if ($json === false) return false;

    $sql = "UPDATE sou_settings
            SET settings_json = JSON_SET(settings_json, :path, CAST(:val AS JSON)),
                updated_at = NOW()
            WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([':path' => $jsonPath, ':val' => $json]);
    if (!$ok) return false;

    // Update in-process cache if already loaded
    if ($cache !== null) {
        $ref =& $cache;
        $parts = explode('.', $path);
        foreach ($parts as $p) {
            if (!isset($ref[$p]) || !is_array($ref[$p])) $ref[$p] = [];
            $ref =& $ref[$p];
        }
        $ref = $value;
    }

    return true;
}

/**
 * Bulk update multiple settings atomically (fewer round-trips).
 * @param array<string,mixed> $map   [ 'a.b' => $val, 'x.y.z' => $val2, ... ]
 */
function update_settings_bulk(array $map): bool {
    global $pdo;

    if (!$map) return true;

    _ensure_row_exists($pdo);

    $frags = [];
    $params = [];
    $i = 0;
    foreach ($map as $dot => $val) {
        $i++;
        $pathParam = ":p{$i}";
        $valParam  = ":v{$i}";
        $frags[]   = "{$pathParam}, CAST({$valParam} AS JSON)";
        $params[$pathParam] = _json_path_from_dot($dot);
        $json = json_encode($val, JSON_UNESCAPED_UNICODE);
        if ($json === false) return false;
        $params[$valParam]  = $json;
    }

    $sql = "UPDATE sou_settings
            SET settings_json = JSON_SET(settings_json, " . implode(', ', $frags) . "),
                updated_at = NOW()
            WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}