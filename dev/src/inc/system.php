<?php
/* *************************************************************************
    This file contains functions that are used throughout the system.

    It is included from the init.php file.
   ************************************************************************* */

/*****************************************************************************
 * Debug functions
 *****************************************************************************/

function zformatValue($value) {
    if (is_bool($value)) {
        return $value ? "true" : "false";
    } elseif (is_null($value)) {
        return "NULL";
    } elseif (is_array($value) || is_object($value)) {
        return print_r($value, true);
    } else {
        return htmlspecialchars($value);
    }
}

function zdebug($str, $forcedebug = false) {
    global $debug;

    if ($debug || $forcedebug) echo zformatValue($str) . "<br/>";
}

function zdebug_kv($key, $str, $forcedebug = false) {
    global $debug;

    if ($debug || $forcedebug) echo "<strong>" . htmlspecialchars($key) . "</strong>: " . zformatValue($str) . "<br/>";
}

function zdebug_p($str, $forcedebug = false) {
    global $debug;

    if (is_bool($str)) {
        $str =  ($str) ? "true" : "false";
    } elseif (is_null($str)) {
        $str = "$str";
    }

    if ($debug || $forcedebug) echo "<p>$str</p>";
}

function zdebug_b($str, $forcedebug = false) {
    global $debug;

    if (is_bool($str)) {
        $str =  ($str) ? "true" : "false";
    } elseif (is_null($str)) {
        $str = "$str";
    }

    if ($debug || $forcedebug) echo "<strong>".$str."</strong><br/>";
}

function zdebug_h($str, $forcedebug = false) {
    global $debug;

    if (is_bool($str)) {
        $str =  ($str) ? "true" : "false";
    } elseif (is_null($str)) {
        $str = "$str";
    }

    if ($debug || $forcedebug) echo "<h2>" . $str . "</h2>";
}

function zdebug_hr($forcedebug = false) {
    global $debug;

    if ($debug || $forcedebug) echo "<hr/>";
}

function zdebug_ta($str, $rows=10, $cols=100, $forcedebug = false) {
    global $debug;

    if ($debug || $forcedebug) echo '<textarea rows="'.$rows.'" cols="'.$cols.'">'.$str.'</textarea><br/>';
}

function zdebug_ta_cz($str, $rows=10, $cols=100, $forcedebug = false) {
    global $debug;

    if ($debug || $forcedebug) echo '<textarea class="form-control" id="textarea-input" rows="15" control-id="ControlID-25">'.$str.'</textarea>';
}

function zdebug_r($arr, $txt=null, $forcedebug = false)
{
    global $debug;

    if ($txt) echo $txt;
    if ($debug || $forcedebug) print_r($arr);
    if ($debug || $forcedebug) echo "<br/>";
}

function zdebugDB($str) {
    global $pdo;

    $sql = "INSERT INTO `zandora_net_db_log`.`zdebug` (`data`, `timestamp`) VALUES (:data, current_timestamp())";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':data', $str, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage()); // Handle error (better to log it in production)
    }
}



/*****************************************************************************
  Misc. Functions
 *****************************************************************************/

/*function get_translation($key, $language) {
    global $pdo;

    $sql = "SELECT `text` FROM `translations` WHERE `language_code` LIKE :language AND `text_key` LIKE :textkey";
    $param = [
        ':language' => $language,
        ':textkey' => $key
    ];
    return pdo_get_col($pdo, $sql, $param);
}*/
function get_translation($key, $language) {
    global $pdo;

    if (is_array($key)) {
        // If $key is an array, initialize an empty array to store translations
        $translations = array();

        foreach ($key as $singleKey) {
            $sql = "SELECT `text` FROM `translations` WHERE `language_code` LIKE :language AND `text_key` LIKE :textkey";
            $param = [
                ':language' => $language,
                ':textkey' => $singleKey
            ];
            $translation = pdo_get_col($pdo, $sql, $param);

            // Add the translation to the result array
            $translations[] = $translation;
        }

        return $translations;
    } else {
        // If $key is a single string, fetch and return the translation for that key
        $sql = "SELECT `text` FROM `translations` WHERE `language_code` LIKE :language AND `text_key` LIKE :textkey";
        $param = [
            ':language' => $language,
            ':textkey' => $key
        ];
        return pdo_get_col($pdo, $sql, $param);
    }
}



/*****************************************************************************
  Date & Time functions
 *****************************************************************************/

/*
 * Purpose: This function takes a local time in a specified time zone, converts
 * it to UTC (Coordinated Universal Time), and returns it in either ISO or UTC
 * format.
 *
 * Parameters:
 *  $timestr: The input time string in the local time zone.
 *  $timezone: The target time zone for the input time (optional, with a default value of $user["timezone"]).
 *  $format: The desired output format, which can be "iso" (default) or "utc."
*/
function localtime_to_utc($timestr, $timezone = NULL, $format = "iso") {
    global $user;

    if (is_null($timezone)) $timezone = $user["timezone"];

    $DateTime = new DateTime($timestr, new DateTimeZone($timezone));

    // Convert to UTC format
    $DateTime->setTimezone(new DateTimeZone('UTC'));

    if ($format === "iso") {
        return $DateTime->format("Y-m-d H:i:s");
    } else if ($format === "utc") {
        return $DateTime->format("Ymd\THis\Z");
    } else {
        return NULL;
    }
}

function utc_to_localtime($timestr, $timezone = NULL) {
    global $user;

    if (is_null($timezone)) $timezone = $user["timezone"];

    $DateTime = new DateTime($timestr, new DateTimeZone('UTC'));

    // Convert to UTC format
    $DateTime->setTimezone(new DateTimeZone($timezone));

    return $DateTime->format("Y-m-d H:i:s");
}


function calculateTimeZoneOffset($timeZone, $dateTime, $format = false) {
    // Create a DateTime object for the specified date and time
    $date = new DateTime($dateTime, new DateTimeZone($timeZone));

    // Get the offset in seconds
    $offset = $date->getOffset();

    if (!$format) {
        return $offset / 3600;
    } else {
        // Convert the offset to hours and minutes
        $hours = abs(intval($offset / 3600));
        $minutes = abs(intval(($offset % 3600) / 60));

        // Determine if the offset is positive or negative
        $sign = ($offset >= 0) ? '+' : '-';

        // Format the offset as a string (e.g., "+02:00" or "-03:30")
        $formattedOffset = sprintf('%s%02d:%02d', $sign, $hours, $minutes);

        return $formattedOffset;
    }
}

/*
 * Purpose: This function takes a UTC timestamp, converts it to a specified
 * local time zone, and adjusts it based on a stored offset. It then returns
 * the resulting local time in ISO format.
 *
 * Parameters:
 *  $utcTimestamp: The input UTC timestamp.
 *  $targetTimeZone: The target local time zone for the conversion.
 *  $storedOffset: The stored offset in hours that needs to be applied to the converted local time.
 */
function convert_utc_to_local($utcTimestamp, $targetTimeZone, $storedOffset) {
    $dateTime = new DateTime($utcTimestamp, new DateTimeZone('UTC'));

    // Set the time zone
    $dateTime->setTimezone(new DateTimeZone($targetTimeZone));

    // Get the current offset for the target time zone
    $currentOffset = $dateTime->getOffset() / 3600; // Convert to hours
    //zdebug("Current Offset: $currentOffset");

    // Calculate the time difference between the stored offset and the current offset
    $offsetDiff = $storedOffset - $currentOffset;
    //zdebug("Offset Diff: $offsetDiff");

    // Adjust the time based on the offset difference
    $dateTime->modify("$offsetDiff hours");

    return $dateTime->format('Y-m-d H:i:s');
}


function dateparts_to_date($year, $month, $day) {
    return date_create($year . '-' . $month . '-' . $day);
}

function dateparts_to_iso($year, $month, $day) {
    $date = date_create($year . '-' . $month . '-' . $day);
    return date_format($date, 'Y-m-d');
}

function date_to_iso($date) {
    return date_format($date, 'Y-m-d');
}

function calc_age($date) {
    return((int)date_diff($date,date_create('today'))->y);
}

function friendly_date($datestr, $show_year = FALSE) {
    // https://stackoverflow.com/questions/8744952/formatting-datetime-object-respecting-localegetdefault/73250691#73250691

    if ($show_year) {
        return strtolower(date_format(date_create($datestr), "j. M 'y"));
    } else {
        return strtolower(date_format(date_create($datestr), "j. M"));
    }
}

function friendly_time($timeString) {
    // Check if the input is NULL or empty
    if ($timeString === null || trim($timeString) === "") {
        return "";
    }

    // Split the time string into hours, minutes, and seconds
    list($hours, $minutes, $seconds) = explode(":", $timeString);

    // Convert hours to an integer
    $hours = intval($hours);

    // Format the time string as needed
    if ($hours === 0) {
        $formattedTime = "$minutes:$seconds";
    } else {
        $totalMinutes = $hours * 60 + intval($minutes);
        $formattedTime = "$totalMinutes:$seconds";
    }

    return $formattedTime;
}

function get_date_str($date, $show_year = true, $short_month = false, $short_year = false) {
    global $months;

    if ($date instanceof DateTime) {
        $timestamp = $date->getTimestamp();
    } else {
        $timestamp = strtotime($date);
    }

    $month = $short_month ? substr(strtolower($months[date('n', $timestamp)]["name"]), 0, 3) : strtolower($months[date('n', $timestamp)]["name"]);
    $year = $short_year ? "'" . date('y', $timestamp) : date('Y', $timestamp);
    $event_date_str = date('j. ', $timestamp) . $month;

    if ($show_year) {
        $event_date_str .= ' ' . $year;
    }

    return $event_date_str;
}

/*
 * Purpose: This function takes a time string in the format "HH:MM:SS" and
 * converts it to an ISO 8601 duration string in the format "PT#H#M#S".
 * The function will only include the hours, minutes, and seconds if they are
 * greater than zero.
 * Parameters:
 * $time: The input time string in the format "HH:MM:SS".
 * Returns: The ISO 8601 duration string in the format "PT#H#M#S".
 * Example: convertToIso8601Duration("01:30:00") returns "PT1H30M".
 */
function convertToIso8601Duration($time) {
    // Split the time into hours, minutes, and seconds
    list($hours, $minutes, $seconds) = explode(":", $time);

    // Build the ISO 8601 duration string
    $duration = "PT";
    if ($hours > 0) {
        $duration .= $hours . "H";
    }
    if ($minutes > 0) {
        $duration .= $minutes . "M";
    }
    if ($seconds > 0) {
        $duration .= $seconds . "S";
    }

    return $duration;
}



/*****************************************************************************
  String functions
 *****************************************************************************/

function strempty($input) {
    return !isset($input) || $input === null || trim((string)$input) === '';
}

/**
 * Custom function to count words in a UTF-8 encoded string.
 *
 * @param string $content Content for which word count is calculated.
 * @return int Number of words in the content.
 */
function mb_str_word_count($content) {
    // Remove HTML tags and collapse multiple spaces into one
    $text = preg_replace('/\s+/', ' ', strip_tags($content));

    // Split the string into words based on spaces and punctuation
    $words = preg_split('/[[:space:][:punct:]]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

    // Count the number of words
    return count($words);
}


function string_to_slug($str, $delimiter = '-') {
    $unwanted_array = ['½'=>'-', 'æ'=>'a', 'å' => 'a', 'à' => 'a', 'á' => 'a', 'ä' => 'a', 'â' => 'a', 'è' => 'e', 'é' => 'e', 'ë' => 'e', 'ê' => 'e',
        'ì'=>'i', 'í' => 'i', 'ï' => 'î', 'ø' => 'o', 'ò' => 'o', 'ó' => 'o', 'ö' => 'o', 'ô' => '0', 'ù' => 'u', 'ú' => 'u', 'ü' => 'u', 'û'=>'u', 'ñ'=>'n', 'ç'=>'c', '·'=>'-', '/'=>'-', '_'=>'-', ','=>'-', ':'=>'-', ';'=>'-'];
    $str = strtr(strtolower($str), $unwanted_array);

    // Convert encoding and ignore unrepresentable characters
    $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);

    // Clean up the slug
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[\']/', '', $str))), $delimiter));

    return $slug;
}


/*****************************************************************************
  URL functions
 *****************************************************************************/
/*
function get_param($param, $default = NULL, $convert_string_null = true) {
    if (is_null($param)) {
        $data = (isset($_POST) && count($_POST) > 0) ? $_POST : ((isset($_GET) && count($_GET) > 0) ? $_GET : NULL);
        //if(strempty($data)) $data = $default;

        foreach ($data as $key => $value) {
            if ($convert_string_null && $value === "null") {
                $data[$key] = NULL;
            } elseif (strempty($value)) {
                $data[$key] = NULL;
            }
        }

        return $data;
    } else {
        $data = isset($_POST[$param]) ? $_POST[$param] : (isset($_GET[$param]) ? $_GET[$param] : $default);
        //if(strempty($data)) $data = $default;

        if ($convert_string_null && $data === "null") {
            return NULL;
        } elseif (strempty($data)) {
            return NULL;
        } else {
            return $data;
        }
    }
}
*/

function get_param($param = null, $default = null, $convert_string_null = true) {
    $source = !empty($_POST) ? $_POST : $_GET;

    if (is_null($param)) {
        // Apply processing to all parameters
        foreach ($source as $key => $value) {
            $source[$key] = process_param_value($value, $convert_string_null);
        }
        return $source;
    }

    if (isset($source[$param])) {
        return process_param_value($source[$param], $convert_string_null);
    }

    return $default;
}

function get_array_param($param): array {
    $raw = get_param($param);

    if (is_array($raw)) {
        return $raw;
    }

    if (is_string($raw) && trim($raw) !== '') {
        // Check for comma-separated values
        if (strpos($raw, ',') !== false) {
            return array_filter(array_map('trim', explode(',', $raw)));
        }
        return [trim($raw)];
    }

    return [];
}


function process_param_value($value, $convert_string_null) {
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = process_param_value($v, $convert_string_null);
        }
        return $value;
    }

    if ($convert_string_null && $value === "null") {
        return null;
    }

    if (strempty($value)) {
        return null;
    }

    return $value;
}



/*****************************************************************************
  Authorization functions
 *****************************************************************************/

function is_logged_in() {
    return (bool)(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]);
}

function get_user_id() {
    return (is_logged_in()) ? $_SESSION["user_info"]["ID"] : NULL;
}

function is_admin() {
    if (is_logged_in()) {
        $isAdmin = isset($_SESSION["user_info"]["admin"]) ? $_SESSION["user_info"]["admin"] : false;
        return (bool)$isAdmin;
    } else {
        return false;
    }
}

function get_admin_level() {
    if (is_logged_in()) {
        $adminLevel = is_admin() ? $_SESSION["user_info"]["adminlevel"] : 0;
        return $adminLevel;
    } else {
        return 0;
    }
}

function is_editor() {
    if (is_logged_in()) {
        $isWikiEditor = isset($_SESSION["user_info"]["wiki_editor"]) && $_SESSION["user_info"]["wiki_editor"];

        if ($isWikiEditor || is_admin()) {
            return true;
        }
    }

    return false;
}

function is_reviewer() {
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
        $isWikiReviewer = isset($_SESSION["user_info"]["wiki_reviewer"]) && $_SESSION["user_info"]["wiki_reviewer"];

        if ($isWikiReviewer || is_editor() || is_admin()) {
            return true;
        }
    }

    return false;
}

function is_partner() {
    if (is_logged_in()) {
        $isPartner = isset($_SESSION["user_info"]["partner"]) && $_SESSION["user_info"]["partner"];

        if ($isPartner || is_admin()) {
            return true;
        }
    }

    return false;
}

function is_my_place($place_id) {
    if (is_logged_in()) {
        return in_array($place_id, explode(",", $_SESSION["user_info"]["merchant"]));
    }

    return false;
}


function authorize($key) {
    // Check if the key exists and is defined as a constant
    if (defined($key) && is_logged_in()) {
        $access = constant($key);

        if ($access["partner"] && is_partner()) return true;
        if ($access["editor"] && is_editor()) return true;
        if ($access["admin"] && is_admin() && get_admin_level() >= $access["level"]) return true;
    }

    // Default to not authorized
    return false;
}



function is_premium() {
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
        $isPremiumValid = isset($_SESSION["premium_valid"]) && $_SESSION["premium_valid"];

        if ($isPremiumValid || is_admin() || is_editor()) {
            return true;
        }
    }

    return false;
}

function set_user_session(&$user_info = NULL) {

    if ($user_info == NULL) {

        // User is not logged in
        $_SESSION["loggedin"] = FALSE;
        $_SESSION["ident"] = "ip:" . USER_IP;

        // Remove all existing session variables associated with the user id
        unset($_SESSION["user_info"]);
        unset($_SESSION["premium_days_left"]);
        unset($_SESSION["premium_valid"]);

    } else {

        // User is logged in
        $_SESSION["loggedin"] = TRUE;
        $_SESSION["ident"] = "u:" . $user_info["ID"];

        If (!$user_info["premium_expire"] && ($user_info["admin"] || $user_info["wiki_editor"])) {

            $currentDate = new DateTime();
            $endOfCurrentYear = new DateTime(date("Y-12-31"));

            $endOfNextYear = new DateTime(date("Y-12-31"));
            $endOfNextYear->modify("+1 year");

            $daysUntilEndOfCurrentYear = $currentDate->diff($endOfCurrentYear)->days;

            if ($daysUntilEndOfCurrentYear < 90) {
                $user_info["premium_expire"] = $endOfNextYear->format("Y-m-d");
            } else {
                $user_info["premium_expire"] = $endOfCurrentYear->format("Y-m-d");
            }
            unset($currentDate, $endOfCurrentYear, $endOfNextYear, $daysUntilEndOfCurrentYear);

        }

        // Setup session variables with user info
        $_SESSION["user_info"] = $user_info;
        $_SESSION["user_info"]["avatar"] = NULL;

        // Setup premium session variables
        $_SESSION["premium_days_left"] = ($user_info["premium_expire"]) ? (ceil((strtotime($user_info["premium_expire"]) - time()) / (60 * 60 * 24)) + 1) : 0;
        $_SESSION["premium_valid"] = ($_SESSION["premium_days_left"] > 0) ? TRUE : FALSE;
    }

}

function persist_user_session($user_id, $session_id) {
    global $pdo;

    $sql = "SELECT * FROM `user_session` WHERE `user_id` = :user_id";
    $params = [
        ":user_id" => $user_id
    ];
    if (pdo_row_exists($pdo, $sql, $params)) {
        $sql = "UPDATE `user_session` SET `session_id` = :session_id, `expire` = :expire WHERE `user_id` = :user_id";
    } else {
        $sql = "INSERT INTO `user_session` (`user_id`, `session_id`, `expire`) VALUES (:user_id, :session_id, :expire)";
    }
    $params = [
        ":user_id" => $user_id,
        ":session_id" => $session_id,
        ":expire" => date("Y-m-d H:i:s", strtotime("+6 hours"))
    ];
    pdo_execute($pdo, $sql, $params);
}

/*****************************************************************************
  Zaps Functions
 *****************************************************************************/

function get_zaps_action_count($pdo, $user_id, $action_type, $times_unit) {
    $sql = "SELECT COUNT(*) FROM `zaps` WHERE `user_id` = :user_id AND `type` = :type";
    if ($times_unit == "day") {
        $sql .= " AND DATE(`timestamp`) = CURDATE()";
    }
    $params = [
        ':user_id' => $user_id,
        ':type' => $action_type
    ];

    return pdo_get_col($pdo, $sql, $params);
}

function add_zaps_to_user($zaps_type, $user_id = NULL) {
    global $pdo;
    global $zaps_table;

    if (is_null($user_id)) $user_id = $_SESSION["id"];
    if (is_string($zaps_type)) {
        $zap_info = $zaps_table[$zaps_type];
        $zaps_value = $zap_info['value'];
    }

    // Check the count of the action performed
    $action_count = get_zaps_action_count($pdo, $user_id, $zaps_type, $zap_info['times_unit']);
    if (is_null($zap_info['max_times']) ||
        ($zap_info['times_unit'] == "infinite" && $action_count < $zap_info['max_times']) ||
        ($zap_info['times_unit'] == "day" && $action_count < $zap_info['max_times'])) {

        // Insert into Zaps history
        $sql = "INSERT INTO `zaps` (user_id, type, zaps) VALUES (:user_id, :type, :zaps)";
        $params = [
            ':user_id' => $user_id,
            ':type' => $zaps_type,
            ':zaps' => $zaps_value
        ];

        try {
            pdo_execute($pdo, $sql, $params);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        // Update user's Zaps score
        $sql = "UPDATE `user` SET `zaps` = `zaps` + :zaps WHERE `id` = :user_id";
        $params = [
            ':zaps' => $zaps_value,
            ':user_id' => $user_id,
        ];

        try {
            pdo_execute($pdo, $sql, $params);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

/*****************************************************************************
 * Image functions
 *****************************************************************************/

function get_image_bin($image_id, $blob_field = "data") {
    global $pdo;
    global $default_error_reporting;

    if ($image_id == 0) {
        return file_get_contents(PRODUCT_IMAGE_BLANK, FILE_USE_INCLUDE_PATH);
    }

    $sql = "SELECT `".$blob_field."` AS `data`, `filename` FROM `product_image` WHERE `ID` = :image_id";
    $row = pdo_get_row($pdo, $sql, [':image_id' => $image_id]);
    $file_blob = $row["data"];

    if (is_null($row["data"])) {
        error_reporting(E_ALL ^ E_WARNING);

        $file_blob = file_get_contents(PRODUCT_IMAGE_PATH . $image_id . "-" . $row["filename"], FILE_USE_INCLUDE_PATH);

        if ($file_blob == FALSE) {
            $file_blob = file_get_contents(PRODUCT_IMAGE_BLANK, FILE_USE_INCLUDE_PATH);
        }
        error_reporting($default_error_reporting);
    }
    return $file_blob;
}

function get_image_src($image_id) {
    log_image_load($image_id, "image");
    return "data:image/jpg;base64," . base64_encode(get_image_bin($image_id));
}

function get_thumb_bin($image_id) {
    return get_image_bin($image_id, "thumb");
}

function get_thumb_src($image_id) {
    //log_image_load($image_id, "thumb");
    return "data:image/jpg;base64," . base64_encode(get_image_bin($image_id, "thumb"));
}

function get_image_src_from_file($path, $filename, $type = NULL) {
    global $default_error_reporting;

    if ($type == "film") {
        $blank_image = "no-image-film.jpg";
    } else {
        $blank_image = "no-image-large.jpg";
    }

    error_reporting(E_ALL ^ E_WARNING);
    $file_blob = file_get_contents($path . $filename, FILE_USE_INCLUDE_PATH);
    if ($file_blob === FALSE) $file_blob = file_get_contents("img/".$blank_image, FILE_USE_INCLUDE_PATH);
    error_reporting($default_error_reporting);

    return "data:image/jpg;base64," . base64_encode($file_blob);
}


function get_avatar_bin($table = "user", $table_id = "user_id") {
    global $pdo;

    $avatar_blob = NULL;

    //if ($_SESSION["loggedin"]) {
    if ($table_id == "user_id") $table_id = $_SESSION["id"];

    try {
        $sql = "SELECT `avatar` FROM `" . $table . "` WHERE `ID` = :table_id";
        $avatar_blob = pdo_get_col($pdo, $sql, [':table_id' => $table_id]);

        // $avatar_blob now contains the avatar column value
    } catch (Exception $e) {
        // Handle any exceptions
        echo "Error: " . $e->getMessage();
    }
    /*        $sql = "SELECT `avatar` FROM `".$table."` WHERE `ID` = " . $table_id;
            $stmt_local = $con->prepare($sql);
            $stmt_local->execute();
            $stmt_local->store_result();

            $stmt_local->bind_result($avatar_blob);
            $stmt_local->fetch();
            $stmt_local->close();*/
    //}

    if(!$avatar_blob) {
        if ($table == "directory") {
            $avatar_filename = "blank_club.jpg";
        } else {
            $avatar_filename = "blank_user.jpg";
        }
        $avatar_blob = file_get_contents(ZPROFILE_IMAGE_PATH . $avatar_filename, FILE_USE_INCLUDE_PATH);
    }

    return $avatar_blob;
}

function get_avatar_src($table = "user", $table_id = "user_id") {
    return "data:image/jpg;base64," . base64_encode(get_avatar_bin($table, $table_id));
}

function get_user_avatar($user_avatar = NULL) {
    global $pdo;

    if (is_numeric($user_avatar)) {

        $sql = "SELECT `avatar_filename` FROM `user` WHERE `ID` = :user_id";
        $params = [':user_id' => $user_avatar];
        $filename = pdo_get_col($pdo, $sql, $params);

    } elseif (is_string($user_avatar) && !empty($user_avatar)) {

        $filename = $user_avatar;

    } else {

        $filename = NULL;

    }

    if (is_null($filename)) {
        $filename = USER_AVATAR_BLANK;
    }

    return USER_AVATAR_FULL_URL . $filename;
}

// get_avatar_src("directory", $place["ID"])
function get_place_logo($place_logo = NULL, $place_type = "swinger") {
    global $pdo;

    if (is_numeric($place_logo)) {
        $sql = "SELECT `type`, `avatar_filename` FROM `directory` WHERE `ID` = :place_id AND `active` = 1";
        $params = [':place_id' => $place_logo];

        $row = pdo_get_row($pdo, $sql, $params);
        $place_type = $row["type"];
        $filename = $row["avatar_filename"];

    } elseif (is_string($place_logo) && !empty($place_logo)) {
        $filename = $place_logo;

    } else {
        $filename = NULL;
    }

    if (is_null($filename)) {
        if ($place_type === "lgbt") {
            $filename = PLACE_LOGO_BLANK_LGBT;
        } else {
            $filename = PLACE_LOGO_BLANK;
        }
    }

    return PLACE_LOGO_FULL_URL . $filename;
}


function get_practice_logo($practice_logo = NULL) {
    global $pdo;

    if (is_numeric($practice_logo)) {
        $sql = "SELECT `logo_filename` FROM `practice` WHERE `ID` = :practice_id AND `active` = 1";
        $params = [':practice_id' => $practice_logo];

        $row = pdo_get_row($pdo, $sql, $params);
        $filename = $row["logo_filename"];

    } elseif (is_string($practice_logo) && !empty($practice_logo)) {
        $filename = $practice_logo;

    } else {
        $filename = NULL;
    }

    if (is_null($filename)) {
        $filename = PRACTICE_LOGO_BLANK;
    }

    return concat_url(PRACTICE_FILES_FULL_URL , $filename);
}



/*****************************************************************************
 * Cache functions
 *****************************************************************************/

function save_cached_data($key, $data) {
    global $pdo;
    global $cur_datetime;

    // Check if the cache key exists
    $sql = "SELECT `key` FROM `cache` WHERE `key` = :key";
    $params = [':key' => $key];

    $keyExists = pdo_row_exists($pdo, $sql, $params);

    if (!$keyExists) {
        // If the key doesn't exist, insert a new cache entry
        $insertSql = "INSERT INTO `cache` (`key`, `data`) VALUES (:key, :data)";
        $insertParams = [':key' => $key, ':data' => $data];

        pdo_execute($pdo, $insertSql, $insertParams);
    } else {
        // If the key exists, update the existing cache entry
        $updateSql = "UPDATE `cache` SET `data` = :data, `timestamp` = :timestamp WHERE `key` = :key";
        $updateParams = [':key' => $key, ':data' => $data, ':timestamp' => $cur_datetime];

        pdo_execute($pdo, $updateSql, $updateParams);
    }
}

function get_cached_data($key) {
    global $pdo;

    $sql = "SELECT `data` FROM `cache` WHERE `key` = :key";
    $params = [':key' => $key];

    $row = pdo_get_row($pdo, $sql, $params);

    if ($row !== null) {
        return $row['data'];
    }

    return null;
}

// Return cached age in minutes
function get_cache_age($key) {
    global $pdo;

    $sql = "SELECT `timestamp` FROM `cache` WHERE `key` = :key";
    $params = [':key' => $key];

    $timestamp = pdo_get_col($pdo, $sql, $params);

    if ($timestamp !== null) {
        $datetime = strtotime($timestamp);
        $minutes_diff = round((time() - $datetime) / 60);
        return max($minutes_diff, 0);
    } else {
        return 0;
    }
}


// ------------------------------------------------------------
function format_dk_number($number, $decimals = 2) {
    return number_format($number,$decimals,',','.');
}

function price_float($string_number){
    $nleft = substr($string_number, 0,strlen($string_number)-3);
    $nright = substr($string_number, strlen($string_number)-3, 3);

    $nleft = str_replace(",", "", $nleft);
    $nleft = str_replace(".", "", $nleft);

    $string_number = $nleft.$nright ;

    $string_number = str_replace(",", ".", $string_number);

    return floatval($string_number);
}

function get_comment_cnt($type, $type_id, $set_empty_zero = FALSE) {
    global $pdo;

    $sql = "SELECT COUNT(*) FROM `comment` WHERE `type` = :type AND `type_id` = :type_id AND `active` = 1 AND `parent_id` IS NULL";
    $params = [':type' => $type, ':type_id' => $type_id];
    $comment_cnt = pdo_get_col($pdo, $sql, $params);

    if ($comment_cnt < 1) {
        return $set_empty_zero ? 0 : '';
    }

    return $comment_cnt;
}

function get_current_file($remove_initial_slash = FALSE) {
    if (IS_LOCALHOST) {
        $currentUrl = $_SERVER['REQUEST_URI'];

        // Use parse_url to get the path part of the URL
        $urlParts = parse_url($currentUrl);

        // Use basename to get the filename with query parameters
        return basename($urlParts['path']) . (isset($urlParts['query']) ? '?' . $urlParts['query'] : '');
    } else {
        if ($remove_initial_slash) {
            return trim($_SERVER['REQUEST_URI'], '/');
        } else {
            return $_SERVER['REQUEST_URI'];
        }
        return $_SERVER['REQUEST_URI'];
    }
}

function concat_url(...$path) {
    // Initialize the result with the first string
    $result = rtrim($path[0], '/');

    // Iterate through the rest of the strings and concatenate them
    for ($i = 1; $i < count($path); $i++) {
        // Check if the current part starts with "http://" or "https://"
        if (preg_match('~^https?://~', $path[$i])) {
            // If it does, simply append it to the result
            $result .= '/' . $path[$i];
        } else {
            // Otherwise, concatenate with "/"
            $result .= '/' . ltrim($path[$i], '/');
        }
    }

    // Replace any double slashes with single slashes
    $result = preg_replace('~(?<!:)//+~', '/', $result);

    return $result;
}

function concat_path(...$path) {
    // Initialize the result
    $result = '';

    // Concatenate all parts of the path
    foreach ($path as $part) {
        // Normalize directory separators and remove trailing backslashes
        $part = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $part), '\\');
        $result .= DIRECTORY_SEPARATOR . ltrim($part, DIRECTORY_SEPARATOR);
    }

    // Ensure the path ends with a single backslash if it doesn't already
    if (substr($result, -1) !== DIRECTORY_SEPARATOR) {
        $result .= DIRECTORY_SEPARATOR;
    }

    // Remove any duplicated separators
    $result = preg_replace('#'.preg_quote(DIRECTORY_SEPARATOR).'{2,}#', DIRECTORY_SEPARATOR, $result);

    $result = str_replace("\C:", "C:", $result);

    return $result;
}








function vendor_script_included($script_to_find) {
    global $page;

    if (isset($page["vendor_js"]) && $page["vendor_js"]) {
        $vendor_js = $page["vendor_js"];
        $vendor_array = explode(',', $vendor_js);
        $vendor_array = array_map('trim', $vendor_array);

        // Check if the $script_to_find exists in the array
        return in_array($script_to_find, $vendor_array);
    }

    return false;
}

function get_empty_state_html($text, $class="mt-4 mb-2 alert bg-body-secondary d-flex align-items-center gap-3", $icon="broken-heart") {

    if (empty($class)) $class = "mt-4 mb-2 alert bg-body-secondary d-flex align-items-center gap-3";

    if ($icon === "no-open") {
        $icon_html = '<svg class="flex-shrink-0" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M32.8091 8.08428L29.1196 11.7737L30.5704 14.9942C30.6043 15.0672 30.6235 15.1463 30.6267 15.2268C30.6299 15.3073 30.6172 15.3876 30.5892 15.4632L28.7569 20.6222C28.6991 20.776 28.5833 20.9009 28.4344 20.9701C28.2854 21.0394 28.1152 21.0474 27.9604 20.9925C27.8056 20.9377 27.6785 20.8242 27.6065 20.6766C27.5344 20.529 27.5231 20.359 27.5751 20.2032L29.326 15.2819L27.8002 11.8926C27.7479 11.7764 27.7325 11.647 27.756 11.5218C27.7795 11.3966 27.8407 11.2815 27.9315 11.1922L31.9211 7.20253C28.6855 4.18074 24.4233 2.5 19.996 2.5C15.5687 2.5 11.3065 4.18074 8.07085 7.20253L10.4096 9.54128L12.0605 7.96543C12.1329 7.89787 12.2202 7.84828 12.3154 7.82065C12.4105 7.79303 12.5108 7.78815 12.6081 7.8064C12.7055 7.82465 12.7972 7.86552 12.8759 7.92572C12.9545 7.98593 13.018 8.06377 13.061 8.15298L14.8245 11.93C14.8707 12.0287 14.8903 12.1376 14.8816 12.2462C14.8728 12.3548 14.8359 12.4592 14.7745 12.5491L12.5733 15.7509C12.4791 15.8866 12.3351 15.9796 12.1726 16.0094C12.0101 16.0393 11.8424 16.0037 11.7061 15.9103C11.5699 15.817 11.476 15.6735 11.4452 15.5112C11.4143 15.3489 11.449 15.181 11.5415 15.0442L13.5363 12.1364L12.2918 9.4725L10.8286 10.867C10.7099 10.9796 10.5519 11.0413 10.3883 11.0389C10.2247 11.0366 10.0685 10.9704 9.95309 10.8545L7.18286 8.08428C-2.9862 18.8394 4.4642 37.046 19.3709 37.4999L19.3706 32.8225L16.6442 30.7089C16.5588 30.6412 16.4921 30.5528 16.4505 30.4522C16.4089 30.3515 16.3938 30.2418 16.4066 30.1336C16.4209 30.0258 16.463 29.9235 16.5288 29.8369C16.5946 29.7503 16.6817 29.6823 16.7817 29.6396L21.6469 27.5384L21.872 24.9621C21.8895 24.7989 21.9701 24.649 22.0967 24.5446C22.2233 24.4402 22.3858 24.3895 22.5493 24.4034C22.7128 24.4173 22.8644 24.4947 22.9716 24.619C23.0787 24.7432 23.133 24.9045 23.1227 25.0683L22.86 28.0199C22.8516 28.1326 22.812 28.2408 22.7458 28.3325C22.6795 28.4241 22.5892 28.4956 22.4848 28.539L18.245 30.3649L20.3774 32.0221C20.4531 32.0803 20.5145 32.1551 20.5567 32.2408C20.599 32.3264 20.6211 32.4206 20.6213 32.5161V37.5C35.5275 37.0446 42.9791 18.839 32.8091 8.08428ZM19.996 22.5107C19.4415 22.5112 18.9027 22.3271 18.4645 21.9874C18.0263 21.6477 17.7137 21.1718 17.5759 20.6347H10.6159C10.4516 20.6323 10.2949 20.5654 10.1796 20.4484C10.0642 20.3314 9.99959 20.1737 9.9996 20.0094C9.9996 19.8451 10.0643 19.6874 10.1796 19.5704C10.2949 19.4534 10.4517 19.3864 10.616 19.3841H17.5759C17.6873 18.9518 17.9126 18.5573 18.2282 18.2417C18.5439 17.926 18.9384 17.7008 19.3706 17.5894V7.50268C19.3732 7.33855 19.4403 7.18201 19.5573 7.06686C19.6743 6.95171 19.8319 6.88718 19.996 6.88719C20.1602 6.8872 20.3177 6.95175 20.4347 7.06692C20.5517 7.18208 20.6187 7.33862 20.6213 7.50276V17.5894C21.2057 17.7464 21.7134 18.1098 22.0506 18.6124C22.3877 19.1149 22.5314 19.7225 22.4552 20.3228C22.3789 20.9231 22.0878 21.4755 21.6357 21.8777C21.1836 22.2799 20.6011 22.5048 19.996 22.5107Z" fill="#702E44"/>
                      <path d="M20.0006 21.26C20.6913 21.26 21.2512 20.7001 21.2512 20.0094C21.2512 19.3187 20.6913 18.7588 20.0006 18.7588C19.3099 18.7588 18.75 19.3187 18.75 20.0094C18.75 20.7001 19.3099 21.26 20.0006 21.26Z" fill="#702E44"/>
                    </svg>';
    } else {
        $icon_html = '<svg class="flex-shrink-0" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.305 30.1323L16.5284 25.4575C16.1599 24.997 16.1829 24.3522 16.5974 23.9377L20.1669 20.207L18.0943 16.4073C17.887 16.0158 17.9101 15.5553 18.1634 15.1868L21.2722 10.8114L19.3148 7.81769C18.624 7.14987 17.887 6.45901 17.081 5.8833C13.2123 3.23502 8.19204 3.83376 5.03713 7.21895C2.61913 9.82118 2.20462 12.93 2.66519 16.2692C3.17182 20.1149 5.03713 23.2468 7.84661 25.826C10.9555 28.6815 14.1104 31.468 17.2422 34.3005C17.7719 34.7841 18.3246 35.2677 18.8773 35.7743L20.305 30.1323Z" fill="#702E44"/>
                    <path d="M34.9728 7.21895C31.8178 3.83376 26.7746 3.23502 22.9519 5.8833C22.4452 6.22873 21.9847 6.64324 21.5241 7.03472L23.5967 10.1896C23.85 10.5811 23.85 11.0877 23.5736 11.4792L20.4418 15.9237L22.5604 19.8386C22.7907 20.2761 22.7216 20.8288 22.3762 21.1742L18.9219 24.7897L22.4222 29.1421C22.6525 29.4184 22.7216 29.7869 22.6525 30.1554L21.2708 35.6361C21.7774 35.1986 22.261 34.7611 22.7446 34.3235C25.8765 31.514 29.0314 28.7046 32.1402 25.849C34.9497 23.2698 36.815 20.1149 37.3217 16.2922C37.8053 12.93 37.3907 9.82118 34.9728 7.21895Z" fill="#702E44"/>
                </svg>';
    }

    return '<div class="'.$class.'">
                '.$icon_html.'
                <div>
                    '.$text.'
                </div>
            </div>';
}

function striptages($htmlText, $encoding = 'UTF-8') {
    // Remove script and style elements
    $htmlText = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', " ", $htmlText);
    $htmlText = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', " ", $htmlText);

    // Remove remaining HTML tags and replace them with a single space
    $htmlText = preg_replace('#<[^>]+>#', ' ', $htmlText);

    // Decode HTML entities
    $htmlText = html_entity_decode($htmlText, ENT_QUOTES, $encoding);

    // Replace multiple whitespace characters with a single space
    $htmlText = preg_replace('/\s+/', ' ', $htmlText);

    // Trim leading and trailing spaces
    $htmlText = trim($htmlText);

    return $htmlText;
}

function validate_username($username) {
    // Regular expression to match the allowed characters
    $pattern = '/^[a-zA-Z0-9_.]+$/';

    // Check if the username matches the pattern
    if (preg_match($pattern, $username)) {
        return true; // The username is valid
    } else {
        return false; // The username is invalid
    }
}

function resolve_page_data(&$variable, $data) {
    //zdebug($variable);
    if (isset($variable) && !strempty($variable)) {
        foreach($data AS $key=>$value) {
            //zdebug("$key = $value");
            $variable = str_ireplace("{{".$key."}}", $value, $variable);
        }
        //zdebug($variable);
    }
    //zdebug_hr();
}

function resolve_all_page_data(&$page_data, $data) {
    resolve_page_data($page_data["head_title"], $data);
    resolve_page_data($page_data["head_og_title"], $data);
    resolve_page_data($page_data["head_og_title_mobile"], $data);
    resolve_page_data($page_data["head_desc"], $data);
    resolve_page_data($page_data["head_og_desc"], $data);
    resolve_page_data($page_data["page_title"], $data);
    resolve_page_data($page_data["page_toptitle"], $data);
    resolve_page_data($page_data["page_subtitle"], $data);
}


function get_cur($cur_from, $amount_from, $cur_to = NULL) {
    global $zandora;
    global $rates;

    if (!$cur_to) $cur_to = $zandora["currency"];

    $amount = $amount_from * $rates[$cur_from] / $rates[$cur_to];

    return round($amount, 2);
}

function get_local_price($cur_from, $amount_from) {
    global $user;

    if ($cur_from != $user["currency"]) {
        // Calculate local price
        return get_cur($cur_from, $amount_from, $user["currency"]);
    } else {
        return $amount_from;
    }
}


function formatBytes($bytes, $digits = 1) {
    if ($bytes === null || $bytes === '') {
        return '';
    }

    $kb = $bytes / 1024;
    $mb = $kb / 1024;
    $gb = $mb / 1024;

    if ($gb >= 1) {
        return number_format($gb, $digits) . ' GB';
    } elseif ($mb >= 1) {
        return number_format($mb, $digits) . ' MB';
    } elseif ($kb >= 1) {
        return number_format($kb, $digits) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}


function generate_guid_v4() {
    if (function_exists('com_create_guid')) {
        return trim(com_create_guid(), '{}');
    }

    // Generate a version 4 UUID as per RFC 4122
    $data = random_bytes(16);

    // Set the version to 4 (0100) and the variant to 10 (10xx)
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function get_country_from_ip($ip) {
    global $pdo;

    if (in_array($ip, ['127.0.0.1', '::1'])) {
        return 'DK'; // or whatever you want as default
    }

    $sql = "SELECT `country_code` FROM `cache_ipcountry` WHERE `ip` = :ip";
    $params = [':ip' => $ip];
    $data = pdo_get_col($pdo, $sql, $params);

    if ($data) {
        return $data;
    }

    $url = "https://api.country.is/" . $ip;
    $response = @file_get_contents($url);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    $sql = "INSERT INTO `cache_ipcountry` (`ip`, `country_code`, `data`) VALUES (:ip, :country_code, :data)";
    $params = [
        ':ip' => $ip,
        ':country_code' => strtoupper($data->country),
        ':data' => $response
    ];
    pdo_execute($pdo, $sql, $params);

    return isset($data->country) ? $data->country : null;
}

function get_browser_language(): ?string {
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        // Example: "en-US,en;q=0.9,fr;q=0.8"
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if (!empty($langs[0])) {
            // Take the primary language part and make it uppercase
            return strtoupper(substr($langs[0], 0, 2));
        }
    }
    return null;
}

function timeToSeconds($time) {
    list($hours, $minutes, $seconds) = explode(":", $time);
    return ($hours * 3600) + ($minutes * 60) + $seconds;
}


function time_ago($timestamp) {
    global $months; // Ensure we access the global months array

    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;

    // Time calculations
    $seconds = $diff;
    $minutes = round($diff / 60);
    $hours = round($diff / 3600);
    $days = round($diff / 86400);

    if ($seconds < 60) {
        return "$seconds seconds ago";
    } elseif ($minutes < 60) {
        return "$minutes minutes ago";
    } elseif ($hours < 6) {
        return "$hours hours ago";
    } elseif (date("Y-m-d", $time) == date("Y-m-d", $now)) {
        return "i dag kl. " . date("H:i", $time);
    } elseif (date("Y-m-d", $time) == date("Y-m-d", strtotime("yesterday"))) {
        return "i går kl. " . date("H:i", $time);
    } else {
        // Get the month number (1-12)
        $monthNumber = (int) date("n", $time);
        // Get the 3-letter lowercase month name from the global months array
        $monthShort = $months[$monthNumber]["short"];

        return date("j. ", $time) . $monthShort . " kl. " . date("H:i", $time); // Example: "27. feb, 19:00"
    }
}



/*
 * Check if the current logged-in user has a given permission
 *
 * @param string $permissionName The name of the permission to check for
 * @return bool
 */
function hasPermission($permissionName) {
    global $pdo;

    if (!is_logged_in()) return false;

    $userId = get_user_id();

    // Single query to check both superuser and permission at the same time
    $sql = "
        SELECT EXISTS (
            -- Check if user is a superuser
            SELECT 1 FROM `user`
            WHERE `ID` = :user_id 
            AND `admin` = 1 
            AND `adminlevel` = 5
            AND `active` = 1 
            AND `suspended` = 0 
            AND `deleted` = 0
        ) 

        UNION 

        SELECT EXISTS (
            -- Check if user has permission directly or via a role
            SELECT 1 FROM `permissions` p
            LEFT JOIN `user_permissions` up ON up.`permission_id` = p.`id` AND up.`user_id` = :user_id
            LEFT JOIN `role_permissions` rp ON rp.`permission_id` = p.`id`
            LEFT JOIN `user_roles` ur ON ur.`role_id` = rp.`role_id` AND ur.`user_id` = :user_id
            WHERE p.`name` = :permission_name
            LIMIT 1
        )
    ";

    $params = [
        ":user_id" => $userId,
        ":permission_name" => $permissionName
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // If either query returns 1, the user has permission
    return (bool) array_sum($stmt->fetchAll(PDO::FETCH_COLUMN));
}

function in_comma_list(string $needle, $list, bool $case_insensitive = true): bool {
    if (!is_string($list) || trim($list) === '') {
        return false;
    }

    $items = explode(",", $list);

    if ($case_insensitive) {
        $needle = strtolower($needle);
        $items = array_map('strtolower', $items);
    }

    return in_array($needle, $items);
}