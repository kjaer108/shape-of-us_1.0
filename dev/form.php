<?php
$page = ["name"=>"form", "translate"=>true, "sourcelang" => "en"];
require_once "src/inc/init.php";

//log_page_load();

//unset($_SESSION["formdata"]);
zdebug($_SESSION["formdata"][3] ?? null);
zdebug($_SESSION["formdata"][2]["anatomy"] ?? null);

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
                            <a href="http://localhost:63342/shape-of-us_1.0/dev/form.php?step=1&lang=en" class="nav-link align-items-start<?= $curStep <= 1 ? " pe-none" : "" ?><?= $curStep == 1 ? " active" : "" ?>">
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
                            <a href="http://localhost:63342/shape-of-us_1.0/dev/form.php?step=2&lang=en" class="nav-link align-items-start<?= $curStep <= 2 ? " pe-none" : "" ?><?= $curStep == 2 ? " active" : "" ?>">
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
                            <a href="wizard-03.html" class="nav-link align-items-start<?= $curStep <= 3 ? " pe-none" : "" ?><?= $curStep == 3 ? " active" : "" ?>">
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
                            <a href="wizard-04.html" class="nav-link align-items-start<?= $curStep <= 4 ? " pe-none" : "" ?><?= $curStep == 4 ? " active" : "" ?>">
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
                            <a href="wizard-05.html" class="nav-link align-items-start<?= $curStep <= 5 ? " pe-none" : "" ?><?= $curStep == 5 ? " active" : "" ?>">
    <?php if ($curStep <= 5) { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">05</span>
<?php } else { ?>
                                <span class="d-block me-2 pe-1 text-center" style="min-width: 28px;">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="d-block mt-1" width="28" height="22" fill="none"><path d="M22.065 5.034c-3.126.938-7.112 3.446-10.787 7.743l-2.172-2.203c-.334-.349-.955-.349-1.289 0L6.219 12.21c-.31.327-.286.807.048 1.091l4.916 4.319c.406.349 1.074.262 1.36-.196 2.625-4.341 5.441-7.569 10.214-11.277.573-.458.048-1.33-.692-1.112z" fill="currentColor"/></svg>
                                </span>
<?php } ?>
                                <?= __("Summary") ?>
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
                                            <input id="<?= $id ?>" type="checkbox" class="btn-check" name="skin_tones" value="<?= $id ?>" <?= $checked ?>>
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
                                    "searchEnabled": true,
                                    "removeItemButton": false
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
        "searchEnabled": true,
        "removeItemButton": false
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
                                <div id="section-penis-penis" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Penis") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Various medical, cultural, or gender-affirming procedures can affect the penis. If you have undergone any of these, please indicate them below.") ?>
                                    </p>

                                    <div class="mb-3">
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-none" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-none" class="form-check-label fs-base">
                                                <?= __("None") ?>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-circumcision" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-circumcision" class="form-check-label fs-base">
                                                <?= __("Circumcision") ?>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-implant" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-implant" class="form-check-label fs-base">
                                                <?= __("Penile implant") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Prosthetic implant for erectile dysfunction or gender-affirming purposes.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-surgery" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-surgery" class="form-check-label fs-base">
                                                <?= __("Penile lengthening or enlargement surgery") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Cosmetic or reconstructive procedure to modify penis size.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-hypospadias" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-hypospadias" class="form-check-label fs-base">
                                                <?= __("Hypospadias repair") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Surgical correction of a congenital condition affecting the urethral opening.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#medical-penis-specify">
                                            <input id="medical-penis-other-surgical-procedures" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="medical-penis-specify" class="collapse">
                                            <div class="border-bottom border-dark">
                                                <input id="medical-penis-other-surgical-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Breast -->
                                <div id="section-penis-breast" class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <h2 class="h4 mb-2 fw-semibold">
                                        <?= __("Breast") ?>
                                    </h2>
                                    <p class="text-body-secondary">
                                        <?= __("Some men and non-binary individuals undergo chest procedures, such as gynecomastia surgery or breast reduction, for medical, hormonal, or aesthetic reasons. If you have had any of these procedures, please indicate them below.") ?>
                                    </p>

                                    <div class="mb-md-4 mb-3">
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-breast-none" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-breast-none" class="form-check-label fs-base">
                                                <?= __("None") ?>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-breast-gynecomastia" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-breast-gynecomastia" class="form-check-label fs-base">
                                                <?= __("Gynecomastia surgery") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Surgical reduction of excess breast tissue in men or non-binary individuals due to hormonal or medical factors.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="medical-penis-breast-nipple" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-breast-nipple" class="form-check-label fs-base">
                                                <?= __("Nipple reconstruction or modification") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Surgical reshaping or reconstruction of the nipple, often after mastectomy, top surgery, or for aesthetic purposes.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#medical-penis-breast-specify">
                                            <input id="medical-penis-breast-other-surgical-procedures" type="checkbox" class="form-check-input">
                                            <label for="medical-penis-breast-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="medical-penis-breast-specify" class="collapse">
                                            <div class="border-bottom border-dark">
                                                <input id="medical-penis-breast-other-surgical-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse">
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

                                <!-- Male to Female (MtF) -->
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
                                            <input id="trans-mtf-none" type="checkbox" class="form-check-input">
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
                                            <input id="trans-mtf-top-breast-augmentation" type="checkbox" class="form-check-input">
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
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-mtf-bottom-vaginoplasty" type="checkbox" class="form-check-input">
                                            <label for="trans-mtf-bottom-vaginoplasty" class="form-check-label fs-base">
                                                <?= __("Vaginoplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Surgical construction of a vagina as part of gender-affirming surgery.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-mtf-bottom-vulvoplasty" type="checkbox" class="form-check-input">
                                            <label for="trans-mtf-bottom-vulvoplasty" class="form-check-label fs-base">
                                                <?= __("Vulvoplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Surgical construction of external vulva structures without a vaginal canal.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-mtf-bottom-orchiectomy" type="checkbox" class="form-check-input">
                                            <label for="trans-mtf-bottom-orchiectomy" class="form-check-label fs-base">
                                                <?= __("Orchiectomy (Testicle Removal)") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                        <?= __("Removal of testicles, often performed before or instead of full vaginoplasty.") ?>
                    </span>
                                            </label>
                                        </div>

                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#mtf-specify">
                                            <input id="mtf-other-surgical-procedures" type="checkbox" class="form-check-input">
                                            <label for="mtf-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="mtf-specify" class="collapse">
                                            <div class="border-bottom border-dark">
                                                <input id="mtf-other-surgical-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse">
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
                                            <input id="trans-ftm-none" type="checkbox" class="form-check-input">
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
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-flatter-chest" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-top-flatter-chest" class="form-check-label fs-base">
                                                <?= __("Gender-Affirming Chest Surgery") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                                                    <?= __("Surgery to create a flatter chest, often chosen by trans men and non-binary individuals.") ?>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-breast-reduction" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-top-breast-reduction" class="form-check-label fs-base">
                                                Breast reduction
                                                <span class="mt-1 d-block text-body-secondary">
                              Surgical removal of excess breast tissue for medical, aesthetic, or gender-affirming reasons.
                            </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-partial-mastectomy" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-top-partial-mastectomy" class="form-check-label fs-base">
                                                Partial mastectomy
                                                <span class="mt-1 d-block text-body-secondary">
                              Surgical removal of part of the breast tissue, often due to medical reasons.
                            </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-full-mastectomy" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-top-full-mastectomy" class="form-check-label fs-base">
                                                Full mastectomy
                                                <span class="mt-1 d-block text-body-secondary">
                              Complete removal of breast tissue, commonly for medical or gender-affirming purposes.
                            </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-breast-reconstruction" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-top-breast-reconstruction" class="form-check-label fs-base">
                                                Breast reconstruction
                                                <span class="mt-1 d-block text-body-secondary">
                              Surgical reconstruction post-mastectomy.
                            </span>
                                            </label>
                                        </div>
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-top-nipple-reconstruction" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-top-nipple-reconstruction" class="form-check-label fs-base">
                                                Nipple reconstruction or modification
                                                <span class="mt-1 d-block text-body-secondary">
                              Surgical reshaping or reconstruction of the nipple, often after mastectomy, top surgery, or for aesthetic purposes.
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
                                            <input id="trans-ftm-bottom-phalloplasty" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-bottom-phalloplasty" class="form-check-label fs-base">
                                                <?= __("Phalloplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                                                    <?= __("Gender-affirming surgery to construct a penis.") ?>
                                                </span>
                                            </label>
                                        </div>

                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                            <input id="trans-ftm-bottom-metoidioplasty" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-bottom-metoidioplasty" class="form-check-label fs-base">
                                                <?= __("Metoidioplasty") ?>
                                                <span class="mt-1 d-block text-body-secondary">
                                                    <?= __("Procedure that enhances genital growth caused by testosterone therapy into a small penis.") ?>
                                                </span>
                                            </label>
                                        </div>


                                        <!-- Specify collapse toggle -->
                                        <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#ftm-specify">
                                            <input id="trans-ftm-bottom-other-surgical-procedures" type="checkbox" class="form-check-input">
                                            <label for="trans-ftm-bottom-other-surgical-procedures" class="form-check-label fs-base">
                                                <?= __("Other surgical procedures") ?>
                                            </label>
                                        </div>

                                        <!-- Specify collapse -->
                                        <div id="ftm-specify" class="collapse">
                                            <div class="border-bottom border-dark">
                                                <input id="trans-ftm-bottom-other-surgical-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse">
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
                                <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="buttocks-none" type="checkbox" class="form-check-input">
                                        <label for="buttocks-none" class="form-check-label fs-base">
                                            <?= __("None") ?>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="buttocks-augmentation" type="checkbox" class="form-check-input">
                                        <label for="buttocks-augmentation" class="form-check-label fs-base">
                                            <?= __("Buttock augmentation") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Implants or fat transfer to enhance size and shape.") ?>
                </span>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="buttocks-reconstruction" type="checkbox" class="form-check-input">
                                        <label for="buttocks-reconstruction" class="form-check-label fs-base">
                                            <?= __("Buttock reconstruction") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Reconstructive surgery after trauma, weight loss, or medical conditions.") ?>
                </span>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="buttocks-liposuction" type="checkbox" class="form-check-input">
                                        <label for="buttocks-liposuction" class="form-check-label fs-base">
                                            <?= __("Liposuction on buttocks") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Removal of fat deposits for contouring or reshaping.") ?>
                </span>
                                        </label>
                                    </div>

                                    <!-- Specify collapse toggle -->
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#buttocks-specify">
                                        <input id="buttocks-other-surgical-procedures" type="checkbox" class="form-check-input">
                                        <label for="buttocks-other-surgical-procedures" class="form-check-label fs-base">
                                            <?= __("Other surgical procedures") ?>
                                        </label>
                                    </div>

                                    <!-- Specify collapse -->
                                    <div id="buttocks-specify" class="collapse">
                                        <div class="border-bottom border-dark">
                                            <input id="buttocks-other-surgical-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse">
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
                                <div class="mb-lg-5 mb-4 pb-lg-0 pb-md-2">
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="hormone-none" type="checkbox" class="form-check-input">
                                        <label for="hormone-none" class="form-check-label fs-base">
                                            <?= __("No hormone therapy") ?>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="hormone-estrogen" type="checkbox" class="form-check-input">
                                        <label for="hormone-estrogen" class="form-check-label fs-base">
                                            <?= __("Estrogen Therapy") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Used by transgender women (MtF) and some intersex individuals to develop breasts, redistribute fat, and reduce body hair.") ?>
                </span>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="hormone-testosterone" type="checkbox" class="form-check-input">
                                        <label for="hormone-testosterone" class="form-check-label fs-base">
                                            <?= __("Testosterone Therapy") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Used by transgender men (FtM) and some intersex individuals to develop muscle mass, facial hair, and alter fat distribution.") ?>
                </span>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="hormone-puberty-blockers" type="checkbox" class="form-check-input">
                                        <label for="hormone-puberty-blockers" class="form-check-label fs-base">
                                            <?= __("Puberty Blockers") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Used to delay puberty onset in transgender or intersex individuals.") ?>
                </span>
                                        </label>
                                    </div>

                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2">
                                        <input id="hormone-hormone-blockers" type="checkbox" class="form-check-input">
                                        <label for="hormone-hormone-blockers" class="form-check-label fs-base">
                                            <?= __("Hormone Blockers") ?>
                                            <span class="mt-1 d-block text-body-secondary">
                    <?= __("Used to delay puberty onset in transgender or intersex individuals.") ?>
                </span>
                                        </label>
                                    </div>

                                    <!-- Specify collapse toggle -->
                                    <div class="form-check btn btn-lg btn-light rounded-4 w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#hormone-specify">
                                        <input id="hormone-other-procedures" type="checkbox" class="form-check-input">
                                        <label for="hormone-other-procedures" class="form-check-label fs-base">
                                            <?= __("Other hormone treatments") ?>
                                        </label>
                                    </div>

                                    <!-- Specify collapse -->
                                    <div id="hormone-specify" class="collapse">
                                        <div class="border-bottom border-dark">
                                            <input id="hormone-other-procedures-text" type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("Please specify") ?>" data-autofocus="collapse">
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