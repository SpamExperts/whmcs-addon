<?php

use Carbon\Carbon;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

defined('DS') or define ('DS', DIRECTORY_SEPARATOR);

// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
include_once(__DIR__.DIRECTORY_SEPARATOR.'class.connection.php');


/**
* FUNCTION kwspamexperts_ConfigOptions
* Display Configuration fields
* @return array
*/
function kwspamexperts_ConfigOptions() {
    $configarray = array(
     "ProductType"      => array(
             "Type"         => "dropdown",
             "Options"      => "Incoming,Outgoing,Both",
             "FriendlyName" => "Product Type",
             "Description"  => ""
         ),
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
     "EditRoute"      => array(
             "Type"         => "yesno",
             "FriendlyName" => "Disable user access to manage routes page",
             "Description"  => ""
         ),
     "ChangeContact"      => array(
             "Type"         => "yesno",
             "FriendlyName" => "Disable user access to edit contact e-mail page",
             "Description"  => ""
         ),
    );
    return $configarray;
}


/**
* FUNCTION kwspamexperts_configError
* Message for the admin when the product has no API settings
* @param array $params
* @return string|null
*/
function kwspamexperts_configError($params) {
    if (empty($params['configoption2']) || empty($params['configoption3']) || empty($params['configoption4'])) {
        return "Set the Spam Panel URL, API Username and API Password in the product's Module Settings.";
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
function kwspamexperts_isHostname($value) {
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
* @param mixed $value
* @return bool
*/
function kwspamexperts_isEmail($value) {
    return is_string($value) && strpos($value, '/') === false
        && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
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
    $domain     = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"] : $params['domain'];
    $password   = !empty($params['password'])                ? $params["password"]               : createServerPassword();
    $email      = !empty($params["customfields"]["Email"])   ? $params["customfields"]["Email"]  : $params['clientsdetails']['email'];
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
    $api->call("domain/add/domain/".$domain."/");
    if ($api->isError()) {
        return $api->error();
    }

    // add email
    $api->call("domainuser/add/domain/".$domain."/password/".rawurlencode($password)."/email/".rawurlencode($email)."/");
    if ($api->isError()) {
        return $api->error();
    }

    $outgoing = $params["configoption1"] !== 'Incoming' ? 1 : 0;
    $incoming = $params["configoption1"] !== 'Outgoing' ? 1 : 0;

    $api->call("domain/setproducts/domain/".$domain."/incoming/".$incoming."/outgoing/".$outgoing."/archiving/$archiving/");
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
        $domain = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"] : $params['domain'];
        if (!kwspamexperts_isHostname($domain)) {
            return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
        }
        $api ->call("domain/remove/domain/".$domain."/");

        if ($api->isError())
        {
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
function kwspamexperts_ChangePackage($params) {
    if ($error = kwspamexperts_configError($params)) {
        return $error;
    }
    $domain   = !empty($params["customfields"]["Domain"])   ? $params["customfields"]["Domain"] : $params['domain'];
    $outgoing = $params["configoption1"] != 'Incoming'      ? 1                                 : 0;
        $incoming = $params["configoption1"] != 'Outgoing'      ? 1                                 : 0;
        $archiving  = (int)(!empty($params["configoptions"]["archiving"]) && $params["configoptions"]["archiving"]);

        $api = new kwspamexperts_api($params);
        if (!kwspamexperts_isHostname($domain)) {
            return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
        }
        $api ->call("domain/setproducts/domain/".$domain."/incoming/".$incoming."/outgoing/".$outgoing."/archiving/$archiving/");

        if ($api->isSuccess())
        {
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
    $domain   = !empty($params["customfields"]["Domain"])   ? $params["customfields"]["Domain"] : $params['domain'];
        $api = new kwspamexperts_api($params);

        if (!kwspamexperts_isHostname($domain)) {
            return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
        }
        $api ->call("domain/whitelistrecipient/domain/".$domain."/recipient/*/",false);

        if ($api->isSuccess())
        {
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
    $domain   = !empty($params["customfields"]["Domain"])   ? $params["customfields"]["Domain"] : $params['domain'];
        $api = new kwspamexperts_api($params);
        if (!kwspamexperts_isHostname($domain)) {
            return "Domain is empty or invalid. Please enter a valid domain name in the Domain field.";
        }
        $api ->call("domain/unwhitelistrecipient/domain/".$domain."/recipient/*/",false);

        if ($api->isSuccess())
        {
            return "success";
    }

        return $api->error();
}

if(!function_exists('kwspamexperts_getLang')){
    /**
    * FUNCTION kwspamexperts_getLang
    * Get user language
    * @param array $params
    * @return array
    */
    function kwspamexperts_getLang($params){
        global $CONFIG;
        if(!empty($_SESSION['Language']))
            $language = strtolower($_SESSION['Language']);
        else if(isset($params['clientsdetails']['language']) && strtolower($params['clientsdetails']['language'])!='')
            $language = strtolower($params['clientsdetails']['language']);
        else
            $language = $CONFIG['Language'];

        $available_languages = array('english');
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        $langfilename = __DIR__.DS.'language'.DS.$language.'.php';
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        if(in_array($language, $available_languages) && file_exists($langfilename)) {
            // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
            require($langfilename);
        } else {
            // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
            require(__DIR__ . DS . 'language' . DS . 'english.php');
        }

        if (isset($lang)) {
            return $lang;
        } elseif (isset($LANG)) {
            return $LANG;
        } else {
            return array();
        }
    }
}


/**
* FUNCTION kwspamexperts_ClientAreaAllowedFunctions()
* Allow the management pages to be called from the clientarea without
* adding a button for them
* @return array
*/
function kwspamexperts_ClientAreaAllowedFunctions() {
    $buttonarray = array(
        'Management' => 'management'
    );
    return $buttonarray;
}

if (!function_exists('kwspamexperts_page')){
     /**
     * FUNCTION kwspamexperts_page
     * Run a management page script and collect its template vars
     * @param array $params
     * @param string $page
     * @param bool $handle_actions Run the posted action instead of loading the page data
     * @return array|null Null when the page does not exist
     */
     function kwspamexperts_page($params, $page, $handle_actions){
         $lang              = kwspamexperts_getLang($params);
         $api               = new kwspamexperts_api($params);
         $vars['main_lang'] = $lang['mainsite'];
         $vars['lang']      = $lang[(!isset($lang[$page])                ? 'mainsite'                         : $page)];
         $vars['serviceid'] = $params['serviceid'];
         $domain            = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"]  : $params['domain'];

         if (function_exists('generate_token')) {
             $vars['token'] = generate_token('plain');
         }

         $available_pages = array('EditEmail', 'ManageRoutes', 'ManageAliases');
         // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
         if(empty($page) || !in_array($page, $available_pages, true)
             || !file_exists(__DIR__.DS.$page.'.php')
             || !file_exists(__DIR__.DS.'templates'.DS.$page.'.tpl')
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
             $vars['_status'] = array('code' => 2, 'msg' => $error);
             return $vars;
         }

         // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
         require_once(__DIR__.DS.$page.'.php');

         // The same API error can come from the action and from loading the page
         if (isset($vars['_result'], $vars['_status'])
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
function kwspamexperts_setupError($params, $lang) {
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

if (!function_exists('kwspamexperts_management')){
     /**
     * FUNCTION kwspamexperts_management
     * Display extended pages in clientarea. A posted action is run first, then
     * redirected to the page so the result is shown by a GET request.
     * @param array $params
     * @return array
     */
     function kwspamexperts_management($params){
         $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';

         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $vars = kwspamexperts_page($params, $page, false);
             if ($vars === null) {
                 $lang = kwspamexperts_getLang($params);
                 return array('vars' => array(
                     '_status' => array('code' => 1, 'msg' => $lang['mainsite']['notfound'])
                 ));
             }

             return array(
                 'templatefile' => 'templates/'.$page,
                 'breadcrumb'   => ' > <a href="#">Server Details</a>',
                 'vars'         => $vars
             );
         }

         $url  = 'clientarea.php?action=productdetails&id='.$params['serviceid'];
         $vars = kwspamexperts_page($params, $page, true);
         if ($vars !== null) {
             // Code 2 marks the page as unavailable, which the GET shows again itself
             if (isset($vars['_status']) && $vars['_status']['code'] !== 2) {
                 $_SESSION['kwspamexperts_result'][$params['serviceid']] = $vars['_status'];
             }
             $url .= '&modop=custom&a=management&page='.urlencode($page)
                  .'&token='.urlencode(isset($vars['token']) ? $vars['token'] : '');
         }

         if (ob_get_length()) {
             ob_clean();
         }
         header('Location: '.$url);
         exit;
     }
}

/**
* FUNCTION kwspamexperts_ClientArea
* Display extended pages in clientarea
* @param array $params
* @return array
*/
function kwspamexperts_ClientArea($params) {
    $output = array('vars' => array());
    if (function_exists('generate_token')) {
        $output['vars']['token'] = generate_token('plain');
    }

    $lang = kwspamexperts_getLang($params);

    $output['vars']['lang'] = $lang['mainsite'];
    $domain = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];

    $error = kwspamexperts_setupError($params, $lang);
    if ($error) {
        $output['vars']['api_error'] = $error;
    } else {
        $api = new kwspamexperts_api($params);
        $api->call("authticket/create/username/{$domain}/");

        $api_url = (strpos($params['configoption2'], 'http') !== false)
            ? $params['configoption2']
            : 'https://'.$params['configoption2'];

        $auth = $api->getResponse();
        if ($api->isSuccess()) {
            $output['vars']['api_url'] = $api_url;
            $output['vars']['api_auth'] = $auth['result'];
        } elseif (isset($auth['messages']['error'][0])) {
            $output['vars']['api_error'] = sprintf($lang['mainsite']['notadded'], $domain);
        } else {
            $output['vars']['api_error'] = $lang['mainsite']['unavailable'];
        }
    }

    $output['vars']['disable_manage_routes'] = $params['configoption5'];
    $output['vars']['disable_edit_contact'] = $params['configoption6'];

    return $output;
}
