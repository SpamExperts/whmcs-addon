<?php

/**
 * Management page, loaded by spamexpertsreseller_page() and run in its scope.
 *
 * @var array $params Module parameters from WHMCS
 * @var spamexperts_api $api
 * @var array $vars Template variables, returned by the page function
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

$reseller_id = 0;

$api->call('/reseller/list/');

if ($api->isSuccess()) {
    $resellers = $api->getResponse();

    if (isset($resellers['result'][0])) {
        foreach ($resellers['result'][0] as $val) {
            if ($val['username'] == $params['username']) {
                $reseller_id = $val['id'];
                break;
            }
        }
    }
}

if ($reseller_id) {
    $api->call('/reseller/getbandwidthusage/reseller_id/' . $reseller_id);
    if ($api->isSuccess()) {
        $result = $api->getResponse();
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.CallbackFunctions.WarnFringestuff
        $lines  = array_filter(explode("\n", $result['result']));
        $data   = array();

        foreach ($lines as $val) {
            $data[]      = explode(',', $val);
        }

        $vars['data']    = $data;
    } else {
        $vars['_status'] = array('code' => 0,'msg' => $api->error());
    }
} else {
    $vars['_status'] = array('code' => 0,'msg' => "No reseller found");
}
