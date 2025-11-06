<?php
$page = ["name"=>"presentation", "translate"=>true, "include_js"=>"self"];
require_once "src/inc/init.php";

//log_page_load();
$captions = [
    [
        'text' => 'Anonymous portraits that redefine how we see beauty.',
        'display_time' => 4000
    ],
    [
        'text' => 'A visual archive of the human body, free from filters and shame.',
        'display_time' => 4000
    ],
    [
        'text' => 'Bodies without labels, beauty without limits.',
        'display_time' => 4000
    ],
    [
        'text' => 'This is what real looks like.',
        'display_time' => 4000
    ],
    [
        'text' => 'Step inside the labyrinth and be part of the gallery.',
        'display_time' => 7000
    ]
];

$image_rotate = range(1, 12);
shuffle($image_rotate);


// Localize captions for current language and expose as JSON for JS
$captions_i18n = array_map(function($c){
    return [
        'text' => $c['text'],
        'display_time' => (int)($c['display_time'] ?? 3000)
    ];
}, $captions);


//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body data-name="<?= $page["name"] ?>" class="presentation-tv">

    <!-- Page content -->
    <main class="content-wrapper d-flex flex-column min-vh-100">

        <div class="logo-spacer"></div>

        <!-- Logo on top -->
        <section class="section-logo">
            <div class="container text-center">
                <a href="<?= get_url("app") ?>">
                    <img src="assets/img/logo_2000px.png" alt="Shape of Us" class="img-fluid" style="max-width: 420px; width: 100%; height: auto;" />
                </a>
            </div>
        </section>

        <div class="logo-spacer"></div>

        <!-- 4x3 Grid of photos -->
        <section class="section-grid">
            <div class="container-fluid">
                <div class="tv-grid">
                    <?php
                    $sql = "
                        SELECT *
                        FROM (
                            SELECT `entry_id`, `breast` AS `filename`, 'breast' AS `type`
                            FROM `sou_form_images`
                            WHERE `breast` IS NOT NULL AND `breast` <> ''

                            UNION ALL

                            SELECT `entry_id`, `genital` AS `filename`, 'genitals' AS `type`
                            FROM `sou_form_images`
                            WHERE `genital` IS NOT NULL AND `genital` <> ''

                            UNION ALL

                            SELECT `entry_id`, `buttocks` AS `filename`, 'buttocks' AS `type`
                            FROM `sou_form_images`
                            WHERE `buttocks` IS NOT NULL AND `buttocks` <> ''
                        ) AS all_imgs
                        ORDER BY RAND()
                        LIMIT 12;
                    ";

                    // Fetch rows as array via database.php helper
                    $rows = pdo_get_array($pdo, $sql, []);

                    $images = [];
                    foreach ($rows as $row) {
                        if (!empty($row['filename'])) {
                            $images[] = 'https://shapeofus.eu/files/' . ltrim($row['filename'], '/');
                        }
                    }

                    $i = 0;
                    foreach ($images as $img) {
                        $i++;
                        $loading = ($i <= 4) ? 'eager' : 'lazy';

                        // Normalize to web URL
                        if (preg_match('~^https?://~i', $img)) {
                            $webPath = $img;
                        } else {
                            // Build web path relative to current script dir (dev)
                            $webPath = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $img);
                            $webPath = str_replace('\\\\', '/', $webPath);
                            $webPath = str_replace('\\', '/', $webPath);
                        }
                        ?>
                        <div class="col">
                            <div class="ratio ratio-16x9 overflow-hidden rounded" style="background-color: #f5f5f5;">
                                <img src="<?= htmlspecialchars($webPath) ?>" alt="Gallery image" class="w-100 h-100" style="object-fit: cover; object-position: center;" loading="<?= $loading ?>" decoding="async" sizes="(min-width: 992px) 25vw, 50vw">
                            </div>
                        </div>
                        <?php
                    }

                    // If fewer than 12 images, render placeholders to keep grid consistent
                    for ($i = count($images); $i < 12; $i++) { ?>
                        <div class="col">
                            <div class="ratio ratio-16x9 rounded d-flex align-items-center justify-content-center" style="background:#f1f3f5; color:#adb5bd;">
                                <span class="small"><?= __("Coming soon") ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <div class="logo-spacer"></div>

        <!-- One-line text about the project -->
        <section class="section-caption">
            <div class="container text-center">
                <div id="caption-rotator" class="rotator lead mb-0 text-primary fs-1" aria-live="polite">
                    <span class="msg is-active"></span>
                    <span class="msg"></span>
                </div>
                <script id="captions-data" type="application/json">
                    <?= json_encode($captions_i18n, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
                </script>
            </div>
        </section>

        <div class="logo-spacer"></div>

    </main>

<?php include "src/html/html-scripts.php"; ?>
<?php include "src/html/html-end.php"; ?>