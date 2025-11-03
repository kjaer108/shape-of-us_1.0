<?php
$page = ["name"=>"app", "translate"=>true, "sourcelang" => "en"];
require_once "src/inc/init.php";

// Get filters
$selectedBodyParts = get_array_param("body-parts");
$selectedAges = get_array_param("age");

$bodyPartCount = count($selectedBodyParts);
$ageCount = count($selectedAges);

$totalSelectedFilters = $bodyPartCount + $ageCount;
//zdebug($totalSelectedFilters);


//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body>

<!-- Navigation bar (Page header) -->
<header class="px-xl-4 px-lg-3 p-2 bg-body" style="z-index: 999;">
    <div class="container-fluid">
        <div class="row align-items-center">

            <!-- Navbar brand -->
            <div class="col-xl-4 col-md-8">
                <div class="d-flex justify-content-md-start justify-content-between align-items-center gap-xxl-4 gap-md-3 gap-4">
                    <div>
                        <a href="https://shapeofus.eu/app" rel="noopener" class="d-block ms-sm-2">
                            <img src="assets/img/shape-of-us.png" width="283" alt="Shape of Us">
                        </a>
                    </div>
                    <a href="https://zandora.net/" target="_blank" rel="noopener" class="d-block ms-sm-2">
                        <img src="assets/img/zandora.png" width="125" alt="by Zandora">
                    </a>
                </div>
            </div>

            <!-- Navbar categories -->
            <div class="col-xl-4 d-xl-block d-none">
                <ul class="nav navbar-nav justify-content-center gap-1 flex-nowrap">
                    <li class="nav-item">
                        <input id="header-filter-penis" type="checkbox" name="body-parts[]" value="penis" class="btn-check"<?= (in_array("penis", $selectedBodyParts) ? " checked" : "") ?>>
                        <label for="header-filter-penis" class="nav-link rounded-pill" style="cursor: pointer;">
                            <i class="zi-close-circle-fill btn-check-label"></i>
                            <?= __("Penis") ?>
                        </label>
                    </li>
                    <li class="nav-item">
                        <input id="header-filter-vulva" type="checkbox" name="body-parts[]" value="vulva" class="btn-check"<?= (in_array("vulva", $selectedBodyParts) ? " checked" : "") ?>>
                        <label for="header-filter-vulva" class="nav-link rounded-pill" style="cursor: pointer;">
                            <i class="zi-close-circle-fill btn-check-label"></i>
                            <?= __("Vulva") ?>
                        </label>
                    </li>
                    <li class="nav-item">
                        <input id="header-filter-breast" type="checkbox" name="body-parts[]" value="breast" class="btn-check"<?= (in_array("breast", $selectedBodyParts) ? " checked" : "") ?>>
                        <label for="header-filter-breast" class="nav-link rounded-pill" style="cursor: pointer;">
                            <i class="zi-close-circle-fill btn-check-label"></i>
                            <?= __("Breast") ?>
                        </label>
                    </li>
                    <li class="nav-item">
                        <input id="header-filter-buttocks" type="checkbox" name="body-parts[]" value="buttocks" class="btn-check"<?= (in_array("buttocks", $selectedBodyParts) ? " checked" : "") ?>>
                        <label for="header-filter-buttocks" class="nav-link rounded-pill" style="cursor: pointer;">
                            <i class="zi-close-circle-fill btn-check-label"></i>
                            <?= __("Buttocks") ?>
                        </label>
                    </li>
                </ul>
            </div>

            <!-- Navbar toolbar -->
            <div class="col-md-4 d-md-block d-none">
                <div class="d-flex gap-2 justify-content-end">
                    <div class="dropdown">
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
                    <button type="button" class="btn btn-light rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-filters" style="--zs-btn-padding-y: .75rem;" data-filter-button data-filter-text="<?= __("Filters") ?>">
                        <?= __("Filters") ?><?= $totalSelectedFilters > 0 ? " ($totalSelectedFilters)" : "" ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/></svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</header>


<!-- Page content -->
<main class="content-wrapper">

    <!-- Image library wrapper -->
    <div class="pb-sm-0 pb-5 position-relative">

        <!-- Gallery content -->
        <div class="row row-cols-xxl-6 row-cols-xl-5 row-cols-md-4 row-cols-sm-3 row-cols-2 g-0">
        </div>

        <!-- Sentinel element for infinite scroll -->
        <div id="scroll-sentinel" class="py-5"></div>

        <!-- Load more btn
        <div class="mb-sm-0 py-4 px-3 d-flex flex-sm-row flex-column justify-content-sm-center">
            <button type="button" class="btn btn-lg btn-primary rounded-pill">
                Load more
            </button>
        </div> -->

        <!-- Offcanvas toggle (mobile) -->
        <div class="position-fixed bottom-0 start-0 end-0 p-sm-4 p-3 d-flex flex-sm-row flex-column justify-content-sm-end d-md-none d-block">
            <button type="button" class="btn btn-lg btn-light rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-filters" style="--zs-btn-padding-y: .75rem;" data-filter-button data-filter-text="<?= __("Filters") ?>">
                <?= __("Filters") ?><?= $totalSe > 0 ? " ($totalSelectedFilters)" : "" ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/></svg>
            </button>
        </div>
    </div>


    <!-- Offcanvas filters -->
    <form class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-filters" aria-labelledby="offcanvasFilters">

        <!-- Offcanvas header -->
        <div class="offcanvas-header justify-content-between pb-0">
            <h2 class="offcanvas-title text-primary" id="offcanvasFilters">
                <?= __("Filters") ?>
            </h2>

            <!-- Lang switcher + close btn -->
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
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
                <button type="button" class="btn btn-lg btn-link px-0" data-bs-dismiss="offcanvas">
                    <?= __("close") ?>
                </button>
            </div>
        </div>

        <!-- Offcanvas body -->
        <div class="offcanvas-body pt-3">

            <!-- Filters (nav tabs) -->
            <div class="mb-3 mx-n4">
                <div class="pb-2 overflow-auto">
                    <div class="px-4" style="min-width: 512px;">
                        <ul class="nav nav-tabs navbar-nav rounded-pill flex-nowrap text-nowrap">
                            <li class="nav-item">
                                <input id="filter-penis" type="checkbox" name="body-parts[]" value="penis" class="btn-check"<?= (in_array("penis", $selectedBodyParts) ? " checked" : "") ?>>
                                <label for="filter-penis" class="nav-link rounded-pill">
                                    <i class="zi-close-circle-fill btn-check-label"></i>
                                    <?= __("Penis") ?>
                                </label>
                            </li>
                            <li class="nav-item">
                                <input id="filter-vulva" type="checkbox" name="body-parts[]" value="vulva" class="btn-check"<?= (in_array("vulva", $selectedBodyParts) ? " checked" : "") ?>>
                                <label for="filter-vulva" class="nav-link rounded-pill">
                                    <i class="zi-close-circle-fill btn-check-label"></i>
                                    <?= __("Vulva") ?>
                                </label>
                            </li>
                            <li class="nav-item">
                                <input id="filter-breast" type="checkbox" name="body-parts[]" value="breast" class="btn-check"<?= (in_array("breast", $selectedBodyParts) ? " checked" : "") ?>>
                                <label for="filter-breast" class="nav-link rounded-pill">
                                    <i class="zi-close-circle-fill btn-check-label"></i>
                                    <?= __("Breast") ?>
                                </label>
                            </li>
                            <li class="nav-item">
                                <input id="filter-buttocks" type="checkbox" name="body-parts[]" value="buttocks" class="btn-check"<?= (in_array("buttocks", $selectedBodyParts) ? " checked" : "") ?>>
                                <label for="filter-buttocks" class="nav-link rounded-pill">
                                    <i class="zi-close-circle-fill btn-check-label"></i>
                                    <?= __("Buttocks") ?>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filters accordion -->
            <div id="offcanvas-accordion-filters" class="accordion">

                <!-- Age -->
                <div id="offcanvas-accordion-filter-age" class="accordion-item border-bottom">
                    <h3 class="accordion-header position-relative">
                        <button class="accordion-button fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#offcanvas-accordion-filter-age-collapse" aria-expanded="true" aria-controls="offcanvas-accordion-filter-age-collapse">
                  <span class="d-block" style="margin-right: 196px;">
                    <?= __("Age") ?>
                  </span>
                        </button>

                        <!--
                        Selected filter + reset filter group example markup.
                        In case you want to pre-render active filters for group on the backend (highly appreciated)
                        Use the following markup:

                        <span data-sou-selected-filter-area="#offcanvas-accordion-filter-age" class="d-flex gap-1 flex-shrink-0 me-4 pe-2 position-absolute end-0 top-50 translate-middle-y z-3">
                          <button type="button" data-sou-reset-filter-group="#offcanvas-accordion-filter-age" class="btn btn-sm btn-filter rounded-pill">
                            <i class="btn-filter-badge zi-close"></i>
                            <span>2 filters</span>
                          </button>
                        </span> -->
                    </h3>
                    <div id="offcanvas-accordion-filter-age-collapse" class="accordion-collapse collapse show" data-bs-parent="#offcanvas-accordion-filters">
                        <div class="accordion-body">
                            <div class="row row-cols-2 gx-2 pt-2">
                                <div class="col">
                                    <div class="form-check">
                                        <input id="age-18-29" type="checkbox" name="age[]" value="18-29" class="form-check-input"<?= (in_array("18-29", $selectedAges) ? " checked" : "") ?>>
                                        <label for="age-18-29" class="form-check-label">
                                            18-29
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check">
                                        <input id="age-30-39" type="checkbox" name="age[]" value="30-39" class="form-check-input"<?= (in_array("30-39", $selectedAges) ? " checked" : "") ?>>
                                        <label for="age-30-39" class="form-check-label">
                                            30-39
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check">
                                        <input id="age-40-49" type="checkbox" name="age[]" value="40-49" class="form-check-input"<?= (in_array("40-49", $selectedAges) ? " checked" : "") ?>>
                                        <label for="age-40-49" class="form-check-label">
                                            40-49
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check">
                                        <input id="age-50-59" type="checkbox" name="age[]" value="50-59" class="form-check-input"<?= (in_array("50-59", $selectedAges) ? " checked" : "") ?>>
                                        <label for="age-50-59" class="form-check-label">
                                            50-59
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check">
                                        <input id="age-60-69" type="checkbox" name="age[]" value="60-69" class="form-check-input"<?= (in_array("60-69", $selectedAges) ? " checked" : "") ?>>
                                        <label for="age-60-69" class="form-check-label">
                                            60-69
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check">
                                        <input id="age-70" type="checkbox" name="age[]" value="70" class="form-check-input"<?= (in_array("70", $selectedAges) ? " checked" : "") ?>>
                                        <label for="age-70" class="form-check-label">
                                            70+
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skin Tone
                <div id="offcanvas-accordion-filter-skin" class="accordion-item border-bottom">
                    <h3 class="accordion-header position-relative">
                        <button class="accordion-button fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offcanvas-accordion-filter-skin-collapse" aria-expanded="false" aria-controls="offcanvas-accordion-filter-skin-collapse">
                  <span class="d-block" style="margin-right: 196px;">
                    Skin Tone
                  </span>
                        </button>
                    </h3>
                    <div id="offcanvas-accordion-filter-skin-collapse" class="accordion-collapse collapse" data-bs-parent="#offcanvas-accordion-filters">
                        <div class="accordion-body">
                            <div class="row row-cols-2 gx-2 pt-2">
                                <div class="col">
                                    <input id="skin-very-light" type="checkbox" name="skin[]" value="very-light" class="btn-check">
                                    <label for="skin-very-light" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #FFF6EA 0%, #A2806B 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Very light
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-light" type="checkbox" name="skin[]" value="light" class="btn-check">
                                    <label for="skin-light" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #FEF4E4 0%, #BC957C 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Light
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-light-to-medium" type="checkbox" name="skin[]" value="light-medium" class="btn-check">
                                    <label for="skin-light-to-medium" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #FEF4E4 0%, #BC957C 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Light to medium
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-medium" type="checkbox" name="skin[]" value="medium" class="btn-check">
                                    <label for="skin-medium" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #FDEFCF 0%, #AF8460 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Medium
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-medium-to-deep" type="checkbox" name="skin[]" value="medium-to-deep" class="btn-check">
                                    <label for="skin-medium-to-deep" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #F4DCB9 0%, #967254 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Medium to deep
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-deep" type="checkbox" name="skin[]" value="deep" class="btn-check">
                                    <label for="skin-deep" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #DDB385 0%, #7C5A3C 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Deep
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-deep-rich" type="checkbox" name="skin[]" value="deep-rich" class="btn-check">
                                    <label for="skin-deep-rich" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #B9834A 0%, #5D3E26 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Deep rich
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-very-deep" type="checkbox" name="skin[]" value="very-deep" class="btn-check">
                                    <label for="skin-very-deep" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #7B4433 0%, #442B24 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Very deep
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-dark-rich" type="checkbox" name="skin[]" value="dark-rich" class="btn-check">
                                    <label for="skin-dark-rich" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #563B33 0%, #2C251E 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Dark rich
                        </span>
                                    </label>
                                </div>
                                <div class="col">
                                    <input id="skin-darkest" type="checkbox" name="skin[]" value="darkest" class="btn-check">
                                    <label for="skin-darkest" class="form-label d-flex gap-2">
                        <span class="btn-swatch" style="background: linear-gradient(180deg, #46332E 0%, #2A251F 100%);">
                          <i class="btn-swatch-label zi-check"></i>
                        </span>
                                        <span class="align-self-center lh-1 ms-1">
                          Darkest
                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Anatomy
                <div id="offcanvas-accordion-filter-anatomy" class="accordion-item border-bottom">
                    <h3 class="accordion-header position-relative">
                        <button class="accordion-button fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offcanvas-accordion-filter-anatomy-collapse" aria-expanded="false" aria-controls="offcanvas-accordion-filter-anatomy-collapse">
                  <span class="d-block" style="margin-right: 196px;">
                    Anatomy
                  </span>
                        </button>
                    </h3>
                    <div id="offcanvas-accordion-filter-anatomy-collapse" class="accordion-collapse collapse" data-bs-parent="#offcanvas-accordion-filters">
                        <div class="accordion-body">
                            <div class="row gx-2">

                                <!-- Anatomy at birth --
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Anatomy at birth
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-birth-vulva" type="checkbox" name="anatomy-at-birth[]" value="vulva" class="form-check-input">
                                        <label for="anatomy-birth-vulva" class="form-check-label">
                                            Vulva
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-birth-penis" type="checkbox" name="anatomy-at-birth[]" value="penis" class="form-check-input">
                                        <label for="anatomy-birth-penis" class="form-check-label">
                                            Penis
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-birth-intersex" type="checkbox" name="anatomy-at-birth[]" value="intersex" class="form-check-input">
                                        <label for="anatomy-birth-intersex" class="form-check-label">
                                            Intersex
                                        </label>
                                    </div>
                                </div>

                                <!-- Current anatomy --
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Current anatomy
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-current-vulva" type="checkbox" name="current-anatomy[]" value="vulva" class="form-check-input">
                                        <label for="anatomy-current-vulva" class="form-check-label">
                                            Vulva
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-current-penis" type="checkbox" name="current-anatomy[]" value="penis" class="form-check-input">
                                        <label for="anatomy-current-penis" class="form-check-label">
                                            Penis
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-current-trans-vulva" type="checkbox" name="current-anatomy[]" value="post-surgery-transgender-vulva" class="form-check-input">
                                        <label for="anatomy-current-trans-vulva" class="form-check-label">
                                            Post-surgery Transgender Vulva
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-current-trans-penis" type="checkbox" name="current-anatomy[]" value="post-surgery-transgender-penis" class="form-check-input">
                                        <label for="anatomy-current-trans-penis" class="form-check-label">
                                            Post-surgery Transgender Penis
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-current-chest-breasts" type="checkbox" name="current-anatomy[]" value="chest-with-breasts" class="form-check-input">
                                        <label for="anatomy-current-chest-breasts" class="form-check-label">
                                            Chest with Breasts
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="anatomy-current-chest-flat" type="checkbox" name="current-anatomy[]" value="flat-chest" class="form-check-input">
                                        <label for="anatomy-current-chest-flat" class="form-check-label">
                                            Flat Chest (Post-Mastectomy or Gender-Affirming Surgery)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Country
                <div id="offcanvas-accordion-filter-country" class="accordion-item border-bottom">
                    <h3 class="accordion-header position-relative">
                        <button class="accordion-button fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offcanvas-accordion-filter-country-collapse" aria-expanded="false" aria-controls="offcanvas-accordion-filter-country-collapse">
                  <span class="d-block" style="margin-right: 196px;">
                    Country
                  </span>
                        </button>
                    </h3>
                    <div id="offcanvas-accordion-filter-country-collapse" class="accordion-collapse collapse" data-bs-parent="#offcanvas-accordion-filters">
                        <div class="accordion-body">
                            <div class="row gx-2">
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Country at birth
                                    </div>
                                    <select class="form-select form-select-lg rounded-pill" name="country-at-birth[]" multiple data-select='{
                        "classNames": {
                          "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                        },
                        "searchEnabled": true
                      }'>
                                        <option value="">Select country...</option>
                                        <optgroup label="Africa">
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                        </optgroup>
                                        <optgroup label="Asia">
                                            <option value="China">China</option>
                                            <option value="India">India</option>
                                            <option value="Japan">Japan</option>
                                            <option value="South Korea">South Korea</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                        </optgroup>
                                        <optgroup label="Europe">
                                            <option value="Germany">Germany</option>
                                            <option value="France">France</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Spain">Spain</option>
                                        </optgroup>
                                        <optgroup label="North America">
                                            <option value="United States">United States</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                        </optgroup>
                                        <optgroup label="South America">
                                            <option value="Brazil">Brazil</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Chile">Chile</option>
                                            <option value="Peru">Peru</option>
                                        </optgroup>
                                        <optgroup label="Oceania">
                                            <option value="Australia">Australia</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                            <option value="Fiji">Fiji</option>
                                            <option value="Solomon Islands">Solomon Islands</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Country of residence
                                    </div>
                                    <select class="form-select form-select-lg rounded-pill" name="country-of-residence[]" multiple data-select='{
                        "classNames": {
                          "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                        },
                        "searchEnabled": true
                      }'>
                                        <option value="">Select country...</option>
                                        <optgroup label="Africa">
                                            <option value="Nigeria">Nigeria</option>
                                            <option value="South Africa">South Africa</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Egypt">Egypt</option>
                                            <option value="Ethiopia">Ethiopia</option>
                                        </optgroup>
                                        <optgroup label="Asia">
                                            <option value="China">China</option>
                                            <option value="India">India</option>
                                            <option value="Japan">Japan</option>
                                            <option value="South Korea">South Korea</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                        </optgroup>
                                        <optgroup label="Europe">
                                            <option value="Germany">Germany</option>
                                            <option value="France">France</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="Italy">Italy</option>
                                            <option value="Spain">Spain</option>
                                        </optgroup>
                                        <optgroup label="North America">
                                            <option value="United States">United States</option>
                                            <option value="Canada">Canada</option>
                                            <option value="Mexico">Mexico</option>
                                            <option value="Jamaica">Jamaica</option>
                                            <option value="Costa Rica">Costa Rica</option>
                                        </optgroup>
                                        <optgroup label="South America">
                                            <option value="Brazil">Brazil</option>
                                            <option value="Argentina">Argentina</option>
                                            <option value="Colombia">Colombia</option>
                                            <option value="Chile">Chile</option>
                                            <option value="Peru">Peru</option>
                                        </optgroup>
                                        <optgroup label="Oceania">
                                            <option value="Australia">Australia</option>
                                            <option value="New Zealand">New Zealand</option>
                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                            <option value="Fiji">Fiji</option>
                                            <option value="Solomon Islands">Solomon Islands</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Medical History
                <div id="offcanvas-accordion-filter-medhist" class="accordion-item border-bottom">
                    <h3 class="accordion-header position-relative">
                        <button class="accordion-button fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offcanvas-accordion-filter-medhist-collapse" aria-expanded="false" aria-controls="offcanvas-accordion-filter-medhist-collapse">
                  <span class="d-block" style="margin-right: 196px;">
                    Medical History
                  </span>
                        </button>
                    </h3>
                    <div id="offcanvas-accordion-filter-medhist-collapse" class="accordion-collapse collapse" data-bs-parent="#offcanvas-accordion-filters">
                        <div class="accordion-body">

                            <!-- Vulva --
                            <div class="mb-3">
                                <div class="form-label">
                                    Vulva
                                </div>
                                <select class="form-select" name="medical-history-ex1" data-select='{
                      "classNames": {
                        "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                      }
                    }'>
                                    <option value="">Select option</option>
                                    <option value="option-item-1">Option item 1</option>
                                    <option value="option-item-2">Option item 2</option>
                                    <option value="option-item-3">Option item 3</option>
                                </select>
                            </div>

                            <!-- Breasts --
                            <div class="mb-3">
                                <div class="form-label">
                                    Breasts
                                </div>
                                <select class="form-select" name="medical-history-ex2" data-select='{
                      "classNames": {
                        "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                      }
                    }'>
                                    <option value="">Select option</option>
                                    <option value="option-item-1">Option item 1</option>
                                    <option value="option-item-2">Option item 2</option>
                                    <option value="option-item-3">Option item 3</option>
                                </select>
                            </div>

                            <!-- Penises --
                            <div>
                                <div class="form-label">
                                    Penises
                                </div>
                                <select class="form-select" name="medical-history-ex3" data-select='{
                      "classNames": {
                        "containerInner": ["form-select", "form-select-lg", "rounded-pill"]
                      }
                    }'>
                                    <option value="">Select option</option>
                                    <option value="option-item-1">Option item 1</option>
                                    <option value="option-item-2">Option item 2</option>
                                    <option value="option-item-3">Option item 3</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Other filters
                <div id="offcanvas-accordion-filter-other" class="accordion-item border-bottom">
                    <h3 class="accordion-header position-relative">
                        <button class="accordion-button fs-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#offcanvas-accordion-filter-other-collapse" aria-expanded="false" aria-controls="offcanvas-accordion-filter-other-collapse">
                  <span class="d-block" style="margin-right: 196px;">
                    Other filters
                  </span>
                        </button>
                    </h3>
                    <div id="offcanvas-accordion-filter-other-collapse" class="accordion-collapse collapse" data-bs-parent="#offcanvas-accordion-filters">
                        <div class="accordion-body">
                            <div class="row gx-2">

                                <!-- Gender Identity --
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Gender Identity
                                    </div>
                                    <div class="form-check">
                                        <input id="other-gender-man" type="checkbox" name="gender[]" value="man" class="form-check-input">
                                        <label for="other-gender-man" class="form-check-label">
                                            Man
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="other-gender-woman" type="checkbox" name="gender[]" value="woman" class="form-check-input">
                                        <label for="other-gender-woman" class="form-check-label">
                                            Woman
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="other-gender-non-binary" type="checkbox" name="gender[]" value="non-binary" class="form-check-input">
                                        <label for="other-gender-non-binary" class="form-check-label">
                                            Non-binary
                                        </label>
                                    </div>
                                </div>

                                <!-- Presence of Hair --
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Presence of Hair
                                    </div>
                                    <div class="form-check">
                                        <input id="other-hair-natural" type="checkbox" name="hair[]" value="natural" class="form-check-input">
                                        <label for="other-hair-natural" class="form-check-label">
                                            Natural
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="other-hair-trimmed" type="checkbox" name="hair[]" value="trimmed" class="form-check-input">
                                        <label for="other-hair-trimmed" class="form-check-label">
                                            Trimmed
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="other-hair-hairless" type="checkbox" name="hair[]" value="hairless" class="form-check-input">
                                        <label for="other-hair-hairless" class="form-check-label">
                                            Hairless
                                        </label>
                                    </div>
                                </div>

                                <!-- Stretch Marks or Scars --
                                <div class="col-12">
                                    <div class="mb-2 py-1 text-body-secondary">
                                        Stretch Marks or Scars
                                    </div>
                                    <div class="form-check">
                                        <input id="other-marks-none" type="checkbox" name="marks[]" value="no" class="form-check-input">
                                        <label for="other-marks-none" class="form-check-label">
                                            No stretch marks or scars
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="other-marks-stretch" type="checkbox" name="marks[]" value="stretch" class="form-check-input">
                                        <label for="other-marks-stretch" class="form-check-label">
                                            Stretch marks
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input id="other-marks-scars" type="checkbox" name="marks[]" value="scars" class="form-check-input">
                                        <label for="other-marks-scars" class="form-check-label">
                                            Scars
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Offcanvas footer -->
        <div class="offcanvas-header flex-column gap-2">
            <button type="reset" class="btn btn-lg btn-link w-100">
                Reset all filters
            </button>
            <!--<button type="submit" class="btn btn-lg btn-primary w-100 rounded-pill">
                Apply filters
            </button>-->
        </div>
    </form>



    <!-- Modals -->
    <div id="modal-image-viewer" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header py-lg-4 py-3 px-3 border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body px-3 px-lg-4 px-xxl-5">
                    <div class="d-flex flex-column flex-lg-row gap-4 align-items-start">

                        <!-- Image Section -->
                        <div class="flex-shrink-0 position-relative w-100 w-lg-60 text-center">
                            <img src="" alt="Image" class="img-fluid" style="max-height: 90vh; object-fit: contain;">
                            <button type="button"
                                    class="btn btn-lg btn-icon btn-light bg-transparent text-white border-0 position-absolute top-0 end-0 m-2 d-none d-lg-inline"
                                    id="toggle-fullscreen" aria-label="Toggle fullscreen">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none">
                                    <path d="M21.067 5.067l1.867 2L20 10c-.533.533-.533 1.333 0 1.867s1.333.533 1.867 0L24.8 8.933l2 1.867c.533.533 1.2.133 1.2-.4V4.667c0-.4-.267-.667-.667-.667H21.6c-.533 0-.933.667-.533 1.067zm-16 5.733l1.867-1.867 2.933 2.933c.533.533 1.333.533 1.867 0s.533-1.333 0-1.867l-2.8-2.933 1.867-2c.533-.4.133-1.067-.4-1.067H4.667c-.4 0-.667.267-.667.667V10.4c0 .533.667.933 1.067.4zm5.733 16l-2-1.867L11.733 22c.533-.533.533-1.333 0-1.867s-1.333-.533-1.867 0l-2.933 2.933-2-1.867c-.267-.533-.933-.133-.933.4v5.733c0 .4.267.667.667.667H10.4c.533 0 .933-.667.4-1.2zm16.133-5.733l-2 1.867L22 20c-.533-.533-1.333-.533-1.867 0s-.533 1.333 0 1.867l2.933 2.933-1.867 2c-.4.4-.133 1.2.533 1.2h5.733c.4 0 .667-.267.667-.667V21.6c-.133-.533-.8-.933-1.2-.533z"
                                          fill="currentColor" />
                                </svg>
                            </button>
                        </div>

                        <!-- Text / Props Section -->
                        <div id="image-props-container" class="flex-grow-1 w-100 w-lg-40">
                            <!-- Populated via JS -->
                        </div>

                    </div>
                </div>
            </div>
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
