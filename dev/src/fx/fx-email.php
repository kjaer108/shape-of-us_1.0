<?php
// TODO: We need to send out scheduled e-mails

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);


/*****************************************************************************
 * Functions for working with mail templates
 *****************************************************************************/

function replace_parameters($text, $param) {
    global $zandora;

    // Replace system variables
    $text = str_replace("{{year}}", date("Y"), $text);
    $text = str_replace("{{lang}}", $zandora["language"], $text);

    // Substitute parameters
    foreach ($param AS $key=>$value) {
        if ($key == "firstname") {
            if (preg_match('/^\w+/', $value, $matches)) {
                $value = $matches[0]; // "This"
            }
        }
        $text = str_replace("{{".$key."}}", $value, $text);
    }

    return $text;
}


/*****************************************************************************
 * Send mail functions
 *****************************************************************************/

/* Example:
        $email = "kjaer@kjaerland.dk";
        $param = ["token"=>"fiskerthomas"];
        send_mail_from_template("start-signup", $param, $email);

    Templates and required parameters:
        signup-start                    {{token}}, {{year}}
        signup-final                    {{firstname}}, {{year}}
        signup-reminder-1               {{firstname}}, {{token}}, {{year}}
        signup-email-inuse              {{token}}, {{year}}
        password-reset                  {{firstname}}, {{token}}, {{year}}
        account-delete                  {{firstname}}, {{token}}, {{year}}
        account-reactivated             {{firstname}}, {{year}}
        account-data-deleted-reminder   {{firstname}}, {{token}}, {{year}}
        account-data-deleted            {{firstname}}, {{year}}
*/
function send_mail_from_template($template_name, $param, $recipient) {
    global $config;
    global $pdo;

    $err = FALSE;
    $err_msg = "";

    // Get template from DB
    $sql = "SELECT `template`, `subject`, `altBody` FROM `mailtemplate` WHERE `name` LIKE :template_name";
    $params = [':template_name' => $template_name];

    try {
        $mail_data = pdo_get_row($pdo, $sql, $params);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Substitute parameters
    $mail_data["template"] = replace_parameters($mail_data["template"], $param);
    $mail_data["altBody"] = replace_parameters($mail_data["altBody"], $param);

    // Now send the e-mail
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $config["SMTP"]["host"];
        $mail->SMTPAuth = $config["SMTP"]["SMTPAuth"];
        $mail->Username = $config["SMTP"]["username"];
        $mail->Password = $config["SMTP"]["password"];
        $mail->SMTPSecure = $config["SMTP"]["SMTPSecure"];
        $mail->Port = $config["SMTP"]["port"];

        //Recipients
        $mail->setFrom('info@zandora.net', 'Zandora ApS');
        $mail->addAddress($recipient);
        $mail->addBCC('email@zandora.net', 'Zandora Mail Archive');
        $mail->addReplyTo('info@zandora.net', 'Zandora ApS');

        //Content
        $mail->isHTML(true);
        $mail->Subject = $mail_data["subject"];
        $mail->Body = $mail_data["template"];
        $mail->AltBody = $mail_data["altBody"];

        if (!$mail->send()) {
            $err = TRUE;
            $err_msg = $mail->ErrorInfo;
        }

    } catch (Exception $e) {
        $err = TRUE;
        $err_msg = $mail->ErrorInfo;
    }

    return [$err, $err_msg];
}


function send_mail($subject, $body, $recipient, $system = FALSE) {
    global $config;
    global $pdo;

    $err = FALSE;
    $err_msg = "";

    // Now send the e-mail
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $config["SMTP"]["host"];
        $mail->SMTPAuth = $config["SMTP"]["SMTPAuth"];
        $mail->Username = $config["SMTP"]["username"];
        $mail->Password = $config["SMTP"]["password"];
        $mail->SMTPSecure = $config["SMTP"]["SMTPSecure"];
        $mail->Port = $config["SMTP"]["port"];

        //Recipients
        if ($system) {
            $mail->setFrom('thomas@zandora.net', 'Zandora System');
            $mail->addReplyTo('thomas@zandora.net', 'Zandora System');
        } else {
            $mail->setFrom('info@zandora.net', 'Zandora ApS');
            $mail->addReplyTo('info@zandora.net', 'Zandora ApS');
        }

        // Check if $recipient is an array or a single string
        if (is_array($recipient)) {
            foreach ($recipient as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($recipient);
        }

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        //$mail->AltBody = $mail_data["altBody"];

        if (!$mail->send()) {
            $err = TRUE;
            $err_msg = $mail->ErrorInfo;
        }


    } catch (Exception $e) {
        $err = TRUE;
        $err_msg = $mail->ErrorInfo;
    }

    return [$err, $err_msg];
}


/*****************************************************************************
    Schedule Email Functions
 *****************************************************************************/

function schedule_email($template_name, $param, $recipient, $user_id, $send_time) {
    global $pdo;

    $param = json_encode($param);

    if (substr($send_time, 0, 1) === "+" || substr($send_time, 0, 1) === "-") {
        $send_time = date("Y-m-d H:i:s", strtotime($send_time));
    }

    $sql = "INSERT INTO `mail_schedule` (`template`, `params`, `email`, `user_id`, `schedule`) VALUES (:template_name, :param, :recipient, :user_id, :send_time)";
    $params = [
        ':template_name' => $template_name,
        ':param' => $param,
        ':recipient' => $recipient,
        ':user_id' => $user_id,
        ':send_time' => $send_time,
    ];

    try {
        pdo_execute($pdo, $sql, $params);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

}

function delete_scheduled_email($user_id, $template_name) {
    global $pdo;

    $sql = "DELETE FROM `mail_schedule` WHERE `user_id` = :user_id AND `template` = :template_name";
    $params = [':user_id' => $user_id, ':template_name' => $template_name];

    try {
        pdo_execute($pdo, $sql, $params);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


/*****************************************************************************
    E-mail validation functions
 *****************************************************************************/

// Function to determine the validity status and reason for an email based on the API response
function isEmailValid($email) {
    global $email_validation;
    global $config;

    // API endpoint URL
    $url = "https://api.emailable.com/v1/verify?email=".$email."&api_key=".$config['emailable']['apiKey'];

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true, // Return the response instead of outputting it
    ]);

    // Execute the cURL request
    $response_json = curl_exec($curl);

    // Close cURL session
    curl_close($curl);

    // Check if cURL request was successful
    if ($response_json === false) {
        // cURL request failed
        echo "cURL error: " . curl_error($curl);
    } else {
        // Decode JSON response to PHP array
        $response = json_decode($response_json, true);

        switch ($response['state']) {
            case 'undeliverable':
                return ['status' => 0, 'reason' => ['reason_label' => $email_validation[$response['reason']]['label'], 'reason_text' => $email_validation[$response['reason']]['text']]];
                break;
            case 'risky':
                return ['status' => 1, 'reason' => ['reason_label' => $email_validation[$response['reason']]['label'], 'reason_text' => $email_validation[$response['reason']]['text']]];
                break;
            case 'deliverable':
                return ['status' => 2, 'reason' => ['reason_label' => $email_validation[$response['reason']]['label'], 'reason_text' => $email_validation[$response['reason']]['text']]];
                break;
            default:
                return ['status' => 0, 'reason' => ['reason_label' => $email_validation['unknown_state']['label'], 'reason_text' => $email_validation['unknown_state']['text']]];
                break;
        }

    }
}