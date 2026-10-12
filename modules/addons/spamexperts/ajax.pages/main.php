<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

if (isset($_POST['action']) && $_POST['action'] === 'testconnection') {
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(ROOTDIR . DS . 'modules' . DS . 'servers' . DS . 'kwspamexperts' . DS . 'class.connection.php');

    $api = new kwspamexperts_api(
        array(
                'configoption2' => $_POST['url'],
                'configoption3' => $_POST['user'],
                'configoption4' => $_POST['password']
                )
    );

    $api->call('/version/get/');

    if ($api->isSuccess()) {
        die('success');
    } else {
        die(htmlentities($api->error(), ENT_QUOTES, 'UTF-8'));
    }
}
