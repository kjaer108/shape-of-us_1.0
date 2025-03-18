<?php
$page = ["name"=>"coming-soon", "translate"=>true, "sourcelang" => "en"];
require_once "src/inc/init.php";

//log_page_load();

//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body>

    <!-- Navigation bar (Page header) -->
    <header class="container-fluid position-absolute top-0 start-0 end-0">
        <div class="d-flex align-items-center justify-content-sm-end justify-content-between gap-2 py-2">
            <div class="d-sm-none d-flex align-items-center gap-4">
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
    </header>

    <!-- Page content -->
    <main class="content-wrapper">
        <div class="min-vh-100 d-flex align-items-center justify-content-center text-center px-3">
            <div class="mx-auto w-100" style="max-width: 43.125rem;">
                <a href="https://zandora.net/" target="_blank" class="d-inline-block">
                    <img src="assets/img/zandora.png" width="142" alt="Zandora">
                </a>
                <img src="assets/img/shape-of-us.png" width="456" alt="Shape of Us" class="d-block mx-auto">

                <!-- Form -->
                <div class="mt-4 pt-md-2">
                    <h1 class="fw-semibold">
                        <?= __("Coming Soon") ?>
                    </h1>
                    <p class="fs-lg">
                        <?= __("The Shape of Us Project is launching soon. Stay tuned for updates!") ?>
                    </p>
                    <form class="mt-4 pt-lg-3 pt-md-2 d-flex align-items-center gap-2 newsletter-signup">
                        <div class="border-bottom border-dark w-100">
                            <input type="email" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="<?= __("E-mail") ?>" required>
                        </div>
                        <button type="submit" class="btn btn-lg btn-primary rounded-pill py-3 flex-shrink-0">
                            <?= __("Sign up") ?>
                        </button>
                    </form>

                    <!-- Modal toggle -->
                    <button type="button" class="btn btn-lg btn-primary rounded-pill py-3 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Modal
                    </button>
                </div>
            </div>
        </div>
    </main>

<?php include "src/html/html_modals.php"; ?>
<?php include "src/html/html-scripts.php"; ?>
<?php include "src/html/html-end.php"; ?>