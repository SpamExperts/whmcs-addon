<?php

use Carbon\Carbon;
use WHMCS\Database\Capsule;

ini_set('display_errors',0);

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

if (isset($_GET['_debug']) && $_GET['_debug'] === 'turnon') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
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
* FUNCTION kwspamexperts_CreateAccount
* Create Account
* @param array $params
* @return string
*/
function kwspamexperts_CreateAccount($params)
{
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
    if (empty($domain)) {
        return "Domain cannot be empty. Please enter domain name in Domain field.";
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
        $api    = new kwspamexperts_api($params);
        $domain = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"] : $params['domain'];
        if(empty($domain)){
            return "Domain cannot be empty. Please enter domain name in Domain field.";
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
    $domain   = !empty($params["customfields"]["Domain"])   ? $params["customfields"]["Domain"] : $params['domain'];
    $outgoing = $params["configoption1"] != 'Incoming'      ? 1                                 : 0;
        $incoming = $params["configoption1"] != 'Outgoing'      ? 1                                 : 0;
        $archiving  = (int)(!empty($params["configoptions"]["archiving"]) && $params["configoptions"]["archiving"]);

        $api = new kwspamexperts_api($params);
        if(empty($domain)){
            return "Domain cannot be empty. Please enter domain name in Domain field.";
        }
        $api ->call("domain/setproducts/domain/".$domain."/incoming/".$incoming."/outgoing/".$outgoing."/archiving/$archiving/");

        if ($api->isSuccess())
        {
            return "success";
    }

        return $api->error();
}

function kwspamexperts_SuspendAccount($params)
{
    $domain   = !empty($params["customfields"]["Domain"])   ? $params["customfields"]["Domain"] : $params['domain'];
        $api = new kwspamexperts_api($params);

        if(empty($domain)){
            return "Domain cannot be empty. Please enter domain name in Domain field.";
        }
        $api ->call("domain/whitelistrecipient/domain/".$domain."/recipient/*/",false);

        if ($api->isSuccess())
        {
            return "success";
    }

        return $api->error();
}

function kwspamexperts_UnsuspendAccount($params)
{
    $domain   = !empty($params["customfields"]["Domain"])   ? $params["customfields"]["Domain"] : $params['domain'];
        $api = new kwspamexperts_api($params);
        if(empty($domain)){
            return "Domain cannot be empty. Please enter domain name in Domain field.";
        }
        $api ->call("domain/unwhitelistrecipient/domain/".$domain."/recipient/*/",false);

        if ($api->isSuccess())
        {
            return "success";
    }

        return $api->error();
}

/**
* FUNCTION kwspamexperts_getLang
* Get user language
* @params array
* @return string
*/
if(!function_exists('kwspamexperts_getLang')){
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

/**
* FUNCTION kwspamexperts_page
* Run a management page script and collect its template vars
* @param array $params
* @param string $page
* @param bool $handle_actions Run the posted action instead of loading the page data
* @return array|null Null when the page does not exist
*/
if (!function_exists('kwspamexperts_page')){
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

         if(!$handle_actions && isset($_SESSION['spam_status']))
         {
             $vars['_status'] = $_SESSION['spam_status'];
             unset($_SESSION['spam_status']);
         }

         $error = kwspamexperts_setupError($params, $lang);
         if ($error) {
             $vars['_status'] = array('code' => 2, 'msg' => $error);
             return $vars;
         }

         // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
         require_once(__DIR__.DS.$page.'.php');
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
    if (empty($params['configoption2']) || empty($params['configoption3']) || empty($params['configoption4'])) {
        return $lang['mainsite']['notconfigured'];
    }

    $domain = !empty($params["customfields"]["Domain"]) ? $params["customfields"]["Domain"] : $params['domain'];
    if (empty($domain)) {
        return $lang['mainsite']['notsetup'];
    }

    return null;
}

/**
* FUNCTION kwspamexperts_management
* Display extended pages in clientarea. A posted action is run first, then
* redirected to the page so the result is shown by a GET request.
* @params array
* @return array
*/
if (!function_exists('kwspamexperts_management')){
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
             if (isset($vars['_status'])) {
                 $_SESSION['spam_status'] = $vars['_status'];
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
