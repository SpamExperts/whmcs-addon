<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
include_once(ROOTDIR . DS . 'modules' . DS . 'servers' . DS . 'kwspamexperts' . DS . 'class.connection.php');
global $CONFIG;
$addon = (array)Capsule::table('tbladdonmodules')
    ->select('value as version')
    ->where('module', 'spamexperts')
    ->where('setting', 'version')
    ->first();
$data  = getApiSettings();
$curl  = curl_version();

$api   = new kwspamexperts_api(
    [
            'configoption2' => $data['url'],
            'configoption3' => $data['user'],
            'configoption4' => $data['password']
            ]
);
$api->call('/version/get/');

$spam = $api->getResponse();
