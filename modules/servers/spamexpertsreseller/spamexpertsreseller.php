<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved
 * (2014-02-11, 11:40:58)
 *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

use Carbon\Carbon;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
* FUNCTION spamexpertsreseller_ConfigOptions
* Display Configuration fields
* @return array
*/
function spamexpertsreseller_ConfigOptions()
{
    $configarray = array(
        "SpampanelURL"     => array(
            "Type"         => "text",
            "Size"         => "25",
            "FriendlyName" => "Spam Panel URL",
            "Description"  => ""
        ),
        "APIUsername"      => array(
            "Type"         => "text",
            "Size"         => "25",
            "FriendlyName" => "API Username",
            "Description"  => ""
        ),
        "APIPassword"      => array(
            "Type"         => "password",
            "Size"         => "25",
            "FriendlyName" => "API Password",
            "Description"  => ""
        ),
        "DomainsLimit"     => array(
            "Type"         => "text",
            "Size"         => "25",
            "FriendlyName" => "Domains Limits",
            "Description"  => ""
        ),
        "UsageLimits" => array(
            "Type"         => "yesno",
            "Size"         => "25",
            "FriendlyName" => "Usage Limits",
            "Description"  => "Tick to disallow access to the API for the reseller"
        ),
    );
    return $configarray;
}


/**
* FUNCTION spamexpertsreseller_configError
* Message for the admin when the product has no API settings
* @param array $params
* @return string|null
*/
function spamexpertsreseller_configError($params)
{
    if (empty($params['configoption1']) || empty($params['configoption2']) || empty($params['configoption3'])) {
        return "Set the Spam Panel URL, API Username and API Password in the product's Module Settings.";
    }

    return null;
}


/**
* FUNCTION spamexpertsreseller_isHostname
* Check a client supplied domain or hostname before it goes into an API path.
* IDNs are converted to punycode first. WHMCS bundles
* symfony/polyfill-intl-idn, so idn_to_ascii exists even without intl.
* @param mixed $value
* @return bool
*/
function spamexpertsreseller_isHostname($value)
{
    if (!is_string($value) || $value === '') {
        return false;
    }
    $ascii = idn_to_ascii($value, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    return $ascii !== false
        && filter_var($ascii, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
}


/**
* FUNCTION spamexpertsreseller_CreateAccount
* Create Reseller Account
* @param array $params
* @return string
*/
function spamexpertsreseller_CreateAccount($params)
{
    if ($error = spamexpertsreseller_configError($params)) {
        return $error;
    }
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    $api        = new spamexperts_api($params);
    $password   = !empty($params['password']) ? $params["password"] : createServerPassword();
    $email      = !empty($params["customfields"]["Email"])
        ? $params["customfields"]["Email"]
        : $params['clientsdetails']['email'];
    $username   = !empty($params['username']) ? $params['username'] : uniqid();

    // for add, the optional value needed for api_usage is the inverse of the config option to disable api access
    //  - "1" passed as the value enables the API access
    //  - "0" passed as the value or nothing passed disables the API access
    $api_usage = ($params['configoption5'] === 'on' ? 0 : 1);
    $api->call(
        '/reseller/add/username/' . $username . '/password/' . rawurlencode($password)
        . '/email/' . rawurlencode($email) . '/domainslimit/' . $params['configoption4']
        . '/api_usage/' . $api_usage
    );
    if ($api->isSuccess()) {
        // update password & username
        Capsule::table('tblhosting')
            ->where(["id" => $params['serviceid']])
            ->update([
                // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.CryptoFunctions.WarnCryptoFunc
                'password' => encrypt($password),
                'username' => $username,
                'updated_at' => Carbon::now()
            ]);
        return "success";
    }
    return $api->error();
}


/**
* FUNCTION spamexpertsreseller_TerminateAccount
* Terminate Reseller Account
* @param array $params
* @return string
*/
function spamexpertsreseller_TerminateAccount($params)
{
    if ($error = spamexpertsreseller_configError($params)) {
        return $error;
    }
    if (empty($params['username'])) {
        return "Username cannot be empty. Please enter the reseller username in the Username field.";
    }
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    $api = new spamexperts_api($params);
    $api ->call("reseller/remove/username/" . $params['username'] . "/");

    if ($api->isSuccess()) {
        return "success";
    }

    return $api->error();
}


/**
* FUNCTION spamexpertsreseller_ChangePackage
* Upgrade Reseller Account
* @param array $params
* @return string
*/
function spamexpertsreseller_ChangePackage($params)
{
    if ($error = spamexpertsreseller_configError($params)) {
        return $error;
    }
    if (empty($params['username'])) {
        return "Username cannot be empty. Please enter the reseller username in the Username field.";
    }
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    $api = new spamexperts_api($params);
    // for update, the optional value needed for api_usage matches the config option to disable api access
    //  - "0" passed as the value enables the API access
    //  - "1" passed as the value disables the API access
    $api_usage = ($params['configoption5'] === 'on' ? 1 : 0);
    $api ->call(
        "reseller/update/username/" . $params['username'] . "/password/" . rawurlencode($params['password'])
        . "/domainslimit/" . $params['configoption4'] . "/api_usage/" . $api_usage . "/"
    );

    if ($api->isSuccess()) {
        return "success";
    }

    return $api->error();
}


/**
* FUNCTION spamexpertsreseller_ClientArea
* Display extended pages in clientarea
* @param array $params
* @return array
*/
function spamexpertsreseller_ClientArea($params)
{
    $output = array('vars' => array());
    if (function_exists('generate_token')) {
        $output['vars']['token'] = generate_token('plain');
    }
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    $lang = spamexpertsreseller_getLang($params);

    $error = spamexpertsreseller_setupError($params, $lang);
    if ($error) {
        $output['vars']['api_error'] = $error;
    } else {
        $api = new spamexperts_api($params);
        $api->call("authticket/create/username/" . $params['username'] . "/");

        $url = (strpos($params['configoption1'], 'http') === false)
            ? 'https://' . $params['configoption1']
            : $params['configoption1'];

        $res = $api->getResponse();
        if ($api->isSuccess()) {
            $output['vars']['api_url'] = $url . '/?authticket=' . urlencode($res['result']);
        } elseif (isset($res['messages']['error'][0])) {
            $output['vars']['api_error'] = sprintf($lang['mainsite']['notadded'], $params['username']);
        } else {
            $output['vars']['api_error'] = $lang['mainsite']['unavailable'];
        }
    }
    $output['vars']['lang'] = $lang['mainsite'];

    return $output;
}



if (!function_exists('spamexpertsreseller_getLang')) {
    /**
    * FUNCTION spamexpertsreseller_getLang
    * Get user language
    * @param array $params
    * @return array
    */
    function spamexpertsreseller_getLang($params)
    {
        global $CONFIG;
        if (!empty($_SESSION['Language'])) {
            $language = strtolower($_SESSION['Language']);
        } elseif (
            isset($params['clientsdetails']['language'])
            && strtolower($params['clientsdetails']['language']) != ''
        ) {
            $language = strtolower($params['clientsdetails']['language']);
        } else {
            $language = $CONFIG['Language'];
        }

        $available_languages = array('english');
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        $langfilename = __DIR__ . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language . '.php';
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        if (in_array($language, $available_languages, true) && file_exists($langfilename)) {
            // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
            require($langfilename);
        } else {
            // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
            require(__DIR__ . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'english.php');
        }

        if (isset($lang)) {
            return $lang;
        }
    }
}


/**
* FUNCTION spamexpertsreseller_ClientAreaAllowedFunctions()
* Allow the management pages to be called from the clientarea without
* adding a button for them
* @return array
*/
function spamexpertsreseller_ClientAreaAllowedFunctions()
{
    $buttonarray = array(
        'Management' => 'management'
    );
    return $buttonarray;
}


if (!function_exists('spamexpertsreseller_page')) {
    /**
    * FUNCTION spamexpertsreseller_page
    * Run a management page script and collect its template vars
    * @param array $params
    * @param string $page
    * @param bool $handle_actions Run the posted action instead of loading the page data
    * @return array|null Null when the page does not exist
    */
    function spamexpertsreseller_page($params, $page, $handle_actions)
    {
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
        $lang              = spamexpertsreseller_getLang($params);
        $api               = new spamexperts_api($params);
        $vars['main_lang'] = $lang['mainsite'];
        $vars['lang']      = $lang[(!isset($lang[$page]) ? 'mainsite' : $page)];
        $vars['serviceid'] = $params['serviceid'];

        if (function_exists('generate_token')) {
            $vars['token'] = generate_token('plain');
        }

        $available_pages = array('managedomains', 'stats');
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        if (
            empty($page) || !in_array($page, $available_pages, true)
            || !file_exists(__DIR__ . DIRECTORY_SEPARATOR . $page . '.php')
            || !file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $page . '.tpl')
        ) {
            return null;
        }
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem

        $serviceid = $params['serviceid'];
        if (!$handle_actions && isset($_SESSION['spamexpertsreseller_result'][$serviceid])) {
            $vars['_result'] = $_SESSION['spamexpertsreseller_result'][$serviceid];
            unset($_SESSION['spamexpertsreseller_result'][$serviceid]);
        }

        $error = spamexpertsreseller_setupError($params, $lang);
        if ($error) {
            $vars['_status'] = array('code' => 2, 'msg' => $error);
            return $vars;
        }

        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require_once(__DIR__ . DIRECTORY_SEPARATOR . $page . '.php');

        // The same API error can come from the action and from loading the page
        if (
            isset($vars['_result'], $vars['_status'])
            && $vars['_result']['msg'] === $vars['_status']['msg']
        ) {
            unset($vars['_result']);
        }

        return $vars;
    }
}

/**
* FUNCTION spamexpertsreseller_setupError
* Message for a product without API settings or a service without a username
* @param array $params
* @param array $lang
* @return string|null
*/
function spamexpertsreseller_setupError($params, $lang)
{
    if (spamexpertsreseller_configError($params)) {
        return $lang['mainsite']['notconfigured'];
    }

    if (empty($params['username'])) {
        return $lang['mainsite']['notsetup'];
    }

    return null;
}

if (!function_exists('spamexpertsreseller_management')) {
    /**
    * FUNCTION spamexpertsreseller_management
    * Display extended pages in clientarea. A posted action is run first, then
    * redirected to the page so the result is shown by a GET request.
    * @param array $params
    * @return array
    */
    function spamexpertsreseller_management($params)
    {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $vars = spamexpertsreseller_page($params, $page, false);
            if ($vars === null) {
                $lang = spamexpertsreseller_getLang($params);
                return array('vars' => array(
                    '_status' => array('code' => 1, 'msg' => $lang['mainsite']['notfound'])
                ));
            }

            return array(
                'templatefile' => 'templates/' . $page,
                'breadcrumb'   => ' > <a href="#">Server Details</a>',
                'vars'         => $vars
            );
        }

        $url  = 'clientarea.php?action=productdetails&id=' . $params['serviceid'];
        $vars = spamexpertsreseller_page($params, $page, true);
        if ($vars !== null) {
            // Code 2 marks the page as unavailable, which the GET shows again itself
            if (isset($vars['_status']) && $vars['_status']['code'] !== 2) {
                $_SESSION['spamexpertsreseller_result'][$params['serviceid']] = $vars['_status'];
            }
            $url .= '&modop=custom&a=management&page=' . urlencode($page)
                 . '&token=' . urlencode(isset($vars['token']) ? $vars['token'] : '');
        }

        if (ob_get_length()) {
            ob_clean();
        }
        header('Location: ' . $url);
        exit;
    }
}
