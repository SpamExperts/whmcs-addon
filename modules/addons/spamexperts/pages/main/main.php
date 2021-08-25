<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

if (isset($_POST['action']) && $_POST['action'] === 'savechanges') {
    if (saveWHMCSconfig('kwspamexperts_api', serialize($_POST['conf']))) {
        updateProductsConfig(serialize($_POST['conf']));
    }
}

$api = getWHMCSconfig('kwspamexperts_api');
$data = unserialize($api);
