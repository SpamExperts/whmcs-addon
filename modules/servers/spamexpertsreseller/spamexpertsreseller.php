<?php

use Carbon\Carbon;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
* FUNCTION spamexpertsreseller_MetaData
* Module name shown in the WHMCS admin area
* @return array
*/
function spamexpertsreseller_MetaData()
{
    return [
        'DisplayName' => 'SpamExperts Reseller',
    ];
}

/**
* FUNCTION spamexpertsreseller_ConfigOptions
* Display Configuration fields
* @return array
*/
function spamexpertsreseller_ConfigOptions()
{
    $configarray = [
        "SpampanelURL"     => [
            "Type"         => "text",
            "Size"         => "25",
            "FriendlyName" => "Spam Panel URL",
            "Description"  => ""
        ],
        "APIUsername"      => [
            "Type"         => "text",
            "Size"         => "25",
            "FriendlyName" => "API Username",
            "Description"  => ""
        ],
        "APIPassword"      => [
            "Type"         => "password",
            "Size"         => "25",
            "FriendlyName" => "API Password",
            "Description"  => ""
        ],
        "DomainsLimit"     => [
            "Type"         => "text",
            "Size"         => "25",
            "FriendlyName" => "Domains Limits",
            "Description"  => "Whole number. 0 for unlimited, if the API account has no domains limit"
        ],
        "UsageLimits" => [
            "Type"         => "yesno",
            "Size"         => "25",
            "FriendlyName" => "Usage Limits",
            "Description"  => "Tick to disallow access to the API for the reseller"
        ],
    ];
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
    if (preg_match('#^\s*http://#i', $params['configoption1'])) {
        return "Set the Spam Panel URL to an https:// address in the product's Module Settings.";
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
* FUNCTION spamexpertsreseller_isEmail
* Check a client supplied e-mail address before it goes into an API path. The
* API splits its path on slashes, even encoded ones, so those are rejected too.
* @param mixed $value
* @return bool
*/
function spamexpertsreseller_isEmail($value)
{
    return is_string($value) && !str_contains($value, '/')
        && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

/**
* FUNCTION spamexpertsreseller_isDomainsLimit
* Check the product's domains limit before it goes into an API path. The API
* drops an empty path segment, so an empty limit would take the next name.
* @param mixed $value
* @return bool
*/
function spamexpertsreseller_isDomainsLimit($value)
{
    return is_string($value) && ctype_digit($value);
}

/**
* FUNCTION spamexpertsreseller_panelUsername
* The username as the panel stores it. The API strips backslashes and tags from
* every username it is given and trims it, but keeps the case for resellers.
* @param string $username
* @return string
*/
function spamexpertsreseller_panelUsername($username)
{
    return strip_tags(preg_replace('/(^\s+)|(\s+$)/us', '', stripslashes($username)));
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

    if (!spamexpertsreseller_isEmail($email)) {
        return "Email is empty or invalid. Please enter a valid e-mail address in the Email field.";
    }
    if (!spamexpertsreseller_isDomainsLimit($params['configoption4'])) {
        return "Set the Domains Limits to a whole number in the product's Module Settings.";
    }

    // for add, the optional value needed for api_usage is the inverse of the config option to disable api access
    //  - "1" passed as the value enables the API access
    //  - "0" passed as the value or nothing passed disables the API access
    $api_usage = ($params['configoption5'] === 'on' ? 0 : 1);
    $api->call(
        '/reseller/add/username/' . rawurlencode($username) . '/password/' . rawurlencode($password)
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
                // As the panel stores it, so later lookups in its reseller list match
                'username' => spamexpertsreseller_panelUsername($username),
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
    $api ->call("reseller/remove/username/" . rawurlencode($params['username']) . "/");

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
    // The API drops an empty path segment, so an empty password would take the next name
    if (empty($params['password'])) {
        return "Password cannot be empty. Please enter the reseller password in the Password field.";
    }
    if (!spamexpertsreseller_isDomainsLimit($params['configoption4'])) {
        return "Set the Domains Limits to a whole number in the product's Module Settings.";
    }
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    $api = new spamexperts_api($params);
    // for update, the optional value needed for api_usage matches the config option to disable api access
    //  - "0" passed as the value enables the API access
    //  - "1" passed as the value disables the API access
    $api_usage = ($params['configoption5'] === 'on' ? 1 : 0);
    $api ->call(
        "reseller/update/username/" . rawurlencode($params['username'])
        . "/password/" . rawurlencode($params['password'])
        . "/domainslimit/" . $params['configoption4'] . "/api_usage/" . $api_usage . "/"
    );

    if ($api->isSuccess()) {
        return "success";
    }

    return $api->error();
}


/**
* FUNCTION spamexpertsreseller_redirect
* Send the client to a URL instead of the page
* @param string $url
* @return never
*/
function spamexpertsreseller_redirect($url)
{
    if (ob_get_length()) {
        ob_clean();
    }
    header('Location: ' . $url);
    exit;
}

/**
* FUNCTION spamexpertsreseller_ClientArea
* Display extended pages in clientarea
* @param array $params
* @return array
*/
function spamexpertsreseller_ClientArea($params)
{
    $overview = 'clientarea.php?action=productdetails&id=' . $params['serviceid'];

    // The panel has the reseller only from create until terminate, as suspending doesn't touch it
    if (!in_array($params['status'], ['Active', 'Suspended'], true)) {
        if (isset($_GET['manage'])) {
            spamexpertsreseller_redirect($overview);
        }
        spamexpertsreseller_shownPages(false);
        return ['vars' => ['api_error' => null]];
    }

    // The management pages are plain links to the product details, so they
    // need no token in the URL, unlike a modop=custom function in WHMCS 9.
    // The login link makes its ticket only when followed, as one expires.
    if (isset($_GET['manage']) && $_GET['manage'] === 'login') {
        // The overview shows why when the panel cannot be reached
        $url = spamexpertsreseller_loginUrl($params);
        spamexpertsreseller_redirect($url !== null ? $url : $overview);
    }

    // Without a working panel, the sidebar has no management links and the overview shows why
    $lang  = spamexpertsreseller_getLang($params);
    $error = spamexpertsreseller_apiError($params, $lang);
    $pages = $error ? [] : spamexpertsreseller_managePages($params, $lang);
    if (isset($_GET['manage'])) {
        // Only the pages the sidebar links, so not a disabled one either
        if (is_string($_GET['manage']) && isset($pages[$_GET['manage']])) {
            $output = spamexpertsreseller_management($params, $_GET['manage']);
            if ($output !== null) {
                spamexpertsreseller_shownPages($pages);
                return $output;
            }
        }
        unset($_SESSION['spamexpertsreseller_result'][$params['serviceid']]);
        spamexpertsreseller_redirect($overview);
    }

    spamexpertsreseller_shownPages($error ? false : $pages);

    return ['vars' => ['api_error' => $error]];
}

/**
* FUNCTION spamexpertsreseller_loginUrl
* Panel URL with a new login ticket, made only when the client follows the
* login link rather than on every page that shows it
* @param array $params
* @return string|null Null when the panel cannot be reached
*/
function spamexpertsreseller_loginUrl($params)
{
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    if (spamexpertsreseller_setupError($params, spamexpertsreseller_getLang($params))) {
        return null;
    }

    $api = new spamexperts_api($params);
    $api->call("authticket/create/username/" . rawurlencode($params['username']) . "/");
    $res = $api->getResponse();
    if (!$api->isSuccess()) {
        return null;
    }

    // The API refuses http://, so a URL without https:// lacks a scheme
    $url = $params['configoption1'];
    if (!preg_match('#^https://#i', $url)) {
        $url = 'https://' . $url;
    }

    return $url . '/?authticket=' . urlencode($res['result']);
}



if (!function_exists('spamexpertsreseller_getLang')) {
    /**
    * FUNCTION spamexpertsreseller_getLang
    * Get the strings in the client's language. The English ones are loaded
    * first, so a language file only needs the strings it translates.
    * Adding a language takes a language/<name>.php file named as in WHMCS.
    * @param array $params
    * @return array
    */
    function spamexpertsreseller_getLang($params)
    {
        global $CONFIG;
        if (!empty($_SESSION['Language'])) {
            $language = $_SESSION['Language'];
        } elseif (!empty($params['clientsdetails']['language'])) {
            $language = $params['clientsdetails']['language'];
        } else {
            $language = $CONFIG['Language'] ?? '';
        }
        $language = strtolower((string) $language);

        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require($dir . 'english.php');

        // Only a plain name, so the language cannot point outside the folder
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        if ($language !== 'english' && preg_match('/^[a-z-]+$/', $language) && file_exists($dir . $language . '.php')) {
            // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
            require($dir . $language . '.php');
        }

        return $lang ?? [];
    }
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
        $vars['lang']      = $lang[(!isset($lang[$page]) ? 'mainsite' : $page)];
        $vars['serviceid'] = $params['serviceid'];

        if (function_exists('generate_token')) {
            $vars['token'] = generate_token('plain');
        }

        $available_pages = ['managedomains'];
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
            $vars['_status'] = ['code' => 2, 'msg' => $error];
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

/**
* FUNCTION spamexpertsreseller_apiError
* Why the client area cannot offer the management pages: the setup, a panel
* that cannot be reached or a reseller the panel doesn't have
* @param array $params
* @param array $lang
* @return string|null
*/
function spamexpertsreseller_apiError($params, $lang)
{
    $error = spamexpertsreseller_setupError($params, $lang);
    if ($error) {
        return $error;
    }

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');
    // A cheap read of only this reseller. The list has every reseller when the
    // username matches none, so the reseller is still looked up in it.
    $api = new spamexperts_api($params);
    $api->call("reseller/list/username/" . rawurlencode($params['username']) . "/no_domains/1/");
    $res = $api->getResponse();
    if (!$api->isSuccess()) {
        if (!isset($res['messages']['error'][0])) {
            return $lang['mainsite']['unavailable'];
        }
        return $res['messages']['error'][0] === spamexperts_api::INVALID_CREDENTIALS
            ? $lang['mainsite']['notconfigured']
            : $lang['mainsite']['apierror'];
    }
    // Without case, as the panel finds a reseller by its username in any case
    $username = mb_strtolower(spamexpertsreseller_panelUsername($params['username']), 'UTF-8');
    foreach ((array) $res['result'] as $resellers) {
        foreach ((array) $resellers as $reseller) {
            if (
                isset($reseller['username']) && is_string($reseller['username'])
                && mb_strtolower($reseller['username'], 'UTF-8') === $username
            ) {
                return null;
            }
        }
    }

    return sprintf($lang['mainsite']['notadded'], $params['username']);
}

if (!function_exists('spamexpertsreseller_managePages')) {
    /**
    * FUNCTION spamexpertsreseller_managePages
    * Management pages the product offers, in the sidebar's order
    * @param array $params
    * @param array $lang
    * @return array Page names and their labels
    */
    function spamexpertsreseller_managePages($params, $lang)
    {
        // The page calls the API as the reseller, so not when its API access is disallowed
        if ($params['configoption5'] === 'on') {
            return [];
        }
        return ['managedomains' => $lang['mainsite']['manage_domains']];
    }
}

if (!function_exists('spamexpertsreseller_shownPages')) {
    /**
    * FUNCTION spamexpertsreseller_shownPages
    * Record the management pages the client area page can show, which the
    * sidebar hook links to
    * @param array|false|null $pages Pages to record, false when the panel
    * cannot be used, or null to read them
    * @return array|false|null Null when no page of this module was shown
    */
    function spamexpertsreseller_shownPages($pages = null)
    {
        static $shown = null;
        if ($pages !== null) {
            $shown = $pages;
        }
        return $shown;
    }
}

if (!function_exists('spamexpertsreseller_management')) {
    /**
    * FUNCTION spamexpertsreseller_management
    * Display a management page in place of the product overview. A posted
    * action is run first, then redirected to the page so the result is shown
    * by a GET request.
    * @param array $params
    * @param string $page
    * @return array|null Null when the page does not exist
    */
    function spamexpertsreseller_management($params, $page)
    {
        $url = 'clientarea.php?action=productdetails&id=' . $params['serviceid'];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $vars = spamexpertsreseller_page($params, $page, false);
            if ($vars === null) {
                return null;
            }

            return [
                'tabOverviewReplacementTemplate' => 'templates/' . $page . '.tpl',
                'templateVariables'              => $vars
            ];
        }

        // WHMCS checks the token of its own forms only. A wrong one logs the
        // client out, as it does for those.
        check_token('WHMCS.default');

        $vars = spamexpertsreseller_page($params, $page, true);
        if ($vars !== null) {
            // Code 2 marks the page as unavailable, which the GET shows again itself
            if (isset($vars['_status']) && $vars['_status']['code'] !== 2) {
                $_SESSION['spamexpertsreseller_result'][$params['serviceid']] = $vars['_status'];
            }
            $url .= '&manage=' . urlencode($page);
        }

        spamexpertsreseller_redirect($url);
    }
}
