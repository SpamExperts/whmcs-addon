<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

if (isset($_POST['action']) && $_POST['action'] === 'savechanges') {
    // The products use this URL too, so not one that sends the credentials unencrypted
    if (isset($_POST['conf']['url']) && preg_match('#^\s*http://#i', (string) $_POST['conf']['url'])) {
        addError('The API URL must use https://, so the API credentials are sent encrypted.');
    } elseif (saveWHMCSconfig('kwspamexperts_api', serialize($_POST['conf']))) {
        updateProductsConfig(serialize($_POST['conf']));
    }
}

$data = getApiSettings();
