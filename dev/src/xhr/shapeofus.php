<?php
require_once __DIR__."/../inc/init.php";
require_once __DIR__."/../inc/lang.php";

$debug = FALSE;
$dont_write = FALSE;
//$debug = TRUE;
//$dont_write = TRUE;

// *** Get parameters *********************************************************
$cmd = get_param("cmd");


// *** Setup page for processing **********************************************
$result = array();
$success = TRUE;            // Set to FALSE later if a fail happens


// *** Custom Stuff ***********************************************************
$user_id = get_user_id();
//$user_fp = $_SESSION["ufp"] ?? NULL;
$user_ip = user_ip;


/*****************************************************************************

 ****************************************************************************/
if ($cmd == "newsletter-signup") {

    $email = get_param("email");

    if ($debug) {
        zdebug("Email: $email");
    }

    $apiKey = $config["BREVO"]["api"]["key"];
    $listId = $config["BREVO"]["list"]["newsletter"];

    // Validate email before sending
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $success = FALSE;
    }

    if ($success) {
        // STEP 1: Check if contact already exists
        $ch = curl_init($config["BREVO"]["api"]["host"] . "/v3/contacts/" . urlencode($email));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "accept: application/json",
            "api-key: $apiKey"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $contactExists = ($httpCode === 200); // Contact exists if status is 200

        // ✅ STEP 2: Create or Update Contact
        $contactData = [
            "email" => $email,
            "attributes" => [
                "SOON" => true // Set boolean attribute
            ],
            "listIds" => [$listId] // Add contact to the list
        ];

        $endpoint = $contactExists
            ? "/v3/contacts/" . urlencode($email) // If exists → UPDATE
            : "/v3/contacts"; // If not exists → CREATE

        $ch = curl_init($config["BREVO"]["api"]["host"] . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "accept: application/json",
            "api-key: $apiKey",
            "content-type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $contactExists ? "PUT" : "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($contactData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result["response"] = $response;
    }

    $result["success"] = true;


    /*****************************************************************************

     *****************************************************************************/
} else if ($cmd == "set_language") {

    $lang = get_param("lang");

    setLanguage($lang);

    // We are done, let's return the data
    $result["success"] = $success;

}


/*****************************************************************************
 * Return the result
 *****************************************************************************/

if(!$debug) header("Content-type: application/json");
echo json_encode($result);