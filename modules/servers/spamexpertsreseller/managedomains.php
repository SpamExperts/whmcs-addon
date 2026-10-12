<?php

/**
 * Management page, loaded by spamexpertsreseller_page() and run in its scope.
 *
 * @var array $params Module parameters from WHMCS
 * @var bool $handle_actions Run the posted action instead of loading the page data
 * @var array $vars Template variables, returned by the page function
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

$api = new spamexperts_api($params, true);

if ($handle_actions) {
    switch ($_POST['_action'] ?? '') {
        case 'unbind':
            $unbindDomain = $_POST['unbind-domain'] ?? '';
            if (!spamexpertsreseller_isHostname($unbindDomain)) {
                $vars['_status'] = ['code' => 0, 'msg' => $vars['lang']['invalid_domain']];
                break;
            }
            $api->call('/domain/remove/domain/' . rawurlencode($unbindDomain) . '/');

            if ($api->isSuccess()) {
                $vars['_status']  = ['code' => 1, 'msg' => $vars['lang']['domain_unbinded']];
            } else {
                $vars['_status']  = ['code' => 0, 'msg' => $api->error()];
            }
            break;
        case 'bind':
            $bindDomain = $_POST['bind']['domain'] ?? '';
            if (!spamexpertsreseller_isHostname($bindDomain)) {
                $vars['_status'] = ['code' => 0, 'msg' => $vars['lang']['invalid_domain']];
                break;
            }
            $api->call('/domain/add/domain/' . rawurlencode($bindDomain) . '/');
            if ($api->isSuccess()) {
                $vars['_status']  = ['code' => 1, 'msg' => $vars['lang']['domain_binded']];
            } else {
                $vars['_status']  = ['code' => 0, 'msg' => $api->error()];
            }

            break;
    }

    return;
}

$api->call('/domainslist/get/');
if ($api->isSuccess()) {
    $list                   = $api->getResponse();
    $vars['domains_list']   = $list['result'];
} else {
    $vars['_status']        = ['code' => 0, 'msg' => $api->error()];
}
