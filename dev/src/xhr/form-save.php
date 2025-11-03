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
$USER_IP = USER_IP;


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

        // Aggregate Step 3 checkbox ids by section prefix and keep related text fields
        $collectChecked = function(array $prefixes) {
            $out = [];
            foreach ($_POST as $key => $val) {
                // Accept common truthy representations from form.js
                if ($val === '1' || $val === 1 || $val === 'on' || $val === true) {
                    foreach ($prefixes as $pfx) {
                        if (strpos($key, $pfx) === 0) {
                            $out[] = $key;
                            break;
                        }
                    }
                }
            }
            // Return comma-separated list of ids
            return implode(',', $out);
        };

        // Build aggregated values per section
        $vulva_vulva      = $collectChecked(['medical-vulva-']);
        $vulva_vulva_text = get_param('medical-vulva-other-surgical-procedures-text');

        $vulva_breast      = $collectChecked(['medical-breast-']);
        $vulva_breast_text = get_param('medical-breast-other-surgical-procedures-text');

        $penis_penis      = $collectChecked(['medical-penis-']);
        $penis_penis_text = get_param('medical-penis-other-surgical-procedures-text');

        $penis_breast      = $collectChecked(['medical-penis-breast-']);
        $penis_breast_text = get_param('medical-penis-breast-other-surgical-procedures-text');

        $trans_mtf      = $collectChecked(['mtf-']);
        $trans_mtf_text = get_param('mtf-other-surgical-procedures-text');

        $trans_ftm      = $collectChecked(['trans-ftm-']);
        $trans_ftm_text = get_param('trans-ftm-bottom-other-surgical-procedures-text');

        $buttocks      = $collectChecked(['buttocks-']);
        $buttocks_text = get_param('buttocks-other-surgical-procedures-text');

        $hormone      = $collectChecked(['hormone-']);
        $hormone_text = get_param('hormone-other-procedures-text');

        $_SESSION['formdata'][3] = [
            'vulva_vulva' => $vulva_vulva,
            'vulva_vulva_text' => $vulva_vulva_text ?: null,
            'vulva_breast' => $vulva_breast,
            'vulva_breast_text' => $vulva_breast_text ?: null,
            'penis_penis' => $penis_penis,
            'penis_penis_text' => $penis_penis_text ?: null,
            'penis_breast' => $penis_breast,
            'penis_breast_text' => $penis_breast_text ?: null,
            'trans_mtf' => $trans_mtf,
            'trans_mtf_text' => $trans_mtf_text ?: null,
            'trans_ftm' => $trans_ftm,
            'trans_ftm_text' => $trans_ftm_text ?: null,
            'buttocks' => $buttocks,
            'buttocks_text' => $buttocks_text ?: null,
            'hormone' => $hormone,
            'hormone_text' => $hormone_text ?: null,
        ];

    } elseif ($curStep == 4) {

        $hair_chest = get_param("chest_hair");
        $hair_above = get_param("genital_hair_above");
        $hair_below = get_param("genital_hair");
        $hair_buttocks = get_param("buttocks_hair");

        $marks = get_param("marks");
        $marks_text = get_param("marks-scars-text");

        $pregnancy = get_param("pregnancy");
        $vaginal_birth = get_param("vaginal_birth");
        $c_section = get_param("c_section");
        $breastfeeding = get_param("breastfeeding");

        $piercings = get_param("piercings");
        $piercings_other_text = get_param("piercings-other-text");

        $tattoos = get_param("tattoos");
        $tattoos_other_text = get_param("tattoos-other-text");

        $hormonal_influence = get_param("hormonal_influence");

        $menstrual_cycle = get_param("menstrual_cycle");

        $_SESSION["formdata"][4] = [
            "hair_chest" => $hair_chest,
            "hair_above" => $hair_above,
            "hair_below" => $hair_below,
            "hair_buttocks" => $hair_buttocks,
            "marks" => $marks,
            "marks_text" => $marks_text,
            "pregnancy" => $pregnancy,
            "vaginal_birth" => $vaginal_birth,
            "c_section" => $c_section,
            "breastfeeding" => $breastfeeding,
            "piercings" => $piercings,
            "piercings_other_text" => $piercings_other_text,
            "tattoos" => $tattoos,
            "tattoos_other_text" => $tattoos_other_text,
            "hormonal_influence" => $hormonal_influence,
            "menstrual_cycle" => $menstrual_cycle
        ];

    } else {
        $success = FALSE;

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