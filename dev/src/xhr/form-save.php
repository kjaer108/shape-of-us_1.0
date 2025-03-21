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
if ($cmd == "save-step-state") {

    $curStep = get_param("step");

    if ($debug) {
        zdebug("Step: $curStep");
    }

    if ($curStep == 1) {

        $isMale = get_param("male");
        $isFemale = get_param("female");
        $isIntersex = get_param("intersex");
        $isMtF = get_param("mtf");
        $isFtM = get_param("ftm");

        $_SESSION["formdata"][1] = [
            "male" => $isMale,
            "female" => $isFemale,
            "intersex" => $isIntersex,
            "mtf" => $isMtF,
            "ftm" => $isFtM
        ];

    } elseif ($curStep == 2) {

        // http://localhost:63342/shape-of-us_1.0/dev/src/xhr/form-save.php?step=2&age=54&skin_tones=skin-light-to-medium&residence=DK&birth=FR&anatomy=anatomy-male-penis&cmd=save-step-state

        $age = get_param("age");
        $skinTones = get_param("skin_tones");
        $residence = get_param("residence");
        $birth = get_param("birth");
        $anatomy = get_param("anatomy");

        $_SESSION["formdata"][2] = [
            "age" => $age,
            "skin_tones" => $skinTones,
            "residence" => $residence,
            "birth" => $birth,
            "anatomy" => $anatomy
        ];

    } elseif ($curStep == 3) {

        $vulva_vulva = get_param("section-vulva-vulva");
        $vulva_vulva_text = get_param("medical-vulva-other-surgical-procedures-text");

        $vulva_breast = get_param("section-vulva-breast");
        $vulva_breast_text = get_param("medical-breast-other-surgical-procedures-text");

        $penis_penis = get_param("section-penis-penis");
        $penis_penis_text = get_param("medical-penis-other-surgical-procedures-text");

        $penis_breast = get_param("section-penis-breast");
        $penis_breast_text = get_param("medical-penis-breast-other-surgical-procedures-text");

        $trans_mtf = get_param("section-trans-mtf");
        $trans_mtf_text = get_param("mtf-other-surgical-procedures-text");

        $trans_ftm = get_param("section-trans-ftm");
        $trans_ftm_text = get_param("trans-ftm-bottom-other-surgical-procedures-text");

        $buttocks = get_param("section-buttocks");
        $buttocks_text = get_param("buttocks-other-surgical-procedures-text");

        $hormone = get_param("section-hormone");
        $hormone_text = get_param("hormone-other-procedures-text");

        $_SESSION["formdata"][3] = [
            "vulva_vulva" => $vulva_vulva,
            "vulva_vulva_text" => $vulva_vulva_text,
            "vulva_breast" => $vulva_breast,
            "vulva_breast_text" => $vulva_breast_text,
            "penis_penis" => $penis_penis,
            "penis_penis_text" => $penis_penis_text,
            "penis_breast" => $penis_breast,
            "penis_breast_text" => $penis_breast_text,
            "trans_mtf" => $trans_mtf,
            "trans_mtf_text" => $trans_mtf_text,
            "trans_ftm" => $trans_ftm,
            "trans_ftm_text" => $trans_ftm_text,
            "buttocks" => $buttocks,
            "buttocks_text" => $buttocks_text,
            "hormone" => $hormone,
            "hormone_text" => $hormone_text
        ];
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