<?php
$page = ["name"=>"form", "translate"=>true, "sourcelang" => "en"];
require_once "src/inc/init.php";

zdebug(IS_LOCALHOST);


//log_page_load();

//unset($_SESSION["formdata"]);
zdebug($_SESSION["formdata"] ?? null);
//zdebug($_SESSION["formdata"][2]["anatomy"] ?? null);

// Get the current step
$curStep = get_param("step", 1);
//zdebug("curStep from URL: ".$curStep);

// Validate current step
if ($curStep < 1 || $curStep > 5) {
    $curStep = 1;
}

if ($curStep > 1 && !isset($_SESSION["formdata"][$curStep - 1])) {
    $curStep = 1;
}

zdebug("Active curStep: ".$curStep);

// *** Save form data *********************************************************

function generateUniquePhotoId(PDO $pdo): int {
    $ranges = [
        ['min' => 1000, 'max' => 9999],      // Tier 2: 100–9999
        ['min' => 1000, 'max' => 99999],     // Tier 3: 100–999999
        ['min' => 1000, 'max' => 999999],    // Tier 4: 100–9999999
    ];

    foreach ($ranges as $range) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM sou_form_entries 
            WHERE photo_id BETWEEN :min AND :max
        ");
        $stmt->execute(['min' => $range['min'], 'max' => $range['max']]);
        $usedCount = (int) $stmt->fetchColumn();

        $totalPossible = $range['max'] - $range['min'] + 1;
        $threshold = (int) ($totalPossible * 0.8);

        if ($usedCount < $threshold) {
            // Try generating a unique ID
            $attempts = 0;
            do {
                $photoId = random_int($range['min'], $range['max']);
                $stmt = $pdo->prepare("SELECT 1 FROM sou_form_entries WHERE photo_id = :photo_id LIMIT 1");
                $stmt->execute(['photo_id' => $photoId]);
                $exists = $stmt->fetchColumn() > 0;
                $attempts++;
            } while ($exists && $attempts < 1000);

            if (!$exists) {
                return $photoId;
            }
        }
    }

    throw new Exception("Unable to generate a unique photo_id – all ranges are saturated.");
}


if ($curStep == 5) {

    $photoId = generateUniquePhotoId($pdo);
    $ipHashed = hash('sha256', USER_IP);
    $formData = $_SESSION["formdata"];

// Insert main entry
    $sql = "
INSERT INTO sou_form_entries (
    id, photo_id, male, female, intersex, mtf, ftm,
    age, skin_tones, residence, birth, anatomy,
    vulva_vulva, vulva_vulva_text,
    vulva_breast, vulva_breast_text,
    penis_penis, penis_penis_text,
    penis_breast, penis_breast_text,
    trans_mtf, trans_mtf_text,
    trans_ftm, trans_ftm_text,
    buttocks, buttocks_text,
    hormone, hormone_text,
    hair_chest, hair_above, hair_below, hair_buttocks,
    marks, marks_text, pregnancy, vaginal_birth, c_section, breastfeeding,
    piercings, piercings_other_text, tattoos, tattoos_other_text,
    hormonal_influence, menstrual_cycle, ip
) VALUES (
    UUID(), :photo_id, :male, :female, :intersex, :mtf, :ftm,
    :age, :skin_tones, :residence, :birth, :anatomy,
    :vulva_vulva, :vulva_vulva_text,
    :vulva_breast, :vulva_breast_text,
    :penis_penis, :penis_penis_text,
    :penis_breast, :penis_breast_text,
    :trans_mtf, :trans_mtf_text,
    :trans_ftm, :trans_ftm_text,
    :buttocks, :buttocks_text,
    :hormone, :hormone_text,
    :hair_chest, :hair_above, :hair_below, :hair_buttocks,
    :marks, :marks_text, :pregnancy, :vaginal_birth, :c_section, :breastfeeding,
    :piercings, :piercings_other_text, :tattoos, :tattoos_other_text,
    :hormonal_influence, :menstrual_cycle, :ip
)";

    $params = [
        ':photo_id' => $photoId,
        ':male'     => $formData[1]['male'],
        ':female'   => $formData[1]['female'],
        ':intersex' => $formData[1]['intersex'],
        ':mtf'      => $formData[1]['mtf'],
        ':ftm'      => $formData[1]['ftm'],
        ':age'        => $formData[2]['age'],
        ':skin_tones' => $formData[2]['skin_tones'],
        ':residence'  => $formData[2]['residence'],
        ':birth'      => $formData[2]['birth'],
        ':anatomy'    => json_encode(explode(',', $formData[2]['anatomy'] ?? '')),
        ':vulva_vulva'        => json_encode(explode(',', $formData[3]['vulva_vulva'] ?? '')),
        ':vulva_vulva_text'   => $formData[3]['vulva_vulva_text'] ?? null,
        ':vulva_breast'       => json_encode(explode(',', $formData[3]['vulva_breast'] ?? '')),
        ':vulva_breast_text'  => $formData[3]['vulva_breast_text'] ?? null,
        ':penis_penis'        => json_encode(explode(',', $formData[3]['penis_penis'] ?? '')),
        ':penis_penis_text'   => $formData[3]['penis_penis_text'] ?? null,
        ':penis_breast'       => json_encode(explode(',', $formData[3]['penis_breast'] ?? '')),
        ':penis_breast_text'  => $formData[3]['penis_breast_text'] ?? null,
        ':trans_mtf'          => json_encode(explode(',', $formData[3]['trans_mtf'] ?? '')),
        ':trans_mtf_text'     => $formData[3]['trans_mtf_text'] ?? null,
        ':trans_ftm'          => json_encode(explode(',', $formData[3]['trans_ftm'] ?? '')),
        ':trans_ftm_text'     => $formData[3]['trans_ftm_text'] ?? null,
        ':buttocks'           => json_encode(explode(',', $formData[3]['buttocks'] ?? '')),
        ':buttocks_text'      => $formData[3]['buttocks_text'] ?? null,
        ':hormone'            => json_encode(explode(',', $formData[3]['hormone'] ?? '')),
        ':hormone_text'       => $formData[3]['hormone_text'] ?? null,
        ':hair_chest'         => $formData[4]['hair_chest'] ?? null,
        ':hair_above'         => $formData[4]['hair_above'] ?? null,
        ':hair_below'         => $formData[4]['hair_below'] ?? null,
        ':hair_buttocks'      => $formData[4]['hair_buttocks'] ?? null,
        ':marks'              => json_encode(explode(',', $formData[4]['marks'] ?? '')),
        ':marks_text'         => $formData[4]['marks_text'] ?? null,
        ':pregnancy'          => $formData[4]['pregnancy'] ?? null,
        ':vaginal_birth'      => $formData[4]['vaginal_birth'] ?? null,
        ':c_section'          => $formData[4]['c_section'] ?? null,
        ':breastfeeding'      => $formData[4]['breastfeeding'] ?? null,
        ':piercings'          => json_encode(explode(',', $formData[4]['piercings'] ?? '')),
        ':piercings_other_text' => $formData[4]['piercings_other_text'] ?? null,
        ':tattoos'            => json_encode(explode(',', $formData[4]['tattoos'] ?? '')),
        ':tattoos_other_text' => $formData[4]['tattoos_other_text'] ?? null,
        ':hormonal_influence' => $formData[4]['hormonal_influence'] ?? null,
        ':menstrual_cycle'    => $formData[4]['menstrual_cycle'] ?? null,
        ':ip' => $ipHashed
    ];

    pdo_execute($pdo, $sql, $params);

// Get UUID of inserted row
    $entryId = pdo_get_col($pdo, "SELECT id FROM sou_form_entries WHERE photo_id = :photo_id ORDER BY timestamp DESC LIMIT 1", [
        ':photo_id' => $photoId
    ]);

// Helper: insert values into support tables
    $insertSupportValues = function ($table, $values) use ($pdo, $entryId) {
        if (is_string($values)) {
            $values = explode(',', $values);
        }

        $values = array_filter(array_map('trim', $values));

        if (empty($values)) return;

        $sql = "INSERT INTO `$table` (entry_id, value) VALUES (:entry_id, :value)";
        foreach ($values as $val) {
            $params = [
                ':entry_id' => $entryId,
                ':value'    => $val
            ];
            pdo_execute($pdo, $sql, $params);
        }
    };

// Insert into support tables
    $insertSupportValues('sou_form_anatomy', $formData[2]['anatomy'] ?? '');
    $insertSupportValues('sou_form_marks', $formData[4]['marks'] ?? '');
    $insertSupportValues('sou_form_piercings', $formData[4]['piercings'] ?? '');
    $insertSupportValues('sou_form_tattoos', $formData[4]['tattoos'] ?? '');
    $insertSupportValues('sou_form_hormone', $formData[3]['hormone'] ?? '');
    $insertSupportValues('sou_form_buttocks', $formData[3]['buttocks'] ?? '');
    $insertSupportValues('sou_form_trans_mtf', $formData[3]['trans_mtf'] ?? '');
    $insertSupportValues('sou_form_trans_ftm', $formData[3]['trans_ftm'] ?? '');
    $insertSupportValues('sou_form_vulva_vulva', $formData[3]['vulva_vulva'] ?? '');
    $insertSupportValues('sou_form_vulva_breast', $formData[3]['vulva_breast'] ?? '');
    $insertSupportValues('sou_form_penis_penis', $formData[3]['penis_penis'] ?? '');
    $insertSupportValues('sou_form_penis_breast', $formData[3]['penis_breast'] ?? '');


}



// *** Setp 1 form data *******************************************************
$isMale = $_SESSION["formdata"][1]["male"] ?? false;
$isFemale = $_SESSION["formdata"][1]["female"] ?? false;
$isIntersex = $_SESSION["formdata"][1]["intersex"] ?? false;
$isMtF = $_SESSION["formdata"][1]["mtf"] ?? false;
$isFtM = $_SESSION["formdata"][1]["ftm"] ?? false;


//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body>

<!-- Navigation bar (Page header) -->
<header class="container-fluid position-absolute top-0 start-0 end-0">
    <div class="d-flex align-items-center justify-content-sm-end justify-content-between gap-2 py-2">
        <div class="d-sm-none d-flex align-items-center gap-4">
            <div>
                <img src="assets/img/shape-of-us.png" width="180" alt="Shape of Us">
            </div>
            <a href="#" target="_blank" rel="noopener" class="d-block mt-n1">
                <img src="assets/img/zandora.png" width="108" alt="by Zandora">
            </a>
        </div>
        <div class="dropdown mt-sm-2">
            <button class="btn btn-lg btn-icon btn-light rounded-circle bg-transparent border-0 text-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Switch language">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><g clip-path="url(#A)" fill="currentColor"><path d="M12.84 14.317c-.079-.095-.197-.15-.32-.15H7.479c-.124 0-.241.055-.32.15a.42.42 0 0 0-.09.342C7.668 17.904 8.819 20 10 20s2.331-2.096 2.93-5.341c.022-.122-.01-.247-.09-.342zm6.685-7.361c-.055-.173-.215-.29-.397-.29h-4.692a.42.42 0 0 0-.309.137c-.079.087-.118.204-.106.321A28.78 28.78 0 0 1 14.167 10c0 .95-.049 1.918-.145 2.875-.012.117.027.234.106.321s.191.137.309.137h4.692c.181 0 .342-.117.397-.29a10 10 0 0 0 .475-3.044 9.99 9.99 0 0 0-.475-3.044zM13.8 5.486c.034.201.207.348.411.348h4.206a.42.42 0 0 0 .359-.204c.075-.127.078-.283.007-.412-1.251-2.292-3.4-4.038-5.896-4.79-.161-.05-.338.005-.445.138s-.122.316-.039.464c.607 1.088 1.09 2.629 1.398 4.457zm-1.037 1.181H7.238c-.213 0-.391.16-.414.372A27.73 27.73 0 0 0 6.667 10a27.73 27.73 0 0 0 .157 2.961c.023.212.201.372.414.372h5.525c.213 0 .391-.16.414-.372A27.66 27.66 0 0 0 13.334 10a27.66 27.66 0 0 0-.157-2.961c-.023-.212-.201-.372-.414-.372zm-11.18-.834h4.206c.203 0 .377-.147.411-.347.307-1.828.791-3.369 1.398-4.457.083-.148.067-.332-.039-.464S7.275.377 7.113.427a10.08 10.08 0 0 0-5.896 4.79c-.07.129-.068.286.007.412a.42.42 0 0 0 .358.204zm4.29 7.363c.079-.087.118-.204.106-.321A28.8 28.8 0 0 1 5.833 10c0-.95.049-1.918.145-2.875a.41.41 0 0 0-.106-.321.42.42 0 0 0-.309-.137H.872c-.181 0-.342.117-.397.29a9.99 9.99 0 0 0 0 6.087c.055.173.215.29.397.29h4.692c.118 0 .23-.05.309-.137zm.326 1.318c-.034-.201-.208-.348-.411-.348H1.583a.42.42 0 0 0-.358.204c-.075.127-.078.283-.007.412 1.251 2.292 3.4 4.038 5.896 4.79a.41.41 0 0 0 .12.018.42.42 0 0 0 .325-.155c.106-.133.122-.316.039-.464-.607-1.088-1.091-2.629-1.398-4.457zm12.218-.347h-4.206c-.203 0-.377.147-.411.348-.307 1.828-.791 3.369-1.398 4.457-.083.148-.067.332.039.464.081.1.201.155.325.155a.41.41 0 0 0 .12-.018 10.08 10.08 0 0 0 5.896-4.79c.07-.129.068-.286-.007-.412a.42.42 0 0 0-.359-.204zM7.16 5.683c.079.095.197.15.32.15h5.041c.124 0 .241-.055.32-.15s.112-.221.089-.342C12.332 2.096 11.181 0 10.001 0S7.669 2.096 7.07 5.341c-.022.122.01.247.089.342z"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h20v20H0z"/></clipPath></defs></svg>
            </button>
            <ul class="dropdown-menu p-4">
                <?php foreach ($language_support as $lang): ?>
                    <li>
                        <div class="form-check mb-3">
                            <input id="lang-<?php echo $lang; ?>" type="radio" name="site-language"
                                   class="form-check-input border"
                                   value="<?php echo $lang; ?>"
                                <?php echo ($lang === $selectedLang) ? 'checked' : ''; ?>
                                   onchange="setLanguage('<?php echo $lang; ?>')">
                            <label for="lang-<?php echo $lang; ?>" class="form-check-label">
                                <?php echo $language_data['name'][$lang]; ?>
                            </label>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Page progress (mobile) -->
    <div class="progress d-sm-none mx-n3" role="progressbar" aria-label="Steps progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: 4px">
        <div class="progress-bar" style="width: 20%"></div>
    </div>
</header>

<!-- Page content -->
<main class="content-wrapper">
    <div class="container-fluid px-0 min-vh-100 d-flex">
        <div class="row gx-0 w-100">

            <!-- Sidebar -->
            <aside class="col-lg-3 col-sm-4 px-3 d-sm-block d-none" style="background-color: #e5ded1;">
                <div class="p-3 sticky-top">
                    <a href="#" target="_blank" rel="noopener" class="d-block mb-lg-3 mb-2">
                        <img src="assets/img/zandora.png" width="106" alt="by Zandora">
                    </a>
                    <div class="mb-4 pb-md-2">
                        <img src="assets/img/shape-of-us.png" width="318" alt="Shape of Us">
                    </div>

                    <!-- Illustration -->
                    <div class="mb-4 pb-md-2">
                        <div class="ratio ratio-1x1 sidebar-illustration" style="max-width: 200px;">
                            <img src="assets/img/wizard/sidebar-illustration/01.svg" alt="Shape">
                            <img src="assets/img/wizard/sidebar-illustration/02.svg" alt="Shape">
                            <img src="assets/img/wizard/sidebar-illustration/03.svg" alt="Shape">
                        </div>
                    </div>

                    <!-- Navigation -->
                    <ul class="nav flex-column gap-3" style="--zs-nav-link-font-size: 1.25rem; --zs-nav-link-padding-y: 0; --zs-nav-link-padding-x: 0;">
                        <li class="nav-item">
                            <a href="<?= get_url("form-step", 1) ?>" class="nav-link align-items-start<?= ($curStep <= 1 || $curStep == 5) ? " pe-none" : "" ?><?= $curStep == 1 ? " active" : "" ?>">
<?php if ($curStep <= 1) { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">01</span>
<?php } else { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="d-block mt-1" width="28" height="22" fill="none"><path d="M22.065 5.034c-3.126.938-7.112 3.446-10.787 7.743l-2.172-2.203c-.334-.349-.955-.349-1.289 0L6.219 12.21c-.31.327-.286.807.048 1.091l4.916 4.319c.406.349 1.074.262 1.36-.196 2.625-4.341 5.441-7.569 10.214-11.277.573-.458.048-1.33-.692-1.112z" fill="currentColor"/></svg>
                                </span>
<?php } ?>
                                <?= __("Welcome") ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= get_url("form-step", 2) ?>" class="nav-link align-items-start<?= ($curStep <= 2 || $curStep == 5) ? " pe-none" : "" ?><?= $curStep == 2 ? " active" : "" ?>">
<?php if ($curStep <= 2) { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">02</span>
<?php } else { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="d-block mt-1" width="28" height="22" fill="none"><path d="M22.065 5.034c-3.126.938-7.112 3.446-10.787 7.743l-2.172-2.203c-.334-.349-.955-.349-1.289 0L6.219 12.21c-.31.327-.286.807.048 1.091l4.916 4.319c.406.349 1.074.262 1.36-.196 2.625-4.341 5.441-7.569 10.214-11.277.573-.458.048-1.33-.692-1.112z" fill="currentColor"/></svg>
                                </span>
<?php } ?>
                                <?= __("General Information") ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= get_url("form-step", 3) ?>" class="nav-link align-items-start<?= ($curStep <= 3 || $curStep == 5) ? " pe-none" : "" ?><?= $curStep == 3 ? " active" : "" ?>">
<?php if ($curStep <= 3) { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">03</span>
<?php } else { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="d-block mt-1" width="28" height="22" fill="none"><path d="M22.065 5.034c-3.126.938-7.112 3.446-10.787 7.743l-2.172-2.203c-.334-.349-.955-.349-1.289 0L6.219 12.21c-.31.327-.286.807.048 1.091l4.916 4.319c.406.349 1.074.262 1.36-.196 2.625-4.341 5.441-7.569 10.214-11.277.573-.458.048-1.33-.692-1.112z" fill="currentColor"/></svg>
                                </span>
<?php } ?>
                                <?= __("Surgical or Medical History") ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= get_url("form-step", 4) ?>" class="nav-link align-items-start<?= ($curStep <= 4 || $curStep == 5) ? " pe-none" : "" ?>
<?= $curStep == 4 ? " active" : "" ?>">
<?php if ($curStep <= 4) { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">04</span>
<?php } else { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="d-block mt-1" width="28" height="22" fill="none"><path d="M22.065 5.034c-3.126.938-7.112 3.446-10.787 7.743l-2.172-2.203c-.334-.349-.955-.349-1.289 0L6.219 12.21c-.31.327-.286.807.048 1.091l4.916 4.319c.406.349 1.074.262 1.36-.196 2.625-4.341 5.441-7.569 10.214-11.277.573-.458.048-1.33-.692-1.112z" fill="currentColor"/></svg>
                                </span>
                            <?php } ?>
                                <?= __("Additional Information (optional)") ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link align-items-start<?= $curStep <= 5 ? " pe-none" : "" ?><?= $curStep == 5 ? " active" : "" ?>">
    <?php if ($curStep <= 5) { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">05</span>
<?php } else { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="d-block mt-1" width="28" height="22" fill="none"><path d="M22.065 5.034c-3.126.938-7.112 3.446-10.787 7.743l-2.172-2.203c-.334-.349-.955-.349-1.289 0L6.219 12.21c-.31.327-.286.807.048 1.091l4.916 4.319c.406.349 1.074.262 1.36-.196 2.625-4.341 5.441-7.569 10.214-11.277.573-.458.048-1.33-.692-1.112z" fill="currentColor"/></svg>
                                </span>
<?php } ?>
                                <?= __("Ready for picture") ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>

            <!-- Content -->
<?php if ($curStep == 1) { ?>

            <div class="col-lg-9 col-sm-8 py-5 px-lg-5 px-sm-4 px-3">
                <div class="col-xxl-8 col-lg-11 py-lg-5 py-sm-4 py-5 px-xxl-5 px-lg-4">
                    <div class="ff-extra fs-lg">
                        <h1 class="fw-semibold">
                            <?= __("Welcome to the <br> Shape of Us Project") ?>
                        </h1>
                        <p><?= __("Thank you for participating in the Shape of Us project. Your contribution helps celebrate the natural diversity of bodies and experiences.") ?></p>
                        <p><?= __("This form is designed to ensure you only see relevant questions based on your selections. Please take your time to answer as accurately as possible. All responses remain anonymous.") ?></p>

                        <!-- Form -->
                        <form class="mt-lg-5 mt-4 pt-lg-0 pt-md-2" data-step="1">
                            <h2 class="h3 mb-md-4 mb-3 fw-semibold">
                                <?= __("Select the categories that best apply to you") ?>
                            </h2>
                            <div class="mb-md-4 mb-3">
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="category-male" value="male" type="checkbox" class="form-check-input"<?= $isMale ? " checked" : "" ?>>
                                    <label for="category-male" class="form-check-label">
                                        <?= __("Male") ?>
                                    </label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="category-female" value="female" type="checkbox" class="form-check-input"<?= $isFemale ? " checked" : "" ?>>
                                    <label for="category-female" class="form-check-label">
                                        <?= __("Female") ?>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-md-4 mb-3">
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="category-intersex" value="intersex" type="checkbox" class="form-check-input"<?= $isIntersex ? " checked" : "" ?>>
                                    <label for="category-intersex" class="form-check-label">
                                        <?= __("Intersex") ?>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-md-4 mb-3">
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="category-mtf" value="mtf" type="checkbox" class="form-check-input"<?= $isMtF ? " checked" : "" ?>>
                                    <label for="category-mtf" class="form-check-label">
                                        <?= __("Male to Female (MtF)") ?>
                                    </label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="category-ftm" value="tfm" type="checkbox" class="form-check-input"<?= $isFtM ? " checked" : "" ?>>
                                    <label for="category-ftm" class="form-check-label">
                                        <?= __("Female to Male (FtM)") ?>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="d-flex flex-sm-row flex-column pt-2">
                                <button type="submit" class="btn btn-lg btn-primary rounded-pill py-3">
                                    <?= __("Continue") ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M5.628 3.182c-.244.101-.452.272-.599.491s-.225.477-.225.741v7.448c0 .264.078.521.225.741s.355.39.598.491.512.127.77.076.496-.178.683-.365l3.724-3.724c.25-.25.39-.589.39-.943s-.14-.693-.39-.943L7.081 3.472c-.186-.186-.424-.313-.682-.365s-.527-.025-.77.076z" fill="currentColor"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<?php } elseif ($curStep == 2) { ?>

            <div class="col-lg-9 col-sm-8 py-5 px-lg-5 px-sm-4 px-3">
                <div class="col-xxl-8 col-lg-11 py-lg-5 py-sm-4 py-5 px-xxl-5 px-lg-4">

                    <!-- Back button (mobile) -->
                    <a href="wizard-01.html" class="btn btn-lg btn-link px-0 mb-3 d-sm-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" fill="none"><path d="M10.372 3.182c.244.101.452.272.598.491s.225.477.225.741v7.448c0 .264-.078.521-.225.741s-.355.39-.598.491-.512.127-.77.076-.496-.178-.683-.365L5.195 9.081c-.25-.25-.39-.589-.39-.943s.14-.693.39-.943l3.724-3.724c.186-.186.424-.313.682-.365s.527-.025.77.076z" fill="currentColor"/></svg>
                        <?= __("Back") ?>
                    </a>

                    <div class="ff-extra">
                        <h1 class="fw-semibold">
                            <?= __("General Information") ?>
                        </h1>
                        <p class="fs-lg">
                            <?= __("This section collects essential details to help categorize images accurately and ensure meaningful representation in the gallery. All fields are mandatory and must be completed before submission. This information allows others to find relatable images based on factors such as age, skin tone, and anatomy while maintaining full anonymity.") ?>
                        </p>

                        <!-- Form -->
                        <form class="mt-lg-5 mt-4 pt-lg-0 pt-md-2" data-step="2">
                            <input type="hidden" id="category" value="<?= isset($_SESSION["formdata"][1])
                                ? implode(",", array_keys(array_filter($_SESSION["formdata"][1], fn($value) => $value == 1)))
                                : "" ?>">

                            <!-- Age -->
                            <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <h2 class="h3 mb-2 fw-semibold">
                                    <?= __("Age") ?>
                                </h2>
                                <p class="text-body-secondary">
                                    <?= __("Please provide your age at the time the photo was taken. This helps others find relatable images based on life stages.") ?>
                                </p>
                                <div class="d-inline-flex align-items-center gap-2 border-bottom border-dark fw-medium text-primary">
                                    <input type="number" id="general-age" value="<?= $_SESSION["formdata"][2]["age"] ?? '' ?>" class="form-control form-control-lg bg-transparent border-0 px-0" min="18" style="width: 60px;">
                                    <span><?= __("years") ?></span>
                                </div>
                            </div>

                            <!-- Skin -->
                            <?php
                            $selectedSkinTone = $_SESSION["formdata"][2]["skin_tones"] ?? null;
                            ?>

                            <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <h2 class="h3 mb-2 fw-semibold">
                                    <?= __("Skin tone") ?>
                                </h2>
                                <p class="text-body-secondary">
                                    <?= __("Select the skin tone that best represents your overall appearance. This helps showcase the natural diversity of skin colors and textures.") ?>
                                </p>
                                <div class="row row-cols-md-2 row-cols-1 gy-1 gx-2 pt-2" style="max-width: 540px;">
                                    <?php
                                    $skinTones = [
                                        "skin-very-light" => "Very light",
                                        "skin-light" => "Light",
                                        "skin-light-to-medium" => "Light to medium",
                                        "skin-medium" => "Medium",
                                        "skin-medium-to-deep" => "Medium to deep",
                                        "skin-deep" => "Deep",
                                        "skin-deep-rich" => "Deep rich",
                                        "skin-very-deep" => "Very deep",
                                        "skin-dark-rich" => "Dark rich",
                                        "skin-darkest" => "Darkest"
                                    ];

                                    $gradientColors = [
                                        "skin-very-light" => "#FFF6EA, #A2806B",
                                        "skin-light" => "#FEF4E4, #BC957C",
                                        "skin-light-to-medium" => "#FEF4E4, #BC957C",
                                        "skin-medium" => "#FDEFCF, #AF8460",
                                        "skin-medium-to-deep" => "#F4DCB9, #967254",
                                        "skin-deep" => "#DDB385, #7C5A3C",
                                        "skin-deep-rich" => "#B9834A, #5D3E26",
                                        "skin-very-deep" => "#7B4433, #442B24",
                                        "skin-dark-rich" => "#563B33, #2C251E",
                                        "skin-darkest" => "#46332E, #2A251F"
                                    ];

                                    foreach ($skinTones as $id => $label) {
                                        $checked = ($selectedSkinTone === $id) ? 'checked' : '';
                                        $gradient = $gradientColors[$id] ?? "#FFF, #000";
                                        ?>
                                        <div class="col">
                                            <input id="<?= $id ?>" type="radio" class="btn-check" name="skin_tones" value="<?= $id ?>" <?= $checked ?>>
                                            <label for="<?= $id ?>" class="form-label d-flex gap-2">
                                                <span class="btn-swatch" style="background: linear-gradient(180deg, <?= $gradient ?>);">
                                                    <i class="btn-swatch-label zi-check"></i>
                                                </span>
                                                                            <span class="align-self-center lh-1 ms-1">
                                                    <?= __($label) ?>
                                                </span>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>


                            <?php
                            $selectedResidence = $_SESSION["formdata"][2]["residence"] ?? ''; // Get selected residence
                            $selectedBirth = $_SESSION["formdata"][2]["birth"] ?? ''; // Get selected birth country
                            ?>

                            <!-- Country of Residence -->
                            <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <h2 class="h3 mb-2 fw-semibold">
                                    <?= __("Country of Residence") ?>
                                </h2>
                                <p class="text-body-secondary">
                                    <?= __("Enter the country where you currently live. This helps provide geographical context and reflects the environment and culture influencing your body.") ?>
                                </p>
                                <select id="general_residence" class="form-select form-select-lg rounded-pill" data-select='{
                                    "classNames": {
                                        "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                                    },
                                    "searchEnabled": true
                                }'>
                                    <option value=""><?= __("Select country...") ?></option>

                                    <!-- Favorites Group -->
                                    <optgroup label="Favorites">
                                        <?php foreach ($country_favorites as $langCode): ?>
                                            <?php
                                            $code = strtoupper($langCode);
                                            if (isset($country_list[$code])):
                                                $selected = ($selectedResidence === $code) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $code ?>" <?= $selected ?>>
                                                    <?= $country_list[$code] ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>

                                    <!-- All Countries Group -->
                                    <optgroup label="Country">
                                        <?php foreach ($country_list as $code => $name): ?>
                                            <?php
                                            if (!is_null($code) && !in_array(strtolower($code), $country_favorites)):
                                                $selected = ($selectedResidence === $code) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $code ?>" <?= $selected ?>>
                                                    <?= $name ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Country of Birth -->
                            <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <h2 class="h3 mb-2 fw-semibold">
                                    <?= __("Country of Birth") ?>
                                </h2>
                                <p class="text-body-secondary">
                                    <?= __("Provide your country of birth to share more about your heritage or ethnic background. This helps others see themselves in cultural or regional contexts.") ?>
                                </p>
                                <select id="general_birth" class="form-select form-select-lg rounded-pill" data-select='{
                                    "classNames": {
                                        "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                                    },
                                    "searchEnabled": true
                                }'>
                                    <option value=""><?= __("Select country...") ?></option>

                                    <!-- Favorites Group -->
                                    <optgroup label="Favorites">
                                        <?php foreach ($country_favorites as $langCode): ?>
                                            <?php
                                            $code = strtoupper($langCode);
                                            if (isset($country_list[$code])):
                                                $selected = ($selectedBirth === $code) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $code ?>" <?= $selected ?>>
                                                    <?= $country_list[$code] ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>

                                    <!-- All Countries Group -->
                                    <optgroup label="Country">
                                        <?php foreach ($country_list as $code => $name): ?>
                                            <?php
                                            if (!is_null($code) && !in_array(strtolower($code), $country_favorites)):
                                                $selected = ($selectedBirth === $code) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $code ?>" <?= $selected ?>>
                                                    <?= $name ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>

                            <?php
                            $selectedAnatomy = isset($_SESSION["formdata"][2]["anatomy"]) ? explode(',', $_SESSION["formdata"][2]["anatomy"]) : [];
                            ?>

                            <!-- Current Anatomy -->
                            <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <h2 class="h3 mb-2 fw-semibold">
                                    <?= __("Current Anatomy") ?>
                                </h2>
                                <p class="text-body-secondary">
                                    <?= __("Please indicate your current anatomy. This ensures accurate representation, especially for individuals who have undergone gender-affirming surgery or are in transition.")?>
                                </p>

                                <!-- Female -->
                                <div id="section-female" class="mb-md-4 mb-3">
                                    <h3 class="h6 fs-lg mb-2 pb-1"><?= __("Female") ?></h3>
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="anatomy-female-vulva" type="checkbox" class="form-check-input"
                                               name="anatomy[]" value="anatomy-female-vulva" <?= in_array('anatomy-female-vulva', $selectedAnatomy) ? 'checked' : '' ?>>
                                        <label for="anatomy-female-vulva" class="form-check-label">
                                            <?= __("Vulva") ?>
                                        </label>
                                    </div>
                                </div>

                                <!-- Male -->
                                <div id="section-male" class="mb-md-4 mb-3">
                                    <h3 class="h6 fs-lg mb-2 pb-1"><?= __("Male") ?></h3>
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="anatomy-male-penis" type="checkbox" class="form-check-input"
                                               name="anatomy[]" value="anatomy-male-penis" <?= in_array('anatomy-male-penis', $selectedAnatomy) ? 'checked' : '' ?>>
                                        <label for="anatomy-male-penis" class="form-check-label">
                                            <?= __("Penis") ?>
                                        </label>
                                    </div>
                                </div>

                                <!-- Male to Female (MtF) -->
                                <div id="section-mtf" class="mb-md-4 mb-3">
                                    <h3 class="h6 fs-lg mb-2 pb-1"><?= __("Male to Female (MtF)") ?></h3>
                                    <?php
                                    $mtfOptions = [
                                        "anatomy-mtf-1" => [
                                            "label" => __("Post-surgery Transgender Vulva"),
                                            "description" => __("Surgically constructed vulva after gender-affirming surgery.")
                                        ],
                                        "anatomy-mtf-2" => [
                                            "label" => __("Hormone Therapy / No Surgery"),
                                            "description" => __("Individuals undergoing hormone therapy but have not had surgery.")
                                        ],
                                        "anatomy-mtf-3" => [
                                            "label" => __("Orchiectomy (Testicle Removal)"),
                                            "description" => __("Surgical removal of testicles without full vaginoplasty.")
                                        ],
                                        "anatomy-mtf-4" => [
                                            "label" => __("Chest with Breasts"),
                                            "description" => __("Developed breasts from hormone therapy or augmentation.")
                                        ],
                                    ];
                                    foreach ($mtfOptions as $id => $option): ?>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="<?= $id ?>" type="checkbox" class="form-check-input"
                                                   name="anatomy[]" value="<?= $id ?>" <?= in_array($id, $selectedAnatomy) ? 'checked' : '' ?>>
                                            <label for="<?= $id ?>" class="form-check-label">
                                                <?= $option["label"] ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= $option["description"] ?>
                    </span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Female to Male (FtM) -->
                                <div id="section-ftm" class="mb-md-4 mb-3">
                                    <h3 class="h6 fs-lg mb-2 pb-1"><?= __("Female to Male (FtM)") ?></h3>
                                    <?php
                                    $ftmOptions = [
                                        "anatomy-ftm-1" => [
                                            "label" => __("Post-surgery Transgender Penis"),
                                            "description" => __("Surgically constructed penis after gender-affirming surgery.")
                                        ],
                                        "anatomy-ftm-2" => [
                                            "label" => __("Hormone Therapy / No Surgery"),
                                            "description" => __("Individuals undergoing hormone therapy but have not had surgery.")
                                        ],
                                        "anatomy-ftm-3" => [
                                            "label" => __("Flat Chest (Post-Surgery)"),
                                            "description" => __("Surgically flattened chest after top surgery.")
                                        ],
                                    ];
                                    foreach ($ftmOptions as $id => $option): ?>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="<?= $id ?>" type="checkbox" class="form-check-input"
                                                   name="anatomy[]" value="<?= $id ?>" <?= in_array($id, $selectedAnatomy) ? 'checked' : '' ?>>
                                            <label for="<?= $id ?>" class="form-check-label">
                                                <?= $option["label"] ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= $option["description"] ?>
                    </span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>



                            <!-- Submit -->
                            <div class="d-flex flex-sm-row flex-column pt-2">
                                <button type="submit" class="btn btn-lg btn-primary rounded-pill py-3">
                                    <?= __("Continue") ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M5.628 3.182c-.244.101-.452.272-.599.491s-.225.477-.225.741v7.448c0 .264.078.521.225.741s.355.39.598.491.512.127.77.076.496-.178.683-.365l3.724-3.724c.25-.25.39-.589.39-.943s-.14-.693-.39-.943L7.081 3.472c-.186-.186-.424-.313-.682-.365s-.527-.025-.77.076z" fill="currentColor"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<?php } elseif ($curStep == 3) { ?>

            <div class="col-lg-9 col-sm-8 py-5 px-lg-5 px-sm-4 px-3">
                <div class="col-xxl-8 col-lg-11 py-lg-5 py-sm-4 py-5 px-xxl-5 px-lg-4">

                    <!-- Back button (mobile) -->
                    <a href="wizard-02.html" class="btn btn-lg btn-link px-0 mb-3 d-sm-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" fill="none"><path d="M10.372 3.182c.244.101.452.272.598.491s.225.477.225.741v7.448c0 .264-.078.521-.225.741s-.355.39-.598.491-.512.127-.77.076-.496-.178-.683-.365L5.195 9.081c-.25-.25-.39-.589-.39-.943s.14-.693.39-.943l3.724-3.724c.186-.186.424-.313.682-.365s.527-.025.77.076z" fill="currentColor"/></svg>
                        <?= __("Back") ?>
                    </a>

                    <div class="ff-extra">
                        <h1 class="fw-semibold">
                            <?= __("Surgical or Medical History") ?>
                        </h1>
                        <p class="fs-lg">
                            <?= __("Surgeries and medical procedures can significantly impact the body's appearance and function. This section allows you to share whether you have undergone any surgical or medical procedures related to your vulva, penis, breasts, or buttocks. Providing this information helps represent the natural diversity of bodies, including those that have experienced medical interventions.") ?>
                        </p>
                        <div class="mb-3">
                            <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                <input id="medical-none-to-all" type="checkbox" class="form-check-input">
                                <label for="medical-none-to-all" class="form-check-label fs-base">
                                    <?= __("None to all") ?>
                                </label>
                            </div>
                        </div>

                        <!-- Form -->
                        <form class="mt-lg-5 mt-4 pt-lg-0 pt-md-2" data-step="3">
                            <input type="hidden" id="category" value="<?= isset($_SESSION["formdata"][1]) ? implode(",", array_keys(array_filter($_SESSION["formdata"][1], fn($value) => $value == 1))) : "" ?>">
                            <input type="hidden" id="anatomy" value="<?= $_SESSION["formdata"][2]["anatomy"] ?? null ?>">

                            <!-- Vulva -->
                            <div id="section-vulva" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <div class="mb-2 pb-md-2">
                                    <div class="h2 mb-2 fw-semibold">
                                        <?= __("Vulva") ?>
                                    </div>
                                    <p class="text-body-secondary">
                                        <?= __("Missing text...") ?>
                                    </p>
                                </div>

                                <!-- Vulva -->
                                <div id="section-vulva-vulva" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Vulva") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("If you have had any procedures related to your vulva—whether cosmetic, medical, or childbirth-related—please indicate them below.") ?>
                                    </p>

                                    <div class="mb-3">
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-vulva-none" type="checkbox" class="form-check-input"<?= in_array('medical-vulva-none', explode(',', $_SESSION['formdata'][3]['vulva_vulva'] ?? '')) ? ' checked' : '' ?>>
                                            <label for="medical-vulva-none" class="form-check-label fs-base">
                                                <?= __("None") ?>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-vulva-labiaplasty" type="checkbox" class="form-check-input"<?= in_array('medical-vulva-labiaplasty', explode(',', $_SESSION['formdata'][3]['vulva_vulva'] ?? '')) ? ' checked' : '' ?>>
                                            <label for="medical-vulva-labiaplasty" class="form-check-label fs-base">
                                                <?= __("Labiaplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Cosmetic reshaping of the labia for aesthetic or medical reasons.") ?>
                </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-vulva-clitoral-hood-reduction" type="checkbox" class="form-check-input"<?= in_array('medical-vulva-clitoral-hood-reduction', explode(',', $_SESSION['formdata'][3]['vulva_vulva'] ?? '')) ? ' checked' : '' ?>>
                                            <label for="medical-vulva-clitoral-hood-reduction" class="form-check-label fs-base">
                                                <?= __("Clitoral hood reduction") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical reduction or reshaping of the clitoral hood.") ?>
                </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-vulva-hymenoplasty" type="checkbox" class="form-check-input"<?= in_array('medical-vulva-hymenoplasty', explode(',', $_SESSION['formdata'][3]['vulva_vulva'] ?? '')) ? ' checked' : '' ?>>
                                            <label for="medical-vulva-hymenoplasty" class="form-check-label fs-base">
                                                <?= __("Hymenoplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical reconstruction of the hymen.") ?>
                </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-vulva-episiotomy-tearing" type="checkbox" class="form-check-input"<?= in_array('medical-vulva-episiotomy-tearing', explode(',', $_SESSION['formdata'][3]['vulva_vulva'] ?? '')) ? ' checked' : '' ?>>
                                            <label for="medical-vulva-episiotomy-tearing" class="form-check-label fs-base">
                                                <?= __("Episiotomy or tearing repair") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                                                    <?= __("Surgical repair due to childbirth-related tearing.") ?>
                                                </span>
                                            </label>
                                        </div>

<?php
$vulvaText = $_SESSION['formdata'][3]['vulva_vulva_text'] ?? '';
$hasVulvaText = !empty($vulvaText);
?>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#medical-vulva-specify">
                                            <input id="medical-vulva-other-surgical-procedures" type="checkbox" class="form-check-input"
                                                <?= in_array('medical-vulva-other-surgical-procedures', explode(',', $_SESSION['formdata'][3]['vulva_vulva'] ?? '')) || $hasVulvaText ? ' checked' : '' ?>>
                                            <label for="medical-vulva-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="medical-vulva-specify" class="collapse<?= $hasVulvaText ? ' show' : '' ?>">
                                            <div class="border-bottom border-dark">
                                                <input
                                                        id="medical-vulva-other-surgical-procedures-text"
                                                        type="text"
                                                        class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                        placeholder="<?= __("Please specify") ?>"
                                                        value="<?= htmlspecialchars($vulvaText) ?>"
                                                        data-autofocus="collapse"
                                                >
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Breast -->
                                <?php
                                $vulvaBreastText = $_SESSION['formdata'][3]['vulva_breast_text'] ?? '';
                                $hasVulvaBreastText = !empty($vulvaBreastText);
                                $vulvaBreastArray = explode(',', $_SESSION['formdata'][3]['vulva_breast'] ?? '');
                                ?>

                                <div id="section-vulva-breast" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Breast") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Breast tissue may change due to surgery, hormone therapy, or medical conditions. If you have undergone any procedures, please indicate them below.") ?>
                                    </p>

                                    <div class="mb-3">
                                        <?php
                                        $breastOptions = [
                                            'medical-breast-none' => __("None"),
                                            'medical-breast-augmentation' => __("Breast augmentation"),
                                            'medical-breast-reduction' => __("Breast reduction"),
                                            'medical-breast-full-mastectomy' => __("Full mastectomy"),
                                            'medical-breast-reconstruction' => __("Breast reconstruction"),
                                            'medical-breast-nipple-modification' => __("Nipple reconstruction or modification"),
                                        ];

                                        $breastDescriptions = [
                                            'medical-breast-augmentation' => __("Surgical enhancement of breast size, typically using implants or fat transfer."),
                                            'medical-breast-reduction' => __("Surgical removal of excess breast tissue for medical, aesthetic, or gender-affirming reasons."),
                                            'medical-breast-full-mastectomy' => __("Complete removal of breast tissue, commonly for medical or gender-affirming purposes."),
                                            'medical-breast-reconstruction' => __("Surgical reconstruction post-mastectomy."),
                                            'medical-breast-nipple-modification' => __("Surgical reshaping or reconstruction of the nipple, often after mastectomy, top surgery, or for aesthetic purposes."),
                                        ];

                                        foreach ($breastOptions as $id => $label) :
                                            $checked = in_array($id, $vulvaBreastArray) ? 'checked' : '';
                                            ?>
                                            <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                                <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= $checked ?>>
                                                <label for="<?= $id ?>" class="form-check-label fs-base">
                                                    <?= $label ?>
                                                    <?php if (isset($breastDescriptions[$id])) : ?>
                                                        <span class="mt-1 d-block text-body-secondary">
                        <?= $breastDescriptions[$id] ?>
                    </span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#medical-breast-specify">
                                            <input id="medical-breast-other-surgical-procedures" type="checkbox" class="form-check-input"
                                                <?= in_array('medical-breast-other-surgical-procedures', $vulvaBreastArray) || $hasVulvaBreastText ? 'checked' : '' ?>>
                                            <label for="medical-breast-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="medical-breast-specify" class="collapse<?= $hasVulvaBreastText ? ' show' : '' ?>">
                                            <div class="border-bottom border-dark">
                                                <input id="medical-breast-other-surgical-procedures-text"
                                                       type="text"
                                                       class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                       placeholder="<?= __("Please specify") ?>"
                                                       value="<?= htmlspecialchars($vulvaBreastText) ?>"
                                                       data-autofocus="collapse">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Penis -->
                            <div id="section-penis" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <div class="mb-2 pb-md-2">
                                    <div class="h2 mb-2 fw-semibold">
                                        <?= __("Penis") ?>
                                    </div>
                                    <p class="text-body-secondary">
                                        <?= __("Missing text...") ?>
                                    </p>
                                </div>

                                <!-- Penis -->
                                <?php
                                $penisText = $_SESSION['formdata'][3]['penis_penis_text'] ?? '';
                                $hasPenisText = !empty($penisText);
                                $penisArray = explode(',', $_SESSION['formdata'][3]['penis_penis'] ?? '');
                                ?>

                                <div id="section-penis-penis" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Penis") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Various medical, cultural, or gender-affirming procedures can affect the penis. If you have undergone any of these, please indicate them below.") ?>
                                    </p>

                                    <div class="mb-3">
                                        <?php
                                        $penisOptions = [
                                            'medical-penis-none' => __("None"),
                                            'medical-penis-circumcision' => __("Circumcision"),
                                            'medical-penis-implant' => __("Penile implant"),
                                            'medical-penis-surgery' => __("Penile lengthening or enlargement surgery"),
                                            'medical-penis-hypospadias' => __("Hypospadias repair"),
                                        ];

                                        $penisDescriptions = [
                                            'medical-penis-implant' => __("Prosthetic implant for erectile dysfunction or gender-affirming purposes."),
                                            'medical-penis-surgery' => __("Cosmetic or reconstructive procedure to modify penis size."),
                                            'medical-penis-hypospadias' => __("Surgical correction of a congenital condition affecting the urethral opening."),
                                        ];

                                        foreach ($penisOptions as $id => $label) :
                                            $checked = in_array($id, $penisArray) ? 'checked' : '';
                                            ?>
                                            <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                                <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= $checked ?>>
                                                <label for="<?= $id ?>" class="form-check-label fs-base">
                                                    <?= $label ?>
                                                    <?php if (isset($penisDescriptions[$id])) : ?>
                                                        <span class="mt-1 d-block text-body-secondary">
                        <?= $penisDescriptions[$id] ?>
                    </span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#medical-penis-specify">
                                            <input id="medical-penis-other-surgical-procedures" type="checkbox" class="form-check-input"
                                                <?= in_array('medical-penis-other-surgical-procedures', $penisArray) || $hasPenisText ? 'checked' : '' ?>>
                                            <label for="medical-penis-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="medical-penis-specify" class="collapse<?= $hasPenisText ? ' show' : '' ?>">
                                            <div class="border-bottom border-dark">
                                                <input id="medical-penis-other-surgical-procedures-text"
                                                       type="text"
                                                       class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                       placeholder="<?= __("Please specify") ?>"
                                                       value="<?= htmlspecialchars($penisText) ?>"
                                                       data-autofocus="collapse">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Breast -->
                                <?php
                                $penisBreastText = $_SESSION['formdata'][3]['penis_breast_text'] ?? '';
                                $hasPenisBreastText = !empty($penisBreastText);
                                $penisBreastArray = explode(',', $_SESSION['formdata'][3]['penis_breast'] ?? '');
                                ?>

                                <div id="section-penis-breast" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Breast") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Some men and non-binary individuals undergo chest procedures, such as gynecomastia surgery or breast reduction, for medical, hormonal, or aesthetic reasons. If you have had any of these procedures, please indicate them below.") ?>
                                    </p>

                                    <div class="mb-md-4 mb-3">
                                        <?php
                                        $penisBreastOptions = [
                                            'medical-penis-breast-none' => __("None"),
                                            'medical-penis-breast-gynecomastia' => __("Gynecomastia surgery"),
                                            'medical-penis-breast-nipple' => __("Nipple reconstruction or modification"),
                                        ];

                                        $penisBreastDescriptions = [
                                            'medical-penis-breast-gynecomastia' => __("Surgical reduction of excess breast tissue in men or non-binary individuals due to hormonal or medical factors."),
                                            'medical-penis-breast-nipple' => __("Surgical reshaping or reconstruction of the nipple, often after mastectomy, top surgery, or for aesthetic purposes."),
                                        ];

                                        foreach ($penisBreastOptions as $id => $label) :
                                            $checked = in_array($id, $penisBreastArray) ? 'checked' : '';
                                            ?>
                                            <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                                <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= $checked ?>>
                                                <label for="<?= $id ?>" class="form-check-label fs-base">
                                                    <?= $label ?>
                                                    <?php if (isset($penisBreastDescriptions[$id])) : ?>
                                                        <span class="mt-1 d-block text-body-secondary">
                        <?= $penisBreastDescriptions[$id] ?>
                    </span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#medical-penis-breast-specify">
                                            <input id="medical-penis-breast-other-surgical-procedures" type="checkbox" class="form-check-input"
                                                <?= in_array('medical-penis-breast-other-surgical-procedures', $penisBreastArray) || $hasPenisBreastText ? 'checked' : '' ?>>
                                            <label for="medical-penis-breast-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="medical-penis-breast-specify" class="collapse<?= $hasPenisBreastText ? ' show' : '' ?>">
                                            <div class="border-bottom border-dark">
                                                <input id="medical-penis-breast-other-surgical-procedures-text"
                                                       type="text"
                                                       class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                       placeholder="<?= __("Please specify") ?>"
                                                       value="<?= htmlspecialchars($penisBreastText) ?>"
                                                       data-autofocus="collapse">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Transgender -->
                            <div id="section-trans" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <div class="mb-2 pb-md-2">
                                    <div class="h2 mb-2 fw-semibold">
                                        <?= __("Transgender") ?>
                                    </div>
                                    <p class="text-body-secondary">
                                        <?= __("Some individuals undergo gender-affirming surgeries as part of their transition. If you have had any of the following procedures, please indicate them below. If you have not undergone surgery, select 'None'.") ?>
                                    </p>
                                </div>

                                <?php
                                $mtfText = $_SESSION['formdata'][3]['trans_mtf_text'] ?? '';
                                $hasMtfText = !empty($mtfText);
                                $mtfArray = explode(',', $_SESSION['formdata'][3]['trans_mtf'] ?? '');
                                ?>

                                <div id="section-trans-mtf" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Male to Female (MtF)") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Indicate if you have had any gender-affirming surgeries related to your transition from male to female.") ?>
                                    </p>

                                    <!-- None -->
                                    <div class="mb-3">
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-mtf-none" type="checkbox" class="form-check-input"
                                                <?= in_array('trans-mtf-none', $mtfArray) ? 'checked' : '' ?>>
                                            <label for="trans-mtf-none" class="form-check-label fs-base">
                                                <?= __("None") ?>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Top Surgery -->
                                    <div class="mb-3">
                                        <h3 class="mb-2 h6 fw-semibold">
                                            <?= __("Top Surgery (Chest-Related Procedures)") ?>
                                        </h3>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-mtf-top-breast-augmentation" type="checkbox" class="form-check-input"
                                                <?= in_array('trans-mtf-top-breast-augmentation', $mtfArray) ? 'checked' : '' ?>>
                                            <label for="trans-mtf-top-breast-augmentation" class="form-check-label fs-base">
                                                <?= __("Breast augmentation") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical enhancement of breast size, typically using implants or fat transfer.") ?>
                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Bottom Surgery -->
                                    <div class="mb-3">
                                        <h3 class="mb-2 h6 fw-semibold">
                                            <?= __("Bottom Surgery (Genital-Related Procedures)") ?>
                                        </h3>

                                        <?php
                                        $bottomOptions = [
                                            'trans-mtf-bottom-vaginoplasty' => __("Vaginoplasty"),
                                            'trans-mtf-bottom-vulvoplasty' => __("Vulvoplasty"),
                                            'trans-mtf-bottom-orchiectomy' => __("Orchiectomy (Testicle Removal)"),
                                        ];

                                        $bottomDescriptions = [
                                            'trans-mtf-bottom-vaginoplasty' => __("Surgical construction of a vagina as part of gender-affirming surgery."),
                                            'trans-mtf-bottom-vulvoplasty' => __("Surgical construction of external vulva structures without a vaginal canal."),
                                            'trans-mtf-bottom-orchiectomy' => __("Removal of testicles, often performed before or instead of full vaginoplasty."),
                                        ];

                                        foreach ($bottomOptions as $id => $label) :
                                            $checked = in_array($id, $mtfArray) ? 'checked' : '';
                                            ?>
                                            <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                                <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= $checked ?>>
                                                <label for="<?= $id ?>" class="form-check-label fs-base">
                                                    <?= $label ?>
                                                    <span class="mt-1 d-block text-body-secondary"><?= $bottomDescriptions[$id] ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#mtf-specify">
                                            <input id="mtf-other-surgical-procedures" type="checkbox" class="form-check-input"
                                                <?= in_array('mtf-other-surgical-procedures', $mtfArray) || $hasMtfText ? 'checked' : '' ?>>
                                            <label for="mtf-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="mtf-specify" class="collapse<?= $hasMtfText ? ' show' : '' ?>">
                                            <div class="border-bottom border-dark">
                                                <input id="mtf-other-surgical-procedures-text"
                                                       type="text"
                                                       class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                       placeholder="<?= __("Please specify") ?>"
                                                       value="<?= htmlspecialchars($mtfText) ?>"
                                                       data-autofocus="collapse">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Female to Male (FtM) -->
                                <div id="section-trans-ftm" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Female to Male (FtM)") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Indicate if you have had any gender-affirming surgeries related to your transition from female to male.") ?>
                                    </p>

                                    <!-- None -->
                                    <div class="mb-3">
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-none" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-none', explode(',', $_SESSION['formdata'][3]['trans_ftm'] ?? '')) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-none" class="form-check-label fs-base">
                                                <?= __("None") ?>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Top Surgery -->
                                    <div class="mb-3">
                                        <h3 class="mb-2 h6 fw-semibold">
                                            <?= __("Top Surgery (Chest-Related Procedures)") ?>
                                        </h3>
                                        <?php
                                        $transFtM = explode(',', $_SESSION['formdata'][3]['trans_ftm'] ?? '');
                                        ?>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-flatter-chest" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-top-flatter-chest', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-top-flatter-chest" class="form-check-label fs-base">
                                                <?= __("Gender-Affirming Chest Surgery") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgery to create a flatter chest, often chosen by trans men and non-binary individuals.") ?>
                </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-breast-reduction" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-top-breast-reduction', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-top-breast-reduction" class="form-check-label fs-base">
                                                <?= __("Breast reduction") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical removal of excess breast tissue for medical, aesthetic, or gender-affirming reasons.") ?>
                </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-partial-mastectomy" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-top-partial-mastectomy', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-top-partial-mastectomy" class="form-check-label fs-base">
                                                <?= __("Partial mastectomy") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical removal of part of the breast tissue, often due to medical reasons.") ?>
                </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-full-mastectomy" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-top-full-mastectomy', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-top-full-mastectomy" class="form-check-label fs-base">
                                                <?= __("Full mastectomy") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Complete removal of breast tissue, commonly for medical or gender-affirming purposes.") ?>
                </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-breast-reconstruction" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-top-breast-reconstruction', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-top-breast-reconstruction" class="form-check-label fs-base">
                                                <?= __("Breast reconstruction") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical reconstruction post-mastectomy.") ?>
                </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-nipple-reconstruction" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-top-nipple-reconstruction', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-top-nipple-reconstruction" class="form-check-label fs-base">
                                                <?= __("Nipple reconstruction or modification") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Surgical reshaping or reconstruction of the nipple, often after mastectomy, top surgery, or for aesthetic purposes.") ?>
                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Bottom Surgery -->
                                    <div class="mb-3">
                                        <h3 class="mb-2 h6 fw-semibold">
                                            <?= __("Bottom Surgery (Genital-Related Procedures)") ?>
                                        </h3>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-bottom-phalloplasty" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-bottom-phalloplasty', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-bottom-phalloplasty" class="form-check-label fs-base">
                                                <?= __("Phalloplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Gender-affirming surgery to construct a penis.") ?>
                </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-bottom-metoidioplasty" type="checkbox" class="form-check-input"<?= in_array('trans-ftm-bottom-metoidioplasty', $transFtM) ? ' checked' : '' ?>>
                                            <label for="trans-ftm-bottom-metoidioplasty" class="form-check-label fs-base">
                                                <?= __("Metoidioplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                    <?= __("Procedure that enhances genital growth caused by testosterone therapy into a small penis.") ?>
                </span>
                                            </label>
                                        </div>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#ftm-specify">
                                            <input id="trans-ftm-bottom-other-surgical-procedures" type="checkbox" class="form-check-input"<?= !empty($_SESSION['formdata'][3]['trans_ftm_text'] ?? '') ? ' checked' : '' ?>>
                                            <label for="trans-ftm-bottom-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="ftm-specify" class="collapse<?= !empty($_SESSION['formdata'][3]['trans_ftm_text'] ?? '') ? ' show' : '' ?>">
                                            <div class="border-bottom border-dark">
                                                <input id="trans-ftm-bottom-other-surgical-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse" value="<?= htmlspecialchars($_SESSION['formdata'][3]['trans_ftm_text'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttocks -->
                            <div id="section-buttocks" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <div class="mb-2 pb-md-2">
                                    <div class="h2 mb-2 fw-semibold">
                                        <?= __("Buttocks") ?>
                                    </div>
                                    <p class="text-body-secondary">
                                        <?= __("The buttocks can undergo various medical, cosmetic, or reconstructive procedures. If you have had any, please indicate below.") ?>
                                    </p>
                                </div>

                                <!-- Buttocks -->
                                <?php
                                $buttocksText = $_SESSION['formdata'][3]['buttocks_text'] ?? '';
                                $hasButtocksText = !empty($buttocksText);
                                $buttocksArray = explode(',', $_SESSION['formdata'][3]['buttocks'] ?? '');
                                ?>

                                <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <?php
                                    $buttocksOptions = [
                                        'buttocks-none' => __("None"),
                                        'buttocks-augmentation' => __("Buttock augmentation"),
                                        'buttocks-reconstruction' => __("Buttock reconstruction"),
                                        'buttocks-liposuction' => __("Liposuction on buttocks"),
                                    ];

                                    $buttocksDescriptions = [
                                        'buttocks-augmentation' => __("Implants or fat transfer to enhance size and shape."),
                                        'buttocks-reconstruction' => __("Reconstructive surgery after trauma, weight loss, or medical conditions."),
                                        'buttocks-liposuction' => __("Removal of fat deposits for contouring or reshaping."),
                                    ];

                                    foreach ($buttocksOptions as $id => $label) :
                                        $checked = in_array($id, $buttocksArray) ? 'checked' : '';
                                        ?>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= $checked ?>>
                                            <label for="<?= $id ?>" class="form-check-label fs-base">
                                                <?= $label ?>
                                                <?php if (isset($buttocksDescriptions[$id])) : ?>
                                                    <span class="mt-1 d-block text-body-secondary">
                        <?= $buttocksDescriptions[$id] ?>
                    </span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Specify collapse toggle -->
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#buttocks-specify">
                                        <input id="buttocks-other-surgical-procedures" type="checkbox" class="form-check-input"
                                            <?= in_array('buttocks-other-surgical-procedures', $buttocksArray) || $hasButtocksText ? 'checked' : '' ?>>
                                        <label for="buttocks-other-surgical-procedures" class="form-check-label fs-base">
                                            <?= __("Other surgical procedures") ?>
                                        </label>
                                    </div>

                                    <!-- Specify collapse -->
                                    <div id="buttocks-specify" class="collapse<?= $hasButtocksText ? ' show' : '' ?>">
                                        <div class="border-bottom border-dark">
                                            <input id="buttocks-other-surgical-procedures-text"
                                                   type="text"
                                                   class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                   placeholder="<?= __("Please specify") ?>"
                                                   value="<?= htmlspecialchars($buttocksText) ?>"
                                                   data-autofocus="collapse">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Hormone Therapy -->
                            <div id="section-hormone" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                <div class="mb-2 pb-md-2">
                                    <div class="h2 mb-2 fw-semibold">
                                        <?= __("Hormone Therapy") ?>
                                    </div>
                                    <p class="text-body-secondary">
                                        <?= __("Hormone therapy affects body fat distribution, muscle mass, breast or chest development, and genital characteristics. It may be used for gender-affirming care, to regulate hormones in intersex individuals, or for other medical reasons.") ?>
                                    </p>
                                </div>

                                <!-- Hormone Therapy -->
                                <?php
                                $hormoneText = $_SESSION['formdata'][3]['hormone_text'] ?? '';
                                $hasHormoneText = !empty($hormoneText);
                                $hormoneArray = explode(',', $_SESSION['formdata'][3]['hormone'] ?? '');
                                ?>

                                <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <?php
                                    $hormoneOptions = [
                                        'hormone-none' => __("No hormone therapy"),
                                        'hormone-estrogen' => __("Estrogen Therapy"),
                                        'hormone-testosterone' => __("Testosterone Therapy"),
                                        'hormone-puberty-blockers' => __("Puberty Blockers"),
                                        'hormone-hormone-blockers' => __("Hormone Blockers"),
                                    ];

                                    $hormoneDescriptions = [
                                        'hormone-estrogen' => __("Used by transgender women (MtF) and some intersex individuals to develop breasts, redistribute fat, and reduce body hair."),
                                        'hormone-testosterone' => __("Used by transgender men (FtM) and some intersex individuals to develop muscle mass, facial hair, and alter fat distribution."),
                                        'hormone-puberty-blockers' => __("Used to delay puberty onset in transgender or intersex individuals."),
                                        'hormone-hormone-blockers' => __("Used to delay puberty onset in transgender or intersex individuals."),
                                    ];

                                    foreach ($hormoneOptions as $id => $label) :
                                        $checked = in_array($id, $hormoneArray) ? 'checked' : '';
                                        ?>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= $checked ?>>
                                            <label for="<?= $id ?>" class="form-check-label fs-base">
                                                <?= $label ?>
                                                <?php if (isset($hormoneDescriptions[$id])) : ?>
                                                    <span class="mt-1 d-block text-body-secondary">
                    <?= $hormoneDescriptions[$id] ?>
                </span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Specify collapse toggle -->
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#hormone-specify">
                                        <input id="hormone-other-procedures" type="checkbox" class="form-check-input"
                                            <?= in_array('hormone-other-procedures', $hormoneArray) || $hasHormoneText ? 'checked' : '' ?>>
                                        <label for="hormone-other-procedures" class="form-check-label fs-base">
                                            <?= __("Other hormone treatments") ?>
                                        </label>
                                    </div>

                                    <!-- Specify collapse -->
                                    <div id="hormone-specify" class="collapse<?= $hasHormoneText ? ' show' : '' ?>">
                                        <div class="border-bottom border-dark">
                                            <input id="hormone-other-procedures-text"
                                                   type="text"
                                                   class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                                   placeholder="<?= __("Please specify") ?>"
                                                   value="<?= htmlspecialchars($hormoneText) ?>"
                                                   data-autofocus="collapse">
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <!-- Submit -->
                            <div class="d-flex flex-sm-row flex-column pt-2">
                                <button type="submit" class="btn btn-lg btn-primary rounded-pill py-3">
                                    Continue
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M5.628 3.182c-.244.101-.452.272-.599.491s-.225.477-.225.741v7.448c0 .264.078.521.225.741s.355.39.598.491.512.127.77.076.496-.178.683-.365l3.724-3.724c.25-.25.39-.589.39-.943s-.14-.693-.39-.943L7.081 3.472c-.186-.186-.424-.313-.682-.365s-.527-.025-.77.076z" fill="currentColor"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<?php } elseif ($curStep == 4) { ?>

            <div class="col-lg-9 col-sm-8 py-5 px-lg-5 px-sm-4 px-3">
                <div class="col-xxl-8 col-lg-11 py-lg-5 py-sm-4 py-5 px-xxl-5 px-lg-4">

                    <!-- Back button (mobile) -->
                    <a href="wizard-03.html" class="btn btn-lg btn-link px-0 mb-3 d-sm-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" fill="none">
                            <path d="M10.372 3.182c.244.101.452.272.598.491s.225.477.225.741v7.448c0 .264-.078.521-.225.741s-.355.39-.598.491-.512.127-.77.076-.496-.178-.683-.365L5.195 9.081c-.25-.25-.39-.589-.39-.943s.14-.693.39-.943l3.724-3.724c.186-.186.424-.313.682-.365s.527-.025.77.076z" fill="currentColor"/>
                        </svg>
                        <?= __("Back") ?>
                    </a>

                    <div class="ff-extra">
                        <h1 class="fw-semibold">
                            <?= __("Additional Information") ?> <span class="fs-xl"><?= __(" (optional)") ?></span>
                        </h1>
                        <p class="fs-lg">
                            <?= __("This section allows you to share more about your body's characteristics and experiences, but it is entirely optional. Providing this information helps enhance the diversity and inclusivity of the gallery. You may choose to include details about gender identity, body hair, stretch marks, scars, breastfeeding, piercings, tattoos, and other personal attributes. Feel free to only disclose the information you feel comfortable sharing.") ?>
                        </p>
                    </div>


                    <!-- Form -->
                    <form class="mt-lg-5 mt-4 pt-lg-0 pt-md-2" data-step="4">
                        <input type="hidden" id="category" value="<?= isset($_SESSION["formdata"][1]) ? implode(",", array_keys(array_filter($_SESSION["formdata"][1], fn($value) => $value == 1))) : "" ?>">
                        <input type="hidden" id="anatomy" value="<?= $_SESSION["formdata"][2]["anatomy"] ?? null ?>">

                            <!-- Presence of Hair -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold">
                                <?= __("Presence of Hair") ?>
                            </h2>
                            <p class="text-body-tertiary">
                                <?= __("Indicate the presence of body hair in different areas at the time of the photo. This helps showcase natural variations in hair growth and grooming preferences.") ?>
                            </p>

                            <!-- Chest/Breasts -->
                            <div class="mb-md-4 mb-3">
                                <h3 class="mt-md-4 mt-3 h6 fs-lg"><?= __("Chest/Breasts") ?></h3>
                                <?php $chest = $_SESSION["formdata"][4]["hair_chest"] ?? ''; ?>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="chest-hair-natural" type="radio" name="chest-hair" class="form-check-input"
                                        <?= $chest === 'chest-hair-natural' ? 'checked' : '' ?>>
                                    <label for="chest-hair-natural" class="form-check-label"><?= __("Natural (Not Trimmed or Shaved)") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="chest-hair-trimmed" type="radio" name="chest-hair" class="form-check-input"
                                        <?= $chest === 'chest-hair-trimmed' ? 'checked' : '' ?>>
                                    <label for="chest-hair-trimmed" class="form-check-label"><?= __("Trimmed") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="chest-hair-hairless" type="radio" name="chest-hair" class="form-check-input"
                                        <?= $chest === 'chest-hair-hairless' ? 'checked' : '' ?>>
                                    <label for="chest-hair-hairless" class="form-check-label"><?= __("Hairless (Shaved/Waxed)") ?></label>
                                </div>
                            </div>

                            <!-- Genital Area (Above Penis or Vulva) -->
                            <div class="mb-md-4 mb-3">
                                <h3 class="mt-md-4 mt-3 h6 fs-lg"><?= __("Genital Area (Above Penis or Vulva)") ?></h3>
                                <?php $above = $_SESSION["formdata"][4]["hair_above"] ?? ''; ?>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="genital-hair-above-natural" type="radio" name="genital-hair-above" class="form-check-input"
                                        <?= $above === 'genital-hair-above-natural' ? 'checked' : '' ?>>
                                    <label for="genital-hair-above-natural" class="form-check-label"><?= __("Natural (Not Trimmed or Shaved)") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="genital-hair-above-trimmed" type="radio" name="genital-hair-above" class="form-check-input"
                                        <?= $above === 'genital-hair-above-trimmed' ? 'checked' : '' ?>>
                                    <label for="genital-hair-above-trimmed" class="form-check-label"><?= __("Trimmed") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="genital-hair-above-hairless" type="radio" name="genital-hair-above" class="form-check-input"
                                        <?= $above === 'genital-hair-above-hairless' ? 'checked' : '' ?>>
                                    <label for="genital-hair-above-hairless" class="form-check-label"><?= __("Hairless (Shaved/Waxed)") ?></label>
                                </div>
                            </div>

                            <!-- Genital Area (Scrotum/Testicles, Labia, or Perineum) -->
                            <div class="mb-md-4 mb-3">
                                <h3 class="mt-md-4 mt-3 h6 fs-lg"><?= __("Genital Area (Scrotum/Testicles, Labia, or Perineum)") ?></h3>
                                <?php $below = $_SESSION["formdata"][4]["hair_below"] ?? ''; ?>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="genital-hair-natural" type="radio" name="genital-hair" class="form-check-input"
                                        <?= $below === 'genital-hair-natural' ? 'checked' : '' ?>>
                                    <label for="genital-hair-natural" class="form-check-label"><?= __("Natural (Not Trimmed or Shaved)") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="genital-hair-trimmed" type="radio" name="genital-hair" class="form-check-input"
                                        <?= $below === 'genital-hair-trimmed' ? 'checked' : '' ?>>
                                    <label for="genital-hair-trimmed" class="form-check-label"><?= __("Trimmed") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="genital-hair-hairless" type="radio" name="genital-hair" class="form-check-input"
                                        <?= $below === 'genital-hair-hairless' ? 'checked' : '' ?>>
                                    <label for="genital-hair-hairless" class="form-check-label"><?= __("Hairless (Shaved/Waxed)") ?></label>
                                </div>
                            </div>

                            <!-- Buttocks -->
                            <div class="mb-md-4 mb-3">
                                <h3 class="mt-md-4 mt-3 h6 fs-lg"><?= __("Buttocks") ?></h3>
                                <?php $buttocks = $_SESSION["formdata"][4]["hair_buttocks"] ?? ''; ?>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="buttocks-hair-natural" type="radio" name="buttocks-hair" class="form-check-input"
                                        <?= $buttocks === 'buttocks-hair-natural' ? 'checked' : '' ?>>
                                    <label for="buttocks-hair-natural" class="form-check-label"><?= __("Natural (Not Trimmed or Shaved)") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="buttocks-hair-trimmed" type="radio" name="buttocks-hair" class="form-check-input"
                                        <?= $buttocks === 'buttocks-hair-trimmed' ? 'checked' : '' ?>>
                                    <label for="buttocks-hair-trimmed" class="form-check-label"><?= __("Trimmed") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="buttocks-hair-hairless" type="radio" name="buttocks-hair" class="form-check-input"
                                        <?= $buttocks === 'buttocks-hair-hairless' ? 'checked' : '' ?>>
                                    <label for="buttocks-hair-hairless" class="form-check-label"><?= __("Hairless (Shaved/Waxed)") ?></label>
                                </div>
                            </div>
                        </div>


                        <!-- Stretch Marks or Scars -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold">
                                <?= __("Stretch Marks or Scars") ?>
                            </h2>
                            <p class="text-body-tertiary">
                                <?= __("Let us know if your body has visible stretch marks or scars, including on areas such as the breasts, buttocks, or other body parts.") ?>
                            </p>
                            <div class="mb-md-4 mb-3">
                                <?php
                                $marks = explode(",", $_SESSION["formdata"][4]["marks"] ?? '');
                                $marksText = $_SESSION["formdata"][4]["marks_text"] ?? '';
                                ?>

                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="marks-none" type="checkbox" class="form-check-input"
                                        <?= in_array("marks-none", $marks) ? 'checked' : '' ?>>
                                    <label for="marks-none" class="form-check-label">
                                        <?= __("No stretch marks or scars") ?>
                                    </label>
                                </div>

                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="marks-stretch" type="checkbox" class="form-check-input"
                                        <?= in_array("marks-stretch", $marks) ? 'checked' : '' ?>>
                                    <label for="marks-stretch" class="form-check-label">
                                        <?= __("Stretch marks") ?>
                                    </label>
                                </div>

                                <!-- Scars with collapse -->
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2"
                                     data-bs-toggle="collapse"
                                     data-bs-target="#marks-scars-specify"
                                     aria-expanded="<?= in_array("marks-scars", $marks) ? 'true' : 'false' ?>">
                                    <input id="marks-scars" type="checkbox" class="form-check-input"
                                        <?= in_array("marks-scars", $marks) ? 'checked' : '' ?>>
                                    <label for="marks-scars" class="form-check-label">
                                        <?= __("Scars") ?>
                                    </label>
                                </div>

                                <div id="marks-scars-specify" class="collapse <?= in_array("marks-scars", $marks) ? 'show' : '' ?>">
                                    <div class="border-bottom border-dark">
                                        <input id="marks-scars-text"
                                               type="text"
                                               class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                               placeholder="<?= __("Would you like to share the story of your scars?") ?>"
                                               value="<?= htmlspecialchars($marksText) ?>"
                                               data-autofocus="collapse">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Pregnancy -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Pregnancy") ?></h2>
                            <p class="text-body-tertiary">
                                <?= __("Pregnancy may cause changes such as breast growth, stretch marks, and body shape adjustments. Have you been pregnant?") ?>
                            </p>
                            <?php $pregnancy = $_SESSION["formdata"][4]["pregnancy"] ?? ''; ?>
                            <div class="mb-md-4 mb-3 d-flex gap-2">
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="pregnancy-true" type="radio" name="pregnancy" class="form-check-input"
                                        <?= $pregnancy === 'pregnancy-true' ? 'checked' : '' ?>>
                                    <label for="pregnancy-true" class="form-check-label"><?= __("Yes") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="pregnancy-false" type="radio" name="pregnancy" class="form-check-input"
                                        <?= $pregnancy === 'pregnancy-false' ? 'checked' : '' ?>>
                                    <label for="pregnancy-false" class="form-check-label"><?= __("No") ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- Vaginal Birth -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Vaginal Birth") ?></h2>
                            <p class="text-body-tertiary">
                                <?= __("Vaginal birth can impact the body, including perineal scarring, muscle changes, and genital variations. Indicate if you have given birth vaginally.") ?>
                            </p>
                            <?php $vaginal = $_SESSION["formdata"][4]["vaginal_birth"] ?? ''; ?>
                            <div class="mb-md-4 mb-3 d-flex gap-2">
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="vaginal-birth-true" type="radio" name="vaginal-birth" class="form-check-input"
                                        <?= $vaginal === 'vaginal-birth-true' ? 'checked' : '' ?>>
                                    <label for="vaginal-birth-true" class="form-check-label"><?= __("Yes") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="vaginal-birth-false" type="radio" name="vaginal-birth" class="form-check-input"
                                        <?= $vaginal === 'vaginal-birth-false' ? 'checked' : '' ?>>
                                    <label for="vaginal-birth-false" class="form-check-label"><?= __("No") ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- C-Section -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("C-Section") ?></h2>
                            <p class="text-body-tertiary">
                                <?= __("Cesarean birth (C-section) can result in scarring and changes in muscle tone. Indicate if you have had a C-section.") ?>
                            </p>
                            <?php $csection = $_SESSION["formdata"][4]["c_section"] ?? ''; ?>
                            <div class="mb-md-4 mb-3 d-flex gap-2">
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="c-section-true" type="radio" name="c-section" class="form-check-input"
                                        <?= $csection === 'c-section-true' ? 'checked' : '' ?>>
                                    <label for="c-section-true" class="form-check-label"><?= __("Yes") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="c-section-false" type="radio" name="c-section" class="form-check-input"
                                        <?= $csection === 'c-section-false' ? 'checked' : '' ?>>
                                    <label for="c-section-false" class="form-check-label"><?= __("No") ?></label>
                                </div>
                            </div>
                        </div>


                        <?php
                        $breastfeeding = $_SESSION["formdata"][4]["breastfeeding"] ?? '';
                        $piercings = explode(',', $_SESSION["formdata"][4]["piercings"] ?? '');
                        $piercingsText = $_SESSION["formdata"][4]["piercings_other_text"] ?? '';
                        $tattoos = explode(',', $_SESSION["formdata"][4]["tattoos"] ?? '');
                        $tattoosText = $_SESSION["formdata"][4]["tattoos_other_text"] ?? '';
                        $hormonal = $_SESSION["formdata"][4]["hormonal_influence"] ?? '';
                        $menstrual = $_SESSION["formdata"][4]["menstrual_cycle"] ?? '';
                        ?>

                        <!-- Breastfeeding -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Breastfeeding") ?></h2>
                            <p class="text-body-tertiary"><?= __("If relevant, indicate whether you are currently breastfeeding or have breastfed in the past.") ?></p>
                            <div class="mb-md-4 mb-3">
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="breastfeeding-current" type="radio" name="breastfeeding" class="form-check-input"
                                        <?= $breastfeeding === 'breastfeeding-current' ? 'checked' : '' ?>>
                                    <label for="breastfeeding-current" class="form-check-label"><?= __("Yes, I am currently breastfeeding") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="breastfeeding-past" type="radio" name="breastfeeding" class="form-check-input"
                                        <?= $breastfeeding === 'breastfeeding-past' ? 'checked' : '' ?>>
                                    <label for="breastfeeding-past" class="form-check-label"><?= __("Yes, I have breastfed in the past") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2">
                                    <input id="breastfeeding-false" type="radio" name="breastfeeding" class="form-check-input"
                                        <?= $breastfeeding === 'breastfeeding-false' ? 'checked' : '' ?>>
                                    <label for="breastfeeding-false" class="form-check-label"><?= __("No, I have never breastfed") ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- Piercings -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Piercings") ?></h2>
                            <p class="text-body-tertiary"><?= __("Indicate if you have any piercings in the following areas. If your piercing location is not listed, you may specify it under ‘Other’.") ?></p>
                            <div class="mb-md-4 mb-3">
                                <?php
                                $piercingOptions = [
                                    "piercings-false", "piercings-nipple", "piercings-genital", "piercings-buttocks", "piercings-other"
                                ];
                                foreach ($piercingOptions as $id): ?>
                                    <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2" <?= $id === "piercings-other" ? 'data-bs-toggle="collapse" data-bs-target="#piercings-other-specify"' : '' ?>>
                                        <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= in_array($id, $piercings) ? 'checked' : '' ?>>
                                        <label for="<?= $id ?>" class="form-check-label"><?= __(ucwords(str_replace("-", " ", $id))) ?></label>
                                    </div>
                                <?php endforeach; ?>

                                <div id="piercings-other-specify" class="collapse <?= in_array("piercings-other", $piercings) ? 'show' : '' ?>">
                                    <div class="border-bottom border-dark">
                                        <input id="piercings-other-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                               placeholder="<?= __("Would you like to share the story of your piercings?") ?>"
                                               value="<?= htmlspecialchars($piercingsText) ?>" data-autofocus="collapse">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tattoos -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Tattoos") ?></h2>
                            <p class="text-body-tertiary"><?= __("Indicate if you have any tattoos in the following areas. If your tattoo location is not listed, you may specify it under 'Other'.") ?></p>
                            <div class="mb-md-4 mb-3">
                                <?php
                                $tattooOptions = [
                                    "tattoos-false", "tattoos-breasts", "tattoos-vulva", "tattoos-penis", "tattoos-buttocks", "tattoos-other"
                                ];
                                foreach ($tattooOptions as $id): ?>
                                    <div class="form-check btn btn-lg btn-light rounded-pill w-100 mb-2" <?= $id === "tattoos-other" ? 'data-bs-toggle="collapse" data-bs-target="#tattoos-other-specify"' : '' ?>>
                                        <input id="<?= $id ?>" type="checkbox" class="form-check-input" <?= in_array($id, $tattoos) ? 'checked' : '' ?>>
                                        <label for="<?= $id ?>" class="form-check-label"><?= __(ucwords(str_replace("-", " ", $id))) ?></label>
                                    </div>
                                <?php endforeach; ?>

                                <div id="tattoos-other-specify" class="collapse <?= in_array("tattoos-other", $tattoos) ? 'show' : '' ?>">
                                    <div class="border-bottom border-dark">
                                        <input id="tattoos-other-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0"
                                               placeholder="<?= __("Please, specify") ?>"
                                               value="<?= htmlspecialchars($tattoosText) ?>" data-autofocus="collapse">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hormonal Influence -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Hormonal Influence") ?></h2>
                            <p class="text-body-tertiary"><?= __("Have hormonal changes (e.g., puberty, menopause, hormone therapy) influenced the photographed area? This helps document natural body developments across different life") ?></p>
                            <div class="mb-md-4 mb-3 d-flex gap-2">
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="hormonal-influence-true" type="radio" name="hormonal-influence" class="form-check-input"
                                        <?= $hormonal === 'hormonal-influence-true' ? 'checked' : '' ?>>
                                    <label for="hormonal-influence-true" class="form-check-label"><?= __("Yes") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="hormonal-influence-false" type="radio" name="hormonal-influence" class="form-check-input"
                                        <?= $hormonal === 'hormonal-influence-false' ? 'checked' : '' ?>>
                                    <label for="hormonal-influence-false" class="form-check-label"><?= __("No") ?></label>
                                </div>
                            </div>
                        </div>

                        <!-- Menstrual Cycle -->
                        <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                            <h2 class="h3 mb-2 fw-semibold"><?= __("Menstrual Cycle") ?></h2>
                            <p class="text-body-tertiary"><?= __("Were you menstruating when this photo was taken? This helps document natural variations during the cycle.") ?></p>
                            <div class="mb-md-4 mb-3 d-flex gap-2">
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="menstrual-cycle-true" type="radio" name="menstrual-cycle" class="form-check-input"
                                        <?= $menstrual === 'menstrual-cycle-true' ? 'checked' : '' ?>>
                                    <label for="menstrual-cycle-true" class="form-check-label"><?= __("Yes") ?></label>
                                </div>
                                <div class="form-check btn btn-lg btn-light rounded-pill mb-2">
                                    <input id="menstrual-cycle-false" type="radio" name="menstrual-cycle" class="form-check-input"
                                        <?= $menstrual === 'menstrual-cycle-false' ? 'checked' : '' ?>>
                                    <label for="menstrual-cycle-false" class="form-check-label"><?= __("No") ?></label>
                                </div>
                            </div>
                        </div>


                        <!-- Submit -->
                        <div class="d-flex flex-sm-row flex-column pt-2">
                            <button type="submit" class="btn btn-lg btn-primary rounded-pill py-3">
                                <?= __("Submit form") ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M5.628 3.182c-.244.101-.452.272-.599.491s-.225.477-.225.741v7.448c0 .264.078.521.225.741s.355.39.598.491.512.127.77.076.496-.178.683-.365l3.724-3.724c.25-.25.39-.589.39-.943s-.14-.693-.39-.943L7.081 3.472c-.186-.186-.424-.313-.682-.365s-.527-.025-.77.076z" fill="currentColor"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

<?php } elseif ($curStep == 5) { ?>

            <!-- Content -->
            <div class="col-lg-9 col-sm-8 py-5 px-lg-5 px-sm-4 px-3">
                <div class="col-xxl-8 col-lg-11 py-lg-5 py-sm-4 py-5 px-xxl-5 px-lg-4">

                    <!-- Back button (mobile) -->
                    <a href="wizard-04.html" class="btn btn-lg btn-link px-0 mb-3 d-sm-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" fill="none"><path d="M10.372 3.182c.244.101.452.272.598.491s.225.477.225.741v7.448c0 .264-.078.521-.225.741s-.355.39-.598.491-.512.127-.77.076-.496-.178-.683-.365L5.195 9.081c-.25-.25-.39-.589-.39-.943s.14-.693.39-.943l3.724-3.724c.186-.186.424-.313.682-.365s.527-.025.77.076z" fill="currentColor"/></svg>
                        Back
                    </a>

                    <div class="ff-extra">
                        <h1 class="fw-semibold">
                            Thank you for your contribution!
                        </h1>
                        <p class="fs-lg">We truly appreciate your participation in the Shape of Us project. Your contribution helps create a more inclusive and representative collection.</p>
                        <a href="/" class="btn btn-lg btn-light rounded-pill">
                            Visit Shape of Us Site
                            <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M5.628 3.182c-.244.101-.452.272-.599.491s-.225.477-.225.741v7.448c0 .264.078.521.225.741s.355.39.598.491.512.127.77.076.496-.178.683-.365l3.724-3.724c.25-.25.39-.589.39-.943s-.14-.693-.39-.943L7.081 3.472c-.186-.186-.424-.313-.682-.365s-.527-.025-.77.076z" fill="currentColor"/></svg>
                        </a>
                        <div class="mt-lg-5 mt-4 pt-lg-0 pt-md-2">
                            <h2 class="h4 fw-semibold">Your ID</h2>
                            <div class="bg-primary text-white text-center rounded-5 p-3 h1 fw-semibold" style="font-size: 7.5vw;">
                                <?= $photoId ?>
                            </div>
                            <p class="my-4 py-md-2 fs-lg">
                                Please save this number. It will be used when you have your pictures taken to ensure your anonymity. This ID allows us to securely connect your form with your images without collecting any personal information. Thank you for being a part of this important initiative!
                            </p>

                            <!-- Finish -->
                            <div class="d-flex flex-sm-row flex-column pt-2">
                                <a href="#" class="btn btn-lg btn-primary rounded-pill py-3">
                                    Finish
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M5.628 3.182c-.244.101-.452.272-.599.491s-.225.477-.225.741v7.448c0 .264.078.521.225.741s.355.39.598.491.512.127.77.076.496-.178.683-.365l3.724-3.724c.25-.25.39-.589.39-.943s-.14-.693-.39-.943L7.081 3.472c-.186-.186-.424-.313-.682-.365s-.527-.025-.77.076z" fill="currentColor"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php } ?>

        </div>
    </div>
</main>

<?php include "src/html/html_modals.php"; ?>

<script id="translations" type="application/json">
    {
    }
</script>
<?php include "src/html/html-scripts.php"; ?>
<?php include "src/html/html-end.php"; ?>