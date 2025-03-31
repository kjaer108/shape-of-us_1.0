<?php
?>

    <!-- Vendor scripts -->
<?php if (isset($page["vendor_js"]) && strpos($page["vendor_js"], "choices") !== false) { ?>
    <script src="assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>
<?php } ?>
<?php if (isset($page["vendor_js"]) && strpos($page["vendor_js"], "openseadragon") !== false) { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openseadragon/4.0.0/openseadragon.min.js"></script>
<?php } ?>

    <!-- Bootstrap + Site scripts -->
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Zandora JS -->
<?php if (isset($page["vendor_js"]) && strpos($page["vendor_js"], "app") !== false) { ?>
    <script src="assets/js/app.js"></script>
<?php } ?>
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