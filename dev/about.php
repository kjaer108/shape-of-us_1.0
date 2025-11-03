<?php
$page = ["name"=>"about", "translate"=>true];
require_once "src/inc/init.php";

//log_page_load();

zdebug($selectedLang);

//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body>

    <?php include "src/html/html-header.php"; ?>

    <!-- Page content -->
    <main class="content-wrapper">

        <!-- Hero -->
        <section class="position-relative">
            <span class="d-md-block d-none" style="min-height: 900px;"></span>
            <span class="d-md-none d-block" style="min-height: 428px;"></span>

            <!-- Image + video -->
            <div class="row row-cols-md-2 row-cols-1 gx-0 position-absolute w-100 h-100" style="inset: 0;">

                <!-- Image -->
                <div class="col position-relative d-md-block d-none">
                    <img src="assets/img/home/hero/image.jpg" alt="Hero image" class="position-absolute w-100 h-100" style="object-fit: cover;">
                    <span class="position-absolute" style="inset: 0; background-color: #27171133;"></span>
                </div>

                <!-- Video -->
                <div class="col position-relative">
                    <video autoplay muted loop playsinline poster="assets/img/home/hero/poster.jpg" class="position-absolute w-100 h-100" style="object-fit: cover; object-position: center right;">
                        <source src="assets/img/home/hero/video.webm" type="video/webm">
                        <source src="assets/img/home/hero/video.mp4" type="video/mp4">
                    </video>
                    <span class="position-absolute" style="inset: 0; background-color: #27171166;"></span>
                </div>
            </div>

            <!-- Page title + logo -->
            <div class="position-absolute top-50 start-0 end-0 translate-middle-y text-center">
                <h1 class="h2 mb-0 text-white">
                    Body Positivity Gallery
                </h1>
                <div class="ps-ms-0 pe-md-5 px-3">
                    <img src="assets/img/home/hero/logo.png" width="1311" alt="Logo" class="d-block mx-auto">
                </div>
            </div>
        </section>


        <!-- About -->
        <section class="py-5">
            <div class="my-lg-5 my-md-4 my-3 py-xxl-3 container">
                <div class="mb-md-5 mb-4 pb-xxl-4 pb-xl-3 pb-lg-2 pb-md-0 pb-2">
                    <h2 class="h1 mb-0 fw-semibold text-center text-primary">
                        <?= __("The gallery is a celebration of the diversity of human bodies, featuring close-up images of vulvas, penises, breasts, and buttocks to foster understanding, acceptance, and the normalization of body variations") ?>
                    </h2>
                </div>

                <!-- Items row -->
                <div class="row row-cols-lg-3 row-cols-1 gy-4 gx-lg-5 text-center">

                    <!-- Item -->
                    <div class="col">
                        <div class="mx-auto" style="max-width: 23.625rem;">
                            <span class="d-block mb-4 pb-lg-3 pb-md-2 mx-auto border-top border-primary" style="width: 2.5rem;"></span>
                            <h3 class="h6 mb-0 fs-xl fw-normal text-primary">
                                <?= __("Explore a gallery that showcases the incredible variety of real human bodies — all shapes, sizes, and colors — in a safe and respectful space.") ?>
                            </h3>
                        </div>
                    </div>

                    <!-- Item -->
                    <div class="col">
                        <div class="mx-auto" style="max-width: 23.625rem;">
                            <span class="d-block mb-4 pb-lg-3 pb-md-2 mx-auto border-top border-primary" style="width: 2.5rem;"></span>
                            <h3 class="h6 mb-0 fs-xl fw-normal text-primary">
                                <?= __("Discover reflections of your own story through the images and experiences of others. Find connection and belonging in our shared humanity.") ?>
                            </h3>
                        </div>
                    </div>

                    <!-- Item -->
                    <div class="col">
                        <div class="mx-auto" style="max-width: 23.625rem;">
                            <span class="d-block mb-4 pb-lg-3 pb-md-2 mx-auto border-top border-primary" style="width: 2.5rem;"></span>
                            <h3 class="h6 mb-0 fs-xl fw-normal text-primary">
                                <?= __("Feel inspired to embrace your body as it is. This space is designed to foster confidence, self-respect, and personal growth.") ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Gallery button -->
                <div class="mt-md-5 mt-4 pt-xxl-4 pt-xl-3 pt-lg-2 pt-md-0 pt-2 text-center">
                    <a href="<?= get_url("app") ?>" class="btn btn-lg btn-primary rounded-pill">
                        <?= __("Go to gallery") ?>
                        <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>


        <!-- CTA -->
        <section class="py-5 bg-white">
            <div class="my-lg-5 my-md-4 my-3 py-xxl-3 container">
                <div class="row gy-5 gx-lg-4 gx-md-5 align-items-center">

                    <!-- Text -->
                    <div class="col-lg-5 col-md-6 order-md-1 order-2">
                        <h2 class="h1 mb-md-4 mb-3 fw-semibold text-primary">
                            <?= __("Why We Need You") ?>
                        </h2>
                        <p class="mb-lg-5 mb-4 pb-lg-0 pb-2 pe-lg-4 fs-xl lh-sm">
                            <?= __("We're calling on individuals from all walks of life to contribute to this important project. By participating, you'll help us build a visual archive that celebrates the natural diversity of human anatomy and provides others with the opportunity to see themselves in the images. Whether your body has stretch marks, scars, piercings, tattoos, or reflects life experiences like pregnancy or surgery, your participation matters.") ?>
                        </p>
                        <div class="d-flex flex-sm-row flex-column">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-register-interest" class="btn btn-lg btn-primary rounded-pill">
                                <?= __("Participate now") ?>
                                <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Illustration -->
                    <div class="col-md-6 offset-lg-1 order-md-2 order-1">
                        <img src="assets/img/home/cta/illustration.png" alt="Illustration">
                    </div>
                </div>
            </div>
        </section>


        <!-- Participate -->
        <div class="py-5" style="background-image: url(assets/img/home/participate/illustration.jpg); background-position: center; background-size: cover; background-repeat: no-repeat;">
            <div class="my-lg-5 my-md-4 my-3 py-xxl-3 container">

                <!-- Card -->
                <div class="card py-sm-2 bg-white border-0">
                    <div class="card-body m-xxl-4 m-xl-3 my-2 p-md-5">
                        <div class="row gy-4 gx-lg-4 gx-md-5">

                            <!-- Text -->
                            <div class="col-lg-5 col-md-6">
                                <h2 class="h1 mb-md-4 mb-3 fw-semibold text-primary">
                                    What's In It For You?
                                </h2>
                                <p class="mb-lg-5 mb-4 pb-lg-0 pb-md-2 fs-xl lh-sm">
                                    By participating, you become part of a movement that challenges beauty standards and promotes self-acceptance. Your contribution helps others see the beauty in diversity and recognize themselves in the images.
                                </p>
                                <div class="d-flex flex-sm-row flex-column">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-register-interest" class="btn btn-lg btn-primary rounded-pill">
                                        Be part of a movement
                                        <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="col-md-6 offset-lg-1">
                                <div class="d-flex flex-column gap-lg-5 gap-4 mt-md-0 mt-2 pe-xl-5 fs-lg text-dark-emphasis">

                                    <!-- Item -->
                                    <div class="d-flex align-items-start gap-lg-4 gap-3">
                                        <img src="assets/img/home/participate/icons/01.svg" alt="Icon" class="flex-shrink-0">
                                        <div>
                                            <h3 class="mb-2 pb-1 fw-semibold text-primary">
                                                Be Part of a Movement
                                            </h3>
                                            <p class="mb-0 text-body-secondary">
                                                Contribute to a groundbreaking gallery promoting body positivity and self-acceptance.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Item -->
                                    <div class="d-flex align-items-start gap-lg-4 gap-3">
                                        <img src="assets/img/home/participate/icons/02.svg" alt="Icon" class="flex-shrink-0">
                                        <div>
                                            <h3 class="mb-2 pb-1 fw-semibold text-primary">
                                                Inspire Others
                                            </h3>
                                            <p class="mb-0 text-body-secondary">
                                                Your participation will help others see their own beauty and uniqueness.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Item -->
                                    <div class="d-flex align-items-start gap-lg-4 gap-3">
                                        <img src="assets/img/home/participate/icons/03.svg" alt="Icon" class="flex-shrink-0">
                                        <div>
                                            <h3 class="mb-2 pb-1 fw-semibold text-primary">
                                                Celebrate Diversity
                                            </h3>
                                            <p class="mb-0 text-body-secondary">
                                                Help break down societal taboos and normalize body differences.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Events -->
        <section class="py-5 bg-white">
            <div class="my-lg-5 my-md-4 my-3 py-xxl-3 container">
                <div class="row gy-2 gx-xl-4 gx-lg-5">

                    <!-- Title + image -->
                    <div class="col-xl-3 col-md-4">
                        <h2 class="h1 mb-md-4 mb-3 fw-semibold text-primary">
                            Magic Wand-test event
                        </h2>
                        <p class="mb-md-4 mb-3 fs-lg lh-sm">
                            Superpower your orgasms! Set the extension on your clit, and rock the AMORINO back and forth against your G-spot. (For anal play, just remove the band!)
                        </p>

                        <!-- Illustration (mobile) -->
                        <div class="d-md-block d-none">
                            <img src="assets/img/home/events/illustration.jpg" width="240" alt="Illustration" class="rounded-pill">
                        </div>
                    </div>

                    <!-- Events list -->
                    <div class="col-md-8 offset-xl-1">
                        <ul class="list-unstyled">

                            <!-- Item -->
                            <li>
                                <article class="position-relative d-flex align-items-start gap-4 p-2 bg-body-secondary rounded hover-fade">
                                    <div class="flex-shrink-0 d-flex flex-column align-items-center justify-content-center p-2 bg-white rounded text-center" style="width: 5.25rem; height: 5.25rem;">
                                        <h4 class="h3 mb-0 fw-semibold text-primary" style="line-height: .75;">
                                            16.
                                            <span class="fs-sm fw-medium">
                          August
                        </span>
                                        </h4>
                                    </div>
                                    <div class="align-self-center d-flex align-items-center justify-content-between gap-3 w-100">
                                        <div>
                                            <h3 class="h6 mb-2">
                                                <a href="#" class="stretched-link">
                                                    Nordjylland
                                                </a>
                                            </h3>
                                            <ul class="list-unstyled my-0 text-body-tertiary" style="gap: .125rem;">
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><path d="M10.5 1.772A5.25 5.25 0 0 0 6.665.411 5.25 5.25 0 0 0 3.009 2.2 5.25 5.25 0 0 0 1.73 6.063 5.25 5.25 0 0 0 3.595 9.68a12.77 12.77 0 0 1 3.019 3.654.44.44 0 0 0 .386.229.44.44 0 0 0 .224-.062c.068-.04.123-.099.161-.168l.036-.067c.775-1.379 1.797-2.603 3.014-3.612.564-.488 1.018-1.09 1.331-1.767s.478-1.413.484-2.159-.147-1.484-.449-2.166-.746-1.292-1.302-1.789zM7 7.875a2.19 2.19 0 0 1-2.021-1.35c-.166-.4-.209-.84-.124-1.264s.293-.814.599-1.12.696-.514 1.12-.599.864-.041 1.264.124a2.19 2.19 0 0 1 1.35 2.021A2.19 2.19 0 0 1 7 7.875z" fill="currentColor"/></svg>
                                                    </div>
                                                    Tucan Erotic Night Club
                                                </li>
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><g clip-path="url(#A)"><path d="M7 .875c-1.211 0-2.396.359-3.403 1.032s-1.792 1.63-2.256 2.749S.756 7.007.993 8.195s.82 2.279 1.676 3.136 1.948 1.44 3.136 1.676 2.42.115 3.539-.349 2.076-1.249 2.749-2.256S13.125 8.211 13.125 7c0-1.624-.645-3.182-1.794-4.331S8.624.875 7 .875zm2.625 6.563H7c-.116 0-.227-.046-.309-.128S6.563 7.116 6.563 7V3.063c0-.116.046-.227.128-.309s.193-.128.309-.128.227.046.309.128.128.193.128.309v3.5h2.188c.116 0 .227.046.309.128s.128.193.128.309-.046.227-.128.309-.193.128-.309.128z" fill="currentColor"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h14v14H0z"/></clipPath></defs></svg>
                                                    </div>
                                                    fredag 09:00 til 20:00
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="hover-fade-item d-sm-block d-none">
                                            <div class="btn btn-lg btn-primary rounded-pill pe-none">
                                                Kob billet
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </li>

                            <!-- Item -->
                            <li>
                                <article class="position-relative d-flex align-items-start gap-4 p-2 bg-body-secondary rounded hover-fade">
                                    <div class="flex-shrink-0 d-flex flex-column align-items-center justify-content-center p-2 bg-white rounded text-center" style="width: 5.25rem; height: 5.25rem;">
                                        <h4 class="h3 mb-0 fw-semibold text-primary" style="line-height: .75;">
                                            20.
                                            <span class="fs-sm fw-medium">
                          August
                        </span>
                                        </h4>
                                    </div>
                                    <div class="align-self-center d-flex align-items-center justify-content-between gap-3 w-100">
                                        <div>
                                            <h3 class="h6 mb-2">
                                                <a href="#" class="stretched-link">
                                                    Nordjylland
                                                </a>
                                            </h3>
                                            <ul class="list-unstyled my-0 text-body-tertiary" style="gap: .125rem;">
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><path d="M10.5 1.772A5.25 5.25 0 0 0 6.665.411 5.25 5.25 0 0 0 3.009 2.2 5.25 5.25 0 0 0 1.73 6.063 5.25 5.25 0 0 0 3.595 9.68a12.77 12.77 0 0 1 3.019 3.654.44.44 0 0 0 .386.229.44.44 0 0 0 .224-.062c.068-.04.123-.099.161-.168l.036-.067c.775-1.379 1.797-2.603 3.014-3.612.564-.488 1.018-1.09 1.331-1.767s.478-1.413.484-2.159-.147-1.484-.449-2.166-.746-1.292-1.302-1.789zM7 7.875a2.19 2.19 0 0 1-2.021-1.35c-.166-.4-.209-.84-.124-1.264s.293-.814.599-1.12.696-.514 1.12-.599.864-.041 1.264.124a2.19 2.19 0 0 1 1.35 2.021A2.19 2.19 0 0 1 7 7.875z" fill="currentColor"/></svg>
                                                    </div>
                                                    Tucan Erotic Night Club
                                                </li>
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><g clip-path="url(#A)"><path d="M7 .875c-1.211 0-2.396.359-3.403 1.032s-1.792 1.63-2.256 2.749S.756 7.007.993 8.195s.82 2.279 1.676 3.136 1.948 1.44 3.136 1.676 2.42.115 3.539-.349 2.076-1.249 2.749-2.256S13.125 8.211 13.125 7c0-1.624-.645-3.182-1.794-4.331S8.624.875 7 .875zm2.625 6.563H7c-.116 0-.227-.046-.309-.128S6.563 7.116 6.563 7V3.063c0-.116.046-.227.128-.309s.193-.128.309-.128.227.046.309.128.128.193.128.309v3.5h2.188c.116 0 .227.046.309.128s.128.193.128.309-.046.227-.128.309-.193.128-.309.128z" fill="currentColor"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h14v14H0z"/></clipPath></defs></svg>
                                                    </div>
                                                    fredag 09:00 til 20:00
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="hover-fade-item d-sm-block d-none">
                                            <div class="btn btn-lg btn-primary rounded-pill pe-none">
                                                Kob billet
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </li>

                            <!-- Item -->
                            <li>
                                <article class="position-relative d-flex align-items-start gap-4 p-2 bg-body-secondary rounded hover-fade">
                                    <div class="flex-shrink-0 d-flex flex-column align-items-center justify-content-center p-2 bg-white rounded text-center" style="width: 5.25rem; height: 5.25rem;">
                                        <h4 class="h3 mb-0 fw-semibold text-primary" style="line-height: .75;">
                                            22.
                                            <span class="fs-sm fw-medium">
                          August
                        </span>
                                        </h4>
                                    </div>
                                    <div class="align-self-center d-flex align-items-center justify-content-between gap-3 w-100">
                                        <div>
                                            <h3 class="h6 mb-2">
                                                <a href="#" class="stretched-link">
                                                    København
                                                </a>
                                            </h3>
                                            <ul class="list-unstyled my-0 text-body-tertiary" style="gap: .125rem;">
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><path d="M10.5 1.772A5.25 5.25 0 0 0 6.665.411 5.25 5.25 0 0 0 3.009 2.2 5.25 5.25 0 0 0 1.73 6.063 5.25 5.25 0 0 0 3.595 9.68a12.77 12.77 0 0 1 3.019 3.654.44.44 0 0 0 .386.229.44.44 0 0 0 .224-.062c.068-.04.123-.099.161-.168l.036-.067c.775-1.379 1.797-2.603 3.014-3.612.564-.488 1.018-1.09 1.331-1.767s.478-1.413.484-2.159-.147-1.484-.449-2.166-.746-1.292-1.302-1.789zM7 7.875a2.19 2.19 0 0 1-2.021-1.35c-.166-.4-.209-.84-.124-1.264s.293-.814.599-1.12.696-.514 1.12-.599.864-.041 1.264.124a2.19 2.19 0 0 1 1.35 2.021A2.19 2.19 0 0 1 7 7.875z" fill="currentColor"/></svg>
                                                    </div>
                                                    The Upper Floor DK
                                                </li>
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><g clip-path="url(#A)"><path d="M7 .875c-1.211 0-2.396.359-3.403 1.032s-1.792 1.63-2.256 2.749S.756 7.007.993 8.195s.82 2.279 1.676 3.136 1.948 1.44 3.136 1.676 2.42.115 3.539-.349 2.076-1.249 2.749-2.256S13.125 8.211 13.125 7c0-1.624-.645-3.182-1.794-4.331S8.624.875 7 .875zm2.625 6.563H7c-.116 0-.227-.046-.309-.128S6.563 7.116 6.563 7V3.063c0-.116.046-.227.128-.309s.193-.128.309-.128.227.046.309.128.128.193.128.309v3.5h2.188c.116 0 .227.046.309.128s.128.193.128.309-.046.227-.128.309-.193.128-.309.128z" fill="currentColor"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h14v14H0z"/></clipPath></defs></svg>
                                                    </div>
                                                    fredag 09:00 til 20:00
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="hover-fade-item d-sm-block d-none">
                                            <div class="btn btn-lg btn-primary rounded-pill pe-none">
                                                Kob billet
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </li>

                            <!-- Item -->
                            <li>
                                <article class="position-relative d-flex align-items-start gap-4 p-2 bg-body-secondary rounded hover-fade">
                                    <div class="flex-shrink-0 d-flex flex-column align-items-center justify-content-center p-2 bg-white rounded text-center" style="width: 5.25rem; height: 5.25rem;">
                                        <h4 class="h3 mb-0 fw-semibold text-primary" style="line-height: .75;">
                                            26.
                                            <span class="fs-sm fw-medium">
                          August
                        </span>
                                        </h4>
                                    </div>
                                    <div class="align-self-center d-flex align-items-center justify-content-between gap-3 w-100">
                                        <div>
                                            <h3 class="h6 mb-2">
                                                <a href="#" class="stretched-link">
                                                    Aalborg
                                                </a>
                                            </h3>
                                            <ul class="list-unstyled my-0 text-body-tertiary" style="gap: .125rem;">
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><path d="M10.5 1.772A5.25 5.25 0 0 0 6.665.411 5.25 5.25 0 0 0 3.009 2.2 5.25 5.25 0 0 0 1.73 6.063 5.25 5.25 0 0 0 3.595 9.68a12.77 12.77 0 0 1 3.019 3.654.44.44 0 0 0 .386.229.44.44 0 0 0 .224-.062c.068-.04.123-.099.161-.168l.036-.067c.775-1.379 1.797-2.603 3.014-3.612.564-.488 1.018-1.09 1.331-1.767s.478-1.413.484-2.159-.147-1.484-.449-2.166-.746-1.292-1.302-1.789zM7 7.875a2.19 2.19 0 0 1-2.021-1.35c-.166-.4-.209-.84-.124-1.264s.293-.814.599-1.12.696-.514 1.12-.599.864-.041 1.264.124a2.19 2.19 0 0 1 1.35 2.021A2.19 2.19 0 0 1 7 7.875z" fill="currentColor"/></svg>
                                                    </div>
                                                    Tucan Erotic Night Club
                                                </li>
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><g clip-path="url(#A)"><path d="M7 .875c-1.211 0-2.396.359-3.403 1.032s-1.792 1.63-2.256 2.749S.756 7.007.993 8.195s.82 2.279 1.676 3.136 1.948 1.44 3.136 1.676 2.42.115 3.539-.349 2.076-1.249 2.749-2.256S13.125 8.211 13.125 7c0-1.624-.645-3.182-1.794-4.331S8.624.875 7 .875zm2.625 6.563H7c-.116 0-.227-.046-.309-.128S6.563 7.116 6.563 7V3.063c0-.116.046-.227.128-.309s.193-.128.309-.128.227.046.309.128.128.193.128.309v3.5h2.188c.116 0 .227.046.309.128s.128.193.128.309-.046.227-.128.309-.193.128-.309.128z" fill="currentColor"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h14v14H0z"/></clipPath></defs></svg>
                                                    </div>
                                                    fredag 09:00 til 20:00
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="hover-fade-item d-sm-block d-none">
                                            <div class="btn btn-lg btn-primary rounded-pill pe-none">
                                                Kob billet
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </li>

                            <!-- Item -->
                            <li>
                                <article class="position-relative d-flex align-items-start gap-4 p-2 bg-body-secondary rounded hover-fade">
                                    <div class="flex-shrink-0 d-flex flex-column align-items-center justify-content-center p-2 bg-white rounded text-center" style="width: 5.25rem; height: 5.25rem;">
                                        <h4 class="h3 mb-0 fw-semibold text-primary" style="line-height: .75;">
                                            30.
                                            <span class="fs-sm fw-medium">
                          August
                        </span>
                                        </h4>
                                    </div>
                                    <div class="align-self-center d-flex align-items-center justify-content-between gap-3 w-100">
                                        <div>
                                            <h3 class="h6 mb-2">
                                                <a href="#" class="stretched-link">
                                                    Asylvej
                                                </a>
                                            </h3>
                                            <ul class="list-unstyled my-0 text-body-tertiary" style="gap: .125rem;">
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><path d="M10.5 1.772A5.25 5.25 0 0 0 6.665.411 5.25 5.25 0 0 0 3.009 2.2 5.25 5.25 0 0 0 1.73 6.063 5.25 5.25 0 0 0 3.595 9.68a12.77 12.77 0 0 1 3.019 3.654.44.44 0 0 0 .386.229.44.44 0 0 0 .224-.062c.068-.04.123-.099.161-.168l.036-.067c.775-1.379 1.797-2.603 3.014-3.612.564-.488 1.018-1.09 1.331-1.767s.478-1.413.484-2.159-.147-1.484-.449-2.166-.746-1.292-1.302-1.789zM7 7.875a2.19 2.19 0 0 1-2.021-1.35c-.166-.4-.209-.84-.124-1.264s.293-.814.599-1.12.696-.514 1.12-.599.864-.041 1.264.124a2.19 2.19 0 0 1 1.35 2.021A2.19 2.19 0 0 1 7 7.875z" fill="currentColor"/></svg>
                                                    </div>
                                                    The Upper Floor DK
                                                </li>
                                                <li class="d-flex align-items-start gap-2">
                                                    <div class="flex-shrink-0" style="margin-top: -.125rem; color: #cec7c5;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"><g clip-path="url(#A)"><path d="M7 .875c-1.211 0-2.396.359-3.403 1.032s-1.792 1.63-2.256 2.749S.756 7.007.993 8.195s.82 2.279 1.676 3.136 1.948 1.44 3.136 1.676 2.42.115 3.539-.349 2.076-1.249 2.749-2.256S13.125 8.211 13.125 7c0-1.624-.645-3.182-1.794-4.331S8.624.875 7 .875zm2.625 6.563H7c-.116 0-.227-.046-.309-.128S6.563 7.116 6.563 7V3.063c0-.116.046-.227.128-.309s.193-.128.309-.128.227.046.309.128.128.193.128.309v3.5h2.188c.116 0 .227.046.309.128s.128.193.128.309-.046.227-.128.309-.193.128-.309.128z" fill="currentColor"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h14v14H0z"/></clipPath></defs></svg>
                                                    </div>
                                                    fredag 09:00 til 20:00
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="hover-fade-item d-sm-block d-none">
                                            <div class="btn btn-lg btn-primary rounded-pill pe-none">
                                                Kob billet
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>


        <!-- How it works -->
        <section class="position-relative bg-primary" style="color: #cec7c5;">

            <!-- Illustration -->
            <div class="position-md-absolute start-0 top-0 bottom-0 w-md-50" style="position: relative; min-height: 428px;">
                <img src="assets/img/home/hiw/illustration.jpg" alt="Illustration" class="position-absolute w-100 h-100" style="object-fit: cover;">
            </div>

            <!-- Text -->
            <div class="container">
                <div class="row g-0">
                    <div class="col-md-6 offset-md-6 py-5 ps-md-5">
                        <div class="my-xxl-5 my-lg-4 my-3 ms-xxl-4 ms-xl-3 ms-lg-2">
                            <h2 class="h1 mb-md-4 mb-3 fw-semibold text-white">
                                How It Works
                            </h2>
                            <p class="mb-4 fs-xl">
                                Participating is simple and accessible. Just follow these steps to become part of the movement and help showcase the diversity of the human body.
                            </p>
                            <div class="d-flex flex-sm-row flex-column">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#modal-register-interest" class="btn btn-lg btn-light rounded-pill">
                                    Participate now
                                    <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/></svg>
                                </button>
                            </div>

                            <!-- Steps -->
                            <ul class="mt-md-5 mt-4 pt-xl-2 pt-md-0 pt-2 mb-0 list-unstyled gap-3">

                                <!-- Item -->
                                <li class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle fs-sm text-center text-white" style="width: 2.75rem; height: 2.75rem; border: 2px dashed #fff;">
                                        01
                                    </div>
                                    <div class="pt-2">
                                        <h3 class="h4 mb-2 pb-1 fw-semibold text-white">
                                            Register Your Interest
                                        </h3>
                                        <p>
                                            Fill out a quick form to express your interest in participating. We'll keep you updated about our upcoming photo sessions and let you know when we're in a city near you.
                                        </p>
                                    </div>
                                </li>

                                <!-- Item -->
                                <li class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle fs-sm text-center text-white" style="width: 2.75rem; height: 2.75rem; border: 2px dashed #fff;">
                                        02
                                    </div>
                                    <div class="pt-2">
                                        <h3 class="h4 mb-2 pb-1 fw-semibold text-white">
                                            Attend a Photo Session
                                        </h3>
                                        <p>
                                            Once registered, you can join one of our sessions where we take close-up photos of your vulva, penis, breasts, and buttocks.
                                        </p>
                                    </div>
                                </li>

                                <!-- Item -->
                                <li class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle fs-sm text-center text-white" style="width: 2.75rem; height: 2.75rem; border: 2px dashed #fff;">
                                        03
                                    </div>
                                    <div class="pt-2">
                                        <h3 class="h4 mb-2 pb-1 fw-semibold text-white">
                                            Complete the Form
                                        </h3>
                                        <p>
                                            This is an online form to be filled out prior to having your photo taken. You can fill it out beforehand at shape-of-us.eu or when you are at the photo session. You'll be asked a few simple questions about yourself, such as age range, country of residence, and any unique features you'd like to share. Completing this form is required prior to participating.
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Event -->
        <section class="py-5 bg-white">
            <div class="my-lg-5 my-md-4 my-3 py-xxl-3 container">
                <h2 class="h1 mb-md-4 mb-3 fw-semibold">
                    Host a Photo Event
                </h2>
                <div class="row align-items-end gy-4">
                    <div class="col-md-7 fs-xl lh-sm">
                        <p class="mb-0">
                            Do you know of a good location where we can set up and host a photo session? We're looking for spaces where people can feel comfortable and safe participating in this project. We bring all the necessary equipment and handle the session with full anonymity and professionalism.
                        </p>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex flex-sm-row flex-column justify-content-md-end">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-host-photo" class="btn btn-lg btn-primary rounded-pill">
                                I know a great space
                                <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                <div class="row row-cols-lg-5 row-cols-sm-3 row-cols-1 g-3 pt-4">

                    <!-- Card -->
                    <div class="col">
                        <div class="card border-0">
                            <div class="card-body fs-lg lh-sm text-center">
                                <p>
                                    You suggest a venue or location. Please provide contact details for the venue owner or manager if possible
                                </p>
                                <div class="mt-4 pt-2 ff-extra fs-base fw-medium text-dark-emphasis">
                                    01
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="col">
                        <img src="assets/img/home/photo-event/01.jpg" alt="Image" class="rounded">
                    </div>

                    <!-- Card -->
                    <div class="col">
                        <div class="card border-0">
                            <div class="card-body fs-lg lh-sm text-center">
                                <p>
                                    We set up a photo session, making it easy for participants to contribute
                                </p>
                                <div class="mt-4 pt-2 ff-extra fs-base fw-medium text-dark-emphasis">
                                    02
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="col">
                        <img src="assets/img/home/photo-event/02.jpg" alt="Image" class="rounded">
                    </div>

                    <!-- Card -->
                    <div class="col">
                        <div class="card border-0">
                            <div class="card-body fs-lg lh-sm text-center">
                                <p>
                                    Each participant is guided through the process in a private and respectful manner.
                                </p>
                                <div class="mt-4 pt-2 ff-extra fs-base fw-medium text-dark-emphasis">
                                    03
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Privacy -->
        <section class="pb-5 bg-white">
            <div class="mb-lg-5 mb-md-4 mb-3 pb-xxl-3 container">
                <h2 class="h1 mb-md-4 mb-3 fw-semibold">
                    Privacy and Respect
                </h2>
                <div class="row gy-3 gx-md-0 fs-xl lh-sm">
                    <div class="col-md">
                        <p class="mb-0">
                            Your privacy is important to us, and anonymity is at the core of this project. We are committed to ensuring that every participant remains completely anonymous and that no personally identifiable information is collected.
                        </p>
                    </div>
                    <div class="col-md offset-md-1">
                        <p class="mb-0">
                            We do not register any personal data that can identify you at a later time. No name, no email, or any other contact information is collected. All photos are anonymized, and pictures are taken without your face, only from the shoulders down.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>


    <!-- Page footer -->
    <footer class="footer bg-white">
        <div class="container py-4 fs-sm fw-medium text-body-secondary">
            &copy; 2025 - All rights reserved / ZANDORA aps / CVR-nr. 44316528 / Asylvej 15 / 9000 Aalborg / Denmark
        </div>
    </footer>


    <!-- Page modals -->


    <!-- Modal: Register Your Interest -->
    <div id="modal-register-interest" class="modal fade" tabindex="-1" aria-labelledby="modalInterestLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <form class="modal-content justify-content-sm-start justify-content-center p-sm-4">

                <!-- Modal header -->
                <div class="modal-header align-items-center justify-content-between border-0">
                    <h2 class="modal-title" id="modalInterestLabel">
                        Register Your Interest
                    </h2>
                    <button type="button" class="btn btn-lg btn-link py-sm-2 px-0 position-absolute top-0 end-0 z-3 m-sm-5 m-3" data-bs-dismiss="modal">
                        close
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body pt-0" style="flex: initial;">
                    <p class="mb-lg-5 mb-4 pb-lg-0 pb-md-2 fs-lg">
                        Want to be part of the Shape of Us project? Register your interest, and we'll notify you when we're hosting a photo session near you.
                    </p>

                    <!-- Email -->
                    <div class="mt-4 border-bottom" style="border-color: var(--zs-tertiary-color) !important;">
                        <input type="email" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="Email Address" required>
                        <p class="form-text fs-base text-body-secondary">
                            Your email will only be used to keep you informed about upcoming sessions. Once you participate, your email will not be linked to your photo, ensuring complete anonymity.
                        </p>
                    </div>

                    <!-- Country -->
                    <div class="mt-4 border-bottom" style="border-color: var(--zs-tertiary-color) !important;">
                        <select class="form-select form-select-lg bg-transparent rounded-0 px-0" name="country-at-birth[]" data-select='{
                "classNames": {
                  "containerInner": ["form-select", "form-select-lg", "bg-transparent", "border-0", "rounded-0", "px-0"]
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

                    <!-- Submit -->
                    <div class="mt-5 d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-lg btn-secondary rounded-pill" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-lg btn-primary rounded-pill">
                            Submit your interest
                            <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal: Host a Photo Session -->
    <div id="modal-host-photo" class="modal fade" tabindex="-1" aria-labelledby="modalPhotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <form class="modal-content justify-content-sm-start justify-content-center p-sm-4">

                <!-- Modal header -->
                <div class="modal-header align-items-center justify-content-between border-0">
                    <h2 class="modal-title" id="modalPhotoLabel">
                        Host a Photo Session
                    </h2>
                    <button type="button" class="btn btn-lg btn-link py-sm-2 px-0 position-absolute top-0 end-0 z-3 m-sm-5 m-3" data-bs-dismiss="modal">
                        close
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body pt-0" style="flex: initial;">
                    <p class="mb-lg-5 mb-4 pb-lg-0 pb-md-2 fs-lg">
                        Do you have know of a space where we can hold a Shape of Us photo session? Register the venue, and we'll get in touch to discuss the details.
                    </p>

                    <!-- Email -->
                    <div class="mt-4 border-bottom" style="border-color: var(--zs-tertiary-color) !important;">
                        <input type="email" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="Email Address" required>
                    </div>

                    <!-- Country -->
                    <div class="mt-4 border-bottom" style="border-color: var(--zs-tertiary-color) !important;">
                        <select class="form-select form-select-lg bg-transparent rounded-0 px-0" name="country-at-birth[]" data-select='{
                "classNames": {
                  "containerInner": ["form-select", "form-select-lg", "bg-transparent", "border-0", "rounded-0", "px-0"]
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

                    <!-- Contact information (socials, phone, etc.) -->
                    <div class="mt-4 border-bottom" style="border-color: var(--zs-tertiary-color) !important;">
                        <input type="text" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="Contact information (socials, phone, etc.)" required>
                    </div>

                    <!-- Tell us more — what should we know? -->
                    <div class="mt-4 border-bottom" style="border-color: var(--zs-tertiary-color) !important;">
                        <textarea rows="1" class="form-control form-control-lg bg-transparent border-0 rounded-0 px-0" placeholder="Tell us more — what should we know?"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="mt-5 d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-lg btn-secondary rounded-pill" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-lg btn-primary rounded-pill">
                            Submit your space
                            <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.62851 3.18224C5.3848 3.2831 5.1765 3.45404 5.02994 3.6733C4.88338 3.89264 4.80516 4.1505 4.80518 4.41424L4.80518 11.8622C4.80523 12.1259 4.88346 12.3836 5.02998 12.6028C5.17649 12.8221 5.3847 12.9929 5.6283 13.0938C5.8719 13.1947 6.13995 13.2211 6.39855 13.1697C6.65716 13.1183 6.89471 12.9913 7.08118 12.8049L10.8052 9.0809C11.0551 8.83084 11.1956 8.49177 11.1956 8.13824C11.1956 7.7847 11.0551 7.44564 10.8052 7.19557L7.08118 3.47157C6.89478 3.2851 6.65728 3.1581 6.39872 3.10664C6.14015 3.0551 5.87212 3.08144 5.62851 3.18224Z" fill="currentColor"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Vendor scripts -->
    <script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>


<?php include "src/html/html-scripts.php"; ?>
<?php include "src/html/html-end.php"; ?>