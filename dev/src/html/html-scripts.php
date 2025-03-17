<?php
?>

<!-- Vendor scripts -->
<script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- Bootstrap + Site scripts -->
<script src="assets/js/bootstrap.min.js"></script>

<!-- Zandora JS -->
<script src="assets/js/shapeofus.js" data-cookie-consent="strictly-necessary"></script>
<?php
if (!empty($page["include_js"])) {
    $currentFile = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME) . ".js";

    foreach (explode(';', $page["include_js"]) as $part) {
        $filename = trim(strtok($part, '(')); // Get filename before '('
        $filename = ($filename === "self") ? $currentFile : $filename; // Replace "self" with current file

        echo '<script src="assets/js/' . $filename . '"></script>' . PHP_EOL;
    }
}
?>