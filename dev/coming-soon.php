<?php
$page = ["name"=>"frontpage", "translate"=>true];
require_once "src/inc/init.php";

//log_page_load();

//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body>

<!-- Page content -->
<main class="content-wrapper">
    <div class="min-vh-100 d-flex align-items-center justify-content-center text-center px-3">
        <div class="mx-auto w-100" style="max-width: 43.125rem;">
            <a href="#" class="d-inline-block">
                <img src="assets/img/zandora.png" width="106" alt="Zandora">
            </a>
            <img src="assets/img/shape-of-us.png" width="456" alt="Shape of Us" class="d-block mx-auto">

            <!-- Form -->
            <div class="mt-4 pt-md-2">
                <h1 class="fw-semibold">
                    Coming Soon
                </h1>
                <p class="fs-lg">
                    The Shape of Us Project is launching soon. Stay tuned for updates!
                </p>
                <form class="mt-4 pt-lg-3 pt-md-2 d-flex align-items-center gap-2">
                    <div class="border-bottom border-dark w-100">
                        <input type="email" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="Email" required>
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary rounded-pill py-3 flex-shrink-0">
                        Sign up
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include "src/html/html-scripts.php"; ?>
<?php include "src/html/html-end.php"; ?>