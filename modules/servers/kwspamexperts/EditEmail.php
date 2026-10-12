<?php

/**
 * Management page, loaded by kwspamexperts_page() and run in its scope.
 *
 * @var array $params Module parameters from WHMCS
 * @var kwspamexperts_api $api
 * @var string $domain Domain of the service
 * @var bool $handle_actions Run the posted action instead of loading the page data
 * @var array $vars Template variables, returned by the page function
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

if (! $params['configoption6']) {
    if ($handle_actions) {
        // Each contact is updated on its own, so report each result
        $contacts = array(
            'primaryemail' => array('domaincontact', 'primarycontact'),
            'adminemail' => array('domainadmincontact', 'admincontact'),
        );
        $statuses = array();
        foreach ($contacts as $field => $contact) {
            list($endpoint, $label) = $contact;
            if (!isset($_POST[$field])) {
                continue;
            }
            if (!kwspamexperts_isEmail($_POST[$field])) {
                $status = array('code' => 0,'msg' => $vars['lang']['invalid_email']);
            } else {
                $api->call($endpoint . '/set/domain/' . $domain . '/email/' . rawurlencode($_POST[$field]) . '/');
                if ($api->isSuccess()) {
                    $status = array('code' => 1,'msg' => $vars['lang']['email_changed']);
                } else {
                    $status = array('code' => 0,'msg' => $api->error());
                }
            }
            $status['msg'] = $vars['lang'][$label] . ' ' . $status['msg'];
            $statuses[] = $status;
        }
        if ($statuses) {
            $codes = array_column($statuses, 'code');
            $vars['_status'] = array(
                'code' => min($codes),
                'msg' => implode(' ', array_column($statuses, 'msg')),
                'list' => $statuses,
            );
        }

        return;
    }

    $api ->call("domaincontact/get/domain/$domain/");
    if ($api->isSuccess()) {
        $vars['primarycontact'] = $api->getResponse()['result'];
    } else {
        $vars['_status'] = array('code' => 0,'msg' => $api->error());
    }

    $api ->call("domainadmincontact/get/domain/$domain/");
    if ($api->isSuccess()) {
        $vars['admincontact'] = $api->getResponse()['result'];
    } else {
        $vars['_status'] = array('code' => 0,'msg' => $api->error());
    }
} else {
    $vars['_status'] = array('code' => 2,'msg' => $vars['lang']['email_denied']);
}
