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

if (! $params['configoption5']) {
    if ($handle_actions) {
        $api ->call("domain/getroute/domain/" . $domain . "/");
        $routes = $api->getResponse();
        // Both actions rewrite the full route list. call() treats any HTTP 200
        // as success, even when the body isn't JSON, so check the list too.
        if (!$api->isSuccess() || !isset($routes['result']) || !is_array($routes['result'])) {
            $vars['_status'] = array('code' => 0,'msg' => $api->isSuccess() ? 'Resource not found!' : $api->error());
            return;
        }
        switch (isset($_POST['_action']) ? $_POST['_action'] : '') {
            case 'deleteroute':
                $found = false;
                if (!empty($_POST['routes']['old_list']) && is_array($_POST['routes']['old_list'])) {
                    if (count($_POST['routes']['old_list']) === count($routes['result'])) {
                        $vars['_status'] = array('code' => 0,'msg' => $vars['lang']['remove_info']);
                        break;
                    }
                    foreach ($routes['result'] as $key => $val) {
                        if (in_array($val, $_POST['routes']['old_list'], true)) {
                            unset($routes['result'][$key]);
                            $found = true;
                        }
                    }
                } elseif (isset($_POST['routetodel'])) {
                    foreach ($routes['result'] as $key => $val) {
                        if ($val === $_POST['routetodel'] . '::' . $_POST['routeporttodel']) {
                            $found = true;
                            unset($routes['result'][$key]);
                            break;
                        }
                    }
                }


                if ($found) {
                    $api ->call(
                        "domain/edit/domain/" . $domain . "/destinations/"
                        . rawurlencode(str_replace('::', ':', json_encode($routes['result']))) . "/"
                    );

                    if ($api->isSuccess()) {
                        $vars['_status'] = array('code' => 1,'msg' => $vars['lang']['route_removed']);
                    } else {
                        $vars['_status'] = array('code' => 0,'msg' => $api->error());
                    }
                } else {
                    $vars['_status'] = array('code' => 0,'msg' => 'Resource not found!');
                }


                break;
            case 'addroute':
                if (isset($_POST['route']['hostname']) && isset($_POST['route']['port'])) {
                    $hostname = $_POST['route']['hostname'];
                    $port = $_POST['route']['port'];
                    $validHost = kwspamexperts_isHostname($hostname)
                        || filter_var($hostname, FILTER_VALIDATE_IP) !== false;
                    if ($port !== '') {
                        $port = filter_var(
                            $port,
                            FILTER_VALIDATE_INT,
                            array('options' => array('min_range' => 1, 'max_range' => 65535))
                        );
                    }
                    if (!$validHost || $port === false) {
                        $vars['_status'] = array('code' => 0,'msg' => $vars['lang']['invalid_route']);
                        break;
                    }

                    foreach ($routes['result'] as $key => $val) {
                        $routes['result'][$key] = str_replace('::', ':', $val);
                    }

                    $routes['result'][] = $port === '' ? $hostname : $hostname . ":" . $port;
                    $api ->call(
                        "domain/edit/domain/" . $domain . "/destinations/"
                        . rawurlencode(json_encode($routes['result'])) . "/"
                    );
                    if ($api->isSuccess()) {
                        $vars['_status'] = array('code' => 1,'msg' => $vars['lang']['route_added']);
                    } else {
                        $vars['_status'] = array('code' => 0,'msg' => $api->error());
                    }
                }
                break;
        }

        return;
    }

    $vars['domainroutes'] = array();
    $api ->call("domain/getroute/domain/" . $domain . "/");
    if ($api->isSuccess()) {
        $data = $api->getResponse();

        foreach ($data['result'] as $key => $val) {
            $vars['domainroutes'][] = explode(':', $val);
        }
    } else {
        $vars['_status'] = array('code' => 0,'msg' => $api->error());
    }
} else {
    $vars['_status'] = array('code' => 2,'msg' => $vars['lang']['route_denied']);
}
