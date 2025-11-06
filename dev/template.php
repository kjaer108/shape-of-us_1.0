<?php
$page = ["name"=>"frontpage", "translate"=>true];
require_once "src/inc/init.php";

//log_page_load();

//*** HERE WE GO! Let's render the page ***************************************?>
<?php include "src/html/html-begin.php"; ?>

<!-- Body -->
<body data-name="<?= $page["name"] ?>">

<?php include "src/html/html-scripts.php"; ?>
<?php include "src/html/html-end.php"; ?>