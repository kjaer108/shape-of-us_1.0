<!-- Navigation bar (Page header) -->
<header class="px-xl-4 px-lg-3 p-2 bg-body position-absolute top-0 start-0 end-0" style="z-index: 999;">

    <div class="container-fluid">
        <div class="row align-items-center">

            <!-- Navbar brand -->
            <div class="col-xl-4 col-md-8">
                <div class="d-flex justify-content-md-start justify-content-between align-items-center gap-xxl-4 gap-md-3 gap-4">
                    <div>
                        <a href="<?= get_url("app") ?>" rel="noopener" class="d-block ms-sm-2">
                            <img src="assets/img/shape-of-us.png" width="283" alt="Shape of Us">
                        </a>
                    </div>
                    <a href="https://zandora.net/" target="_blank" rel="noopener" class="d-block ms-sm-2">
                        <img src="assets/img/zandora.png" width="125" alt="by Zandora">
                    </a>
                </div>
            </div>

<?php if (isset($page['name']) && $page['name'] === "app"): ?>
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
<?php endif; ?>

            <!-- Navbar toolbar -->
            <div class="col d-md-block d-none">
                <div class="d-flex gap-2 justify-content-end">

<?php if (isset($page['name']) && $page['name'] === "app"): ?>
                    <!-- About link -->
                    <a href="<?= get_url("about") ?>" class="btn btn-link px-3 fs-base text-decoration-none">
                        <?= __("About") ?>
                    </a>

                    <!-- Filters btn -->
                    <button type="button" class="btn btn-light rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-filters" style="--zs-btn-padding-y: .75rem;" data-filter-button data-filter-text="<?= __("Filters") ?>">
                        <?= __("Filters") ?><?= $totalSelectedFilters > 0 ? " ($totalSelectedFilters)" : "" ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/></svg>
                    </button>

                    <button
                            type="button"
                            class="btn btn-link ps-2 pe-0 me-0 fs-base text-decoration-none"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvas-menu"
                            aria-controls="offcanvas-menu"
                    >
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                        <span class="visually-hidden">Open menu</span>
                    </button>
<?php else: ?>
                    <!-- Gallery link -->
                    <a href="<?= get_url("app") ?>" class="btn btn-link px-3 fs-base text-decoration-none">
                        <?= __("Gallery") ?>
                    </a>

                    <!-- Menu toggle -->
                    <button type="button" class="btn btn-light rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-menu" style="--zs-btn-padding-y: .75rem;">
                        Menu
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/></svg>
                    </button>
<?php endif; ?>

                    <!-- Lang switcher -->
                    <div class="dropdown">
                        <button class="btn btn-lg btn-icon btn-light rounded-circle bg-transparent border-0 text-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Switch language">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><g clip-path="url(#A)" fill="currentColor"><path d="M12.84 14.317c-.079-.095-.197-.15-.32-.15H7.479c-.124 0-.241.055-.32.15a.42.42 0 0 0-.09.342C7.668 17.904 8.819 20 10 20s2.331-2.096 2.93-5.341c.022-.122-.01-.247-.09-.342zm6.685-7.361c-.055-.173-.215-.29-.397-.29h-4.692a.42.42 0 0 0-.309.137c-.079.087-.118.204-.106.321A28.78 28.78 0 0 1 14.167 10c0 .95-.049 1.918-.145 2.875-.012.117.027.234.106.321s.191.137.309.137h4.692c.181 0 .342-.117.397-.29a10 10 0 0 0 .475-3.044 9.99 9.99 0 0 0-.475-3.044zM13.8 5.486c.034.201.207.348.411.348h4.206a.42.42 0 0 0 .359-.204c.075-.127.078-.283.007-.412-1.251-2.292-3.4-4.038-5.896-4.79-.161-.05-.338.005-.445.138s-.122.316-.039.464c.607 1.088 1.09 2.629 1.398 4.457zm-1.037 1.181H7.238c-.213 0-.391.16-.414.372A27.73 27.73 0 0 0 6.667 10a27.73 27.73 0 0 0 .157 2.961c.023.212.201.372.414.372h5.525c.213 0 .391-.16.414-.372A27.66 27.66 0 0 0 13.334 10a27.66 27.66 0 0 0-.157-2.961c-.023-.212-.201-.372-.414-.372zm-11.18-.834h4.206c.203 0 .377-.147.411-.347.307-1.828.791-3.369 1.398-4.457.083-.148.067-.332-.039-.464S7.275.377 7.113.427a10.08 10.08 0 0 0-5.896 4.79c-.07.129-.068.286.007.412a.42.42 0 0 0 .358.204zm4.29 7.363c.079-.087.118-.204.106-.321A28.8 28.8 0 0 1 5.833 10c0-.95.049-1.918.145-2.875a.41.41 0 0 0-.106-.321.42.42 0 0 0-.309-.137H.872c-.181 0-.342.117-.397.29a9.99 9.99 0 0 0 0 6.087c.055.173.215.29.397.29h4.692c.118 0 .23-.05.309-.137zm.326 1.318c-.034-.201-.208-.348-.411-.348H1.583a.42.42 0 0 0-.358.204c-.075.127-.078.283-.007.412 1.251 2.292 3.4 4.038 5.896 4.79a.41.41 0 0 0 .12.018.42.42 0 0 0 .325-.155c.106-.133.122-.316.039-.464-.607-1.088-1.091-2.629-1.398-4.457zm12.218-.347h-4.206c-.203 0-.377.147-.411.348-.307 1.828-.791 3.369-1.398 4.457-.083.148-.067.332.039.464.081.1.201.155.325.155a.41.41 0 0 0 .12-.018 10.08 10.08 0 0 0 5.896-4.79c.07-.129.068-.286-.007-.412a.42.42 0 0 0-.359-.204zM7.16 5.683c.079.095.197.15.32.15h5.041c.124 0 .241-.055.32-.15s.112-.221.089-.342C12.332 2.096 11.181 0 10.001 0S7.669 2.096 7.07 5.341c-.022.122.01.247.089.342z"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h20v20H0z"/></clipPath></defs></svg>
                        </button>
                        <?php $groupSuffix = 'desktop'; // or 'mobile' ?>
                        <ul class="dropdown-menu p-4">
                            <?php foreach ($language_support as $lang): ?>
                                <?php $id = "lang-{$groupSuffix}-{$lang}"; ?>
                                <li>
                                    <div class="form-check mb-3">
                                        <input
                                            id="<?php echo $id; ?>"
                                            type="radio"
                                            name="site-language-<?php echo $groupSuffix; ?>"
                                            class="form-check-input border"
                                            value="<?php echo $lang; ?>"
                                            <?php echo ($lang == $selectedLang) ? 'checked' : ''; ?>
                                            onchange="setLanguage('<?php echo $lang; ?>')">
                                        <label for="<?php echo $id; ?>" class="form-check-label">
                                            <?php echo $language_data['name'][$lang]; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</header>

<!-- Offcanvas menu (About page) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-menu" aria-labelledby="offcanvasMenu">

    <!-- Offcanvas header -->
    <div class="offcanvas-header justify-content-between pb-0">
        <h2 class="offcanvas-title text-primary" id="offcanvasMenu">
            Menu
        </h2>

        <!-- Close btn -->
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-lg btn-link px-0" data-bs-dismiss="offcanvas">
                close
            </button>
        </div>
    </div>

    <!-- Offcanvas body -->
    <div class="offcanvas-body pt-3">

        <!-- Navigation -->
        <ul class="nav nav-hover-underline flex-column" style="--zs-nav-link-font-size: 1.5rem; --zs-nav-link-padding-y: 1.5rem; --zs-nav-link-padding-x: 0; --zs-nav-link-color: var(--zs-component-hover-color);">
            <li class="nav-item">
                <a href="<?= get_url("form") ?>" class="nav-link">
                    <?= __("Participation Form") ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= get_url("present") ?>" class="nav-link">
                    <?= __("Presentation Mode") ?>
                </a>
            </li>
        </ul>
    </div>
</div>


<!-- Navigation bar - mobile -->
<?php if (isset($page['name']) && $page['name'] === "app"): ?>
<div class="sticky-top d-md-none mb-3" style="z-index: 999; top: 16px !important; margin-top: 61px;">
<?php else: ?>
<div class="sticky-top d-md-none mb-3" style="z-index: 999; top: 16px !important; margin-top: 61px;">
<?php endif; ?>
    <div class="container">
        <div class="py-2 px-4 rounded-pill" style="background-color: #E5DED1;">
            <div class="row g-3 align-items-center justify-content-between">

<?php if (isset($page['name']) && $page['name'] === "app"): ?>
                <!-- Gallery link -->
                <div class="col-3 text-start">
                    <a href="<?= get_url("about") ?>" class="btn btn-link px-3 fs-base text-decoration-none">
                        About
                    </a>
                </div>

                <!-- Filters toggle -->
                <div class="col-6 text-center">
                    <button type="button" class="btn btn-light rounded-pill w-100 justify-content-between" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-filters" style="--zs-btn-padding-y: .75rem;">
                        Filters (7)
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/></svg>
                    </button>
                </div>
<?php else: ?>
                <!-- Gallery link -->
                <div class="col-3 text-start">
                    <a href="<?= get_url("app") ?>" class="btn btn-link px-3 fs-base text-decoration-none">
                        Gallery
                    </a>
                </div>

                <!-- Menu toggle -->
                <div class="col-6 text-center">
                    <button type="button" class="btn btn-light rounded-pill w-100 justify-content-between" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-menu" style="--zs-btn-padding-y: .75rem;">
                        Menu
                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none"><path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/></svg>
                    </button>
                </div>
<?php endif; ?>

                <!-- Lang switcher -->
                <div class="col-3 text-end">
                    <div class="d-inline-flex align-items-center flex-nowrap gap-1">
<?php if (isset($page['name']) && $page['name'] === "app"): ?>
                        <button
                                type="button"
                                class="btn btn-link p-2 text-decoration-none text-primary flex-shrink-0"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas-menu"
                                aria-controls="offcanvas-menu"
                                aria-label="Open menu"
                        >
                            <i class="fa-solid fa-bars fs-5" aria-hidden="true"></i>
                        </button>
<?php endif; ?>

                        <div class="dropdown flex-shrink-0">
                            <button
                                    class="btn btn-icon rounded-circle p-0 border-0 text-primary"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    aria-label="Switch language"
                                    style="width: 40px; height: 40px;"
                            >
                                <svg class="flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"><g clip-path="url(#A)" fill="currentColor"><path d="M12.84 14.317c-.079-.095-.197-.15-.32-.15H7.479c-.124 0-.241.055-.32.15a.42.42 0 0 0-.09.342C7.668 17.904 8.819 20 10 20s2.331-2.096 2.93-5.341c.022-.122-.01-.247-.09-.342zm6.685-7.361c-.055-.173-.215-.29-.397-.29h-4.692a.42.42 0 0 0-.309.137c-.079.087-.118.204-.106.321A28.78 28.78 0 0 1 14.167 10c0 .95-.049 1.918-.145 2.875-.012.117.027.234.106.321s.191.137.309.137h4.692c.181 0 .342-.117.397-.29a10 10 0 0 0 .475-3.044 9.99 9.99 0 0 0-.475-3.044zM13.8 5.486c.034.201.207.348.411.348h4.206a.42.42 0 0 0 .359-.204c.075-.127.078-.283.007-.412-1.251-2.292-3.4-4.038-5.896-4.79-.161-.05-.338.005-.445.138s-.122.316-.039.464c.607 1.088 1.09 2.629 1.398 4.457zm-1.037 1.181H7.238c-.213 0-.391.16-.414.372A27.73 27.73 0 0 0 6.667 10a27.73 27.73 0 0 0 .157 2.961c.023.212.201.372.414.372h5.525c.213 0 .391-.16.414-.372A27.66 27.66 0 0 0 13.334 10a27.66 27.66 0 0 0-.157-2.961c-.023-.212-.201-.372-.414-.372zm-11.18-.834h4.206c.203 0 .377-.147.411-.347.307-1.828.791-3.369 1.398-4.457.083-.148.067-.332-.039-.464S7.275.377 7.113.427a10.08 10.08 0 0 0-5.896 4.79c-.07.129-.068.286.007.412a.42.42 0 0 0 .358.204zm4.29 7.363c.079-.087.118-.204.106-.321A28.8 28.8 0 0 1 5.833 10c0-.95.049-1.918.145-2.875a.41.41 0 0 0-.106-.321.42.42 0 0 0-.309-.137H.872c-.181 0-.342.117-.397.29a9.99 9.99 0 0 0 0 6.087c.055.173.215.29.397.29h4.692c.118 0 .23-.05.309-.137zm.326 1.318c-.034-.201-.208-.348-.411-.348H1.583a.42.42 0 0 0-.358.204c-.075.127-.078.283-.007.412 1.251 2.292 3.4 4.038 5.896 4.79a.41.41 0 0 0 .12.018.42.42 0 0 0 .325-.155c.106-.133.122-.316.039-.464-.607-1.088-1.091-2.629-1.398-4.457zm12.218-.347h-4.206c-.203 0-.377.147-.411.348-.307 1.828-.791 3.369-1.398 4.457-.083.148-.067.332.039.464.081.1.201.155.325.155a.41.41 0 0 0 .12-.018 10.08 10.08 0 0 0 5.896-4.79c.07-.129.068-.286-.007-.412a.42.42 0 0 0-.359-.204zM7.16 5.683c.079.095.197.15.32.15h5.041c.124 0 .241-.055.32-.15s.112-.221.089-.342C12.332 2.096 11.181 0 10.001 0S7.669 2.096 7.07 5.341c-.022.122.01.247.089.342z"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h20v20H0z"/></clipPath></defs></svg>
                            </button>
                            <?php $groupSuffix = 'mobile'; // or 'mobile' ?>
                                <ul class="dropdown-menu dropdown-menu-end p-4">
                                <?php foreach ($language_support as $lang): ?>
                                    <?php $id = "lang-{$groupSuffix}-{$lang}"; ?>
                                    <li>
                                        <div class="form-check mb-3">
                                            <input
                                                id="<?php echo $id; ?>"
                                                type="radio"
                                                name="site-language-<?php echo $groupSuffix; ?>"
                                                class="form-check-input border"
                                                value="<?php echo $lang; ?>"
                                                <?php echo ($lang == $selectedLang) ? 'checked' : ''; ?>
                                                onchange="setLanguage('<?php echo $lang; ?>')">
                                            <label for="<?php echo $id; ?>" class="form-check-label">
                                                <?php echo $language_data['name'][$lang]; ?>
                                            </label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>