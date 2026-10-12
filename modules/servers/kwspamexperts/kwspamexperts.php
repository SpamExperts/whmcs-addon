<?php

use Carbon\Carbon;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
include_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.connection.php');


/**
* FUNCTION kwspamexperts_MetaData
* Module name shown in the WHMCS admin area
* @return array
*/
function kwspamexperts_MetaData()
{
    return [
        'DisplayName' => 'SpamExperts',
    ];
}

/**
* FUNCTION kwspamexperts_ConfigOptions
* Display Configuration fields
* @return array
*/
function kwspamexperts_ConfigOptions()
{
    $configarray = [
     "ProductType"      => [
             "Type"         => "dropdown",
             "Options"      => "Incoming,Outgoing,Both",
             "FriendlyName" => "Product Type",
             "Description"  => ""
         ],
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
     "EditRoute"      => [
             "Type"         => "yesno",
             "FriendlyName" => "Disable user access to manage routes page",
             "Description"  => ""
         ],
     "ChangeContact"      => [
             "Type"         => "yesno",
             "FriendlyName" => "Disable user access to edit contact e-mail page",
             "Description"  => ""
         ],
    ];
    return $configarray;
}


/**
* FUNCTION kwspamexperts_configError
* Message for the admin when the product has no API settings
* @param array $params
* @return string|null
*/
function kwspamexperts_configError($params)
{
    if (empty($params['configoption2']) || empty($params['configoption3']) || empty($params['configoption4'])) {
        return "Set the Spam Panel URL, API Username and API Password in the product's Module Settings.";
    }
    if (preg_match('#^\s*http://#i', $params['configoption2'])) {
        return "Set the Spam Panel URL to an https:// address in the product's Module Settings.";
    }

    return null;
}


/**
* FUNCTION kwspamexperts_isHostname
* Check a client supplied domain or hostname before it goes into an API path.
* IDNs are converted to punycode first. WHMCS bundles
* symfony/polyfill-intl-idn, so idn_to_ascii exists even without intl.
* @param mixed $value
* @return bool
*/
function kwspamexperts_isHostname($value)
{
    if (!is_string($value) || $value === '') {
        return false;
    }
    $ascii = idn_to_ascii($value, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    return $ascii !== false
        && filter_var($ascii, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
}

/**
* FUNCTION kwspamexperts_isEmail
* Check a client supplied e-mail address before it goes into an API path. The
* API splits its path on slashes, even encoded ones, so those are rejected too.
* FILTER_VALIDATE_EMAIL only knows ASCII domains, so an IDN domain is checked
* in its punycode form. The address itself is passed on unchanged.
* @param mixed $value
* @return bool
*/
function kwspamexperts_isEmail($value)
{
    if (!is_string($value) || str_contains($value, '/')) {
        return false;
    }
    $at = strrpos($value, '@');
    if ($at === false) {
        return false;
    }
    $domain = idn_to_ascii(substr($value, $at + 1), IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    return $domain !== false
        && filter_var(substr($value, 0, $at + 1) . $domain, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) !== false;
}


/**
* FUNCTION kwspamexperts_CreateAccount
* Create Account
* @param array $params
* @return string
*/
function kwspamexperts_CreateAccount($params)
{
    if ($error = kwspamexperts_configError($params)) {
        return $error;
    }
    $api        = new kwspamexperts_api($params);
    $domain     = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    $password   = !empty($params['password']) ? $params["password"] : createServerPassword();
    $email      = !empty($params["customfields"]["Email"])
        ? $params["customfields"]["Email"]
        : $params['clientsdetails']['email'];
    $archiving  = (int)(!empty($params["configoptions"]["archiving"]) && $params["configoptions"]["archiving"]);


    // update password
    Capsule::table('tblhosting')
        ->where(["id" => $params['serviceid']])
        ->update([
            // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.CryptoFunctions.WarnCryptoFunc
            "password" => encrypt($password),
            "updated_at" => Carbon::now()
        ]);

    // add domain
    if (!kwspamexperts_isHostname($domain)) {
        return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
    }
    if (!kwspamexperts_isEmail($email)) {
        return "Email is empty or invalid. Please enter a valid e-mail address in the Email field.";
    }
    $api->call("domain/add/domain/" . $domain . "/");
    if ($api->isError()) {
        return $api->error();
    }

    // add email
    $api->call(
        "domainuser/add/domain/" . $domain . "/password/" . rawurlencode($password)
        . "/email/" . rawurlencode($email) . "/"
    );
    if ($api->isError()) {
        return $api->error();
    }

    $outgoing = $params["configoption1"] !== 'Incoming' ? 1 : 0;
    $incoming = $params["configoption1"] !== 'Outgoing' ? 1 : 0;

    $api->call(
        "domain/setproducts/domain/" . $domain . "/incoming/" . $incoming
        . "/outgoing/" . $outgoing . "/archiving/$archiving/"
    );
    if ($api->isError()) {
        return $api->error();
    }

    return "success";
}

/**
* FUNCTION kwspamexperts_TerminateAccount
* Terminate Account
* @param array $params
* @return string
*/
function kwspamexperts_TerminateAccount($params)
{
    if ($error = kwspamexperts_configError($params)) {
        return $error;
    }
    $api    = new kwspamexperts_api($params);
    $domain = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    if (!kwspamexperts_isHostname($domain)) {
        return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
    }
    $api ->call("domain/remove/domain/" . $domain . "/");

    if ($api->isError()) {
        return $api->error();
    }

    return "success";
}


/**
* FUNCTION kwspamexperts_ChangePackage
* Upgrade Account
* @param array $params
* @return string
*/
function kwspamexperts_ChangePackage($params)
{
    if ($error = kwspamexperts_configError($params)) {
        return $error;
    }
    $domain   = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    $outgoing = $params["configoption1"] !== 'Incoming' ? 1 : 0;
    $incoming = $params["configoption1"] !== 'Outgoing' ? 1 : 0;
    $archiving  = (int)(!empty($params["configoptions"]["archiving"]) && $params["configoptions"]["archiving"]);

    $api = new kwspamexperts_api($params);
    if (!kwspamexperts_isHostname($domain)) {
        return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
    }
    $api ->call(
        "domain/setproducts/domain/" . $domain . "/incoming/" . $incoming
        . "/outgoing/" . $outgoing . "/archiving/$archiving/"
    );

    if ($api->isSuccess()) {
        return "success";
    }

    return $api->error();
}

/**
* FUNCTION kwspamexperts_SuspendAccount
* Suspend Account
* @param array $params
* @return string
*/
function kwspamexperts_SuspendAccount($params)
{
    if ($error = kwspamexperts_configError($params)) {
        return $error;
    }
    $domain   = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    $api = new kwspamexperts_api($params);

    if (!kwspamexperts_isHostname($domain)) {
        return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
    }
    $api ->call("domain/whitelistrecipient/domain/" . $domain . "/recipient/*/", false);

    if ($api->isSuccess()) {
        return "success";
    }

    return $api->error();
}

/**
* FUNCTION kwspamexperts_UnsuspendAccount
* Unsuspend Account
* @param array $params
* @return string
*/
function kwspamexperts_UnsuspendAccount($params)
{
    if ($error = kwspamexperts_configError($params)) {
        return $error;
    }
    $domain   = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    $api = new kwspamexperts_api($params);
    if (!kwspamexperts_isHostname($domain)) {
        return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
    }
    $api ->call("domain/unwhitelistrecipient/domain/" . $domain . "/recipient/*/", false);

    if ($api->isSuccess()) {
        return "success";
    }

    return $api->error();
}

if (!function_exists('kwspamexperts_getLang')) {
    /**
    * FUNCTION kwspamexperts_getLang
    * Get the strings in the client's language. The English ones are loaded
    * first, so a language file only needs the strings it translates.
    * Adding a language takes a language/<name>.php file named as in WHMCS.
    * @param array $params
    * @return array
    */
    function kwspamexperts_getLang($params)
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

        $dir = __DIR__ . DS . 'language' . DS;
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


if (!function_exists('kwspamexperts_page')) {
    /**
    * FUNCTION kwspamexperts_page
    * Run a management page script and collect its template vars
    * @param array $params
    * @param string $page
    * @param bool $handle_actions Run the posted action instead of loading the page data
    * @return array|null Null when the page does not exist
    */
    function kwspamexperts_page($params, $page, $handle_actions)
    {
        $lang              = kwspamexperts_getLang($params);
        $api               = new kwspamexperts_api($params);
        $vars['lang']      = $lang[(!isset($lang[$page]) ? 'mainsite' : $page)];
        $vars['serviceid'] = $params['serviceid'];
        $domain            = !empty($params["customfields"]["Domain"])
            ? $params["customfields"]["Domain"]
            : $params['domain'];

        if (function_exists('generate_token')) {
            $vars['token'] = generate_token('plain');
        }

        $available_pages = ['EditEmail', 'ManageRoutes', 'ManageAliases'];
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        if (
            empty($page) || !in_array($page, $available_pages, true)
            || !file_exists(__DIR__ . DS . $page . '.php')
            || !file_exists(__DIR__ . DS . 'templates' . DS . $page . '.tpl')
        ) {
            return null;
        }
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem

        $serviceid = $params['serviceid'];
        if (!$handle_actions && isset($_SESSION['kwspamexperts_result'][$serviceid])) {
            $vars['_result'] = $_SESSION['kwspamexperts_result'][$serviceid];
            unset($_SESSION['kwspamexperts_result'][$serviceid]);
        }

        $error = kwspamexperts_setupError($params, $lang);
        if ($error) {
            $vars['_status'] = ['code' => 2, 'msg' => $error];
            return $vars;
        }

        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require_once(__DIR__ . DS . $page . '.php');

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
* FUNCTION kwspamexperts_setupError
* Message for a product without API settings or a service without a domain
* @param array $params
* @param array $lang
* @return string|null
*/
function kwspamexperts_setupError($params, $lang)
{
    if (kwspamexperts_configError($params)) {
        return $lang['mainsite']['notconfigured'];
    }

    $domain = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    if (empty($domain)) {
        return $lang['mainsite']['notsetup'];
    }
    if (!kwspamexperts_isHostname($domain)) {
        return $lang['mainsite']['invaliddomain'];
    }

    return null;
}

/**
* FUNCTION kwspamexperts_apiError
* Why the client area cannot offer the management pages: the setup, a panel
* that cannot be reached or a domain the panel doesn't have
* @param array $params
* @param array $lang
* @return string|null
*/
function kwspamexperts_apiError($params, $lang)
{
    $error = kwspamexperts_setupError($params, $lang);
    if ($error) {
        return $error;
    }

    // A cheap read, which the API refuses for a domain it doesn't have
    $domain = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    $api = new kwspamexperts_api($params);
    $api->call("domaincontact/get/domain/{$domain}/");
    if ($api->isSuccess()) {
        return null;
    }

    $res = $api->getResponse();
    if (!isset($res['messages']['error'][0])) {
        return $lang['mainsite']['unavailable'];
    }
    switch ($res['messages']['error'][0]) {
        case kwspamexperts_api::INVALID_CREDENTIALS:
            return $lang['mainsite']['notconfigured'];
        case sprintf(kwspamexperts_api::DOMAIN_NOT_ALLOWED, $domain):
            return sprintf($lang['mainsite']['notadded'], $domain);
        default:
            return $lang['mainsite']['apierror'];
    }
}

if (!function_exists('kwspamexperts_managePages')) {
    /**
    * FUNCTION kwspamexperts_managePages
    * Management pages the product offers, in the sidebar's order
    * @param array $params
    * @param array $lang
    * @return array Page names and their labels
    */
    function kwspamexperts_managePages($params, $lang)
    {
        $pages = [];
        if (empty($params['configoption6'])) {
            $pages['EditEmail'] = $lang['mainsite']['editcontactemail'];
        }
        if (empty($params['configoption5'])) {
            $pages['ManageRoutes'] = $lang['mainsite']['manageroutes'];
        }
        $pages['ManageAliases'] = $lang['mainsite']['managealiases'];

        return $pages;
    }
}

if (!function_exists('kwspamexperts_shownPages')) {
    /**
    * FUNCTION kwspamexperts_shownPages
    * Record the management pages the client area page can show, which the
    * sidebar hook links to
    * @param array|false|null $pages Pages to record, false when the panel
    * cannot be used, or null to read them
    * @return array|false|null Null when no page of this module was shown
    */
    function kwspamexperts_shownPages($pages = null)
    {
        static $shown = null;
        if ($pages !== null) {
            $shown = $pages;
        }
        return $shown;
    }
}

if (!function_exists('kwspamexperts_management')) {
    /**
    * FUNCTION kwspamexperts_management
    * Display a management page in place of the product overview. A posted
    * action is run first, then redirected to the page so the result is shown
    * by a GET request.
    * @param array $params
    * @param string $page
    * @return array|null Null when the page does not exist
    */
    function kwspamexperts_management($params, $page)
    {
        $url = 'clientarea.php?action=productdetails&id=' . $params['serviceid'];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $vars = kwspamexperts_page($params, $page, false);
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

        $vars = kwspamexperts_page($params, $page, true);
        if ($vars !== null) {
            // Code 2 marks the page as unavailable, which the GET shows again itself
            if (isset($vars['_status']) && $vars['_status']['code'] !== 2) {
                $_SESSION['kwspamexperts_result'][$params['serviceid']] = $vars['_status'];
            }
            $url .= '&manage=' . urlencode($page);
        }

        kwspamexperts_redirect($url);
    }
}

/**
* FUNCTION kwspamexperts_redirect
* Send the client to a URL instead of the page
* @param string $url
* @return never
*/
function kwspamexperts_redirect($url)
{
    if (ob_get_length()) {
        ob_clean();
    }
    header('Location: ' . $url);
    exit;
}

/**
* FUNCTION kwspamexperts_ClientArea
* Display extended pages in clientarea
* @param array $params
* @return array
*/
function kwspamexperts_ClientArea($params)
{
    $overview = 'clientarea.php?action=productdetails&id=' . $params['serviceid'];

    // The panel has the domain only from create until terminate, as suspending just whitelists its mail
    if (!in_array($params['status'], ['Active', 'Suspended'], true)) {
        if (isset($_GET['manage'])) {
            kwspamexperts_redirect($overview);
        }
        kwspamexperts_shownPages(false);
        return ['vars' => ['api_error' => null]];
    }

    // The management pages are plain links to the product details, so they
    // need no token in the URL, unlike a modop=custom function in WHMCS 9.
    // The login link makes its ticket only when followed, as one expires.
    if (isset($_GET['manage']) && $_GET['manage'] === 'login') {
        // The overview shows why when the panel cannot be reached
        $url = kwspamexperts_loginUrl($params);
        kwspamexperts_redirect($url !== null ? $url : $overview);
    }

    // Without a working panel, the sidebar has no management links and the overview shows why
    $lang  = kwspamexperts_getLang($params);
    $error = kwspamexperts_apiError($params, $lang);
    $pages = $error ? [] : kwspamexperts_managePages($params, $lang);
    if (isset($_GET['manage'])) {
        // Only the pages the sidebar links, so not a disabled one either
        if (is_string($_GET['manage']) && isset($pages[$_GET['manage']])) {
            $output = kwspamexperts_management($params, $_GET['manage']);
            if ($output !== null) {
                kwspamexperts_shownPages($pages);
                return $output;
            }
        }
        unset($_SESSION['kwspamexperts_result'][$params['serviceid']]);
        kwspamexperts_redirect($overview);
    }

    kwspamexperts_shownPages($error ? false : $pages);

    return ['vars' => ['api_error' => $error]];
}

/**
* FUNCTION kwspamexperts_loginUrl
* Panel URL with a new login ticket, made only when the client follows the
* login link rather than on every page that shows it
* @param array $params
* @return string|null Null when the panel cannot be reached
*/
function kwspamexperts_loginUrl($params)
{
    if (kwspamexperts_setupError($params, kwspamexperts_getLang($params))) {
        return null;
    }

    $domain = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    $api = new kwspamexperts_api($params);
    $api->call("authticket/create/username/{$domain}/");
    $auth = $api->getResponse();
    if (!$api->isSuccess()) {
        return null;
    }

    // The API refuses http://, so a URL without https:// lacks a scheme
    $api_url = $params['configoption2'];
    if (!preg_match('#^https://#i', $api_url)) {
        $api_url = 'https://' . $api_url;
    }

    return $api_url . '/?authticket=' . urlencode($auth['result']);
}
