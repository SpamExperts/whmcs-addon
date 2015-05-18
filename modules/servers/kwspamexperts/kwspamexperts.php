<?php

error_reporting(E_ALL);
ini_set('display_errors',0);

if (!defined("WHMCS")) 
{
  die("This file cannot be accessed directly");
}

if(isset($_GET['_debug']) && $_GET['_debug'] == 'turnon')
{
    error_reporting(E_ALL);
    ini_set('display_errors',1);
}

if(!defined('DS')) 
    define ('DS', DIRECTORY_SEPARATOR);

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'class.connection.php');


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
	update_query("tblhosting", array("password" => encrypt($password)), array("id" => $params['serviceid']));

        // add domain
        $api->call("domain/add/domain/".$domain."/");
	if ($api->isError())
        {
            return $api->error();
	}
        
        // add email
	$api->call("domainuser/add/domain/".$domain."/password/".$password."/email/".$email."/");
	if ($api->isError())
        {
            return $api->error();
	}
        
        $outgoing = $params["configoption1"] != 'Incoming' ? 1 : 0;
        $incoming = $params["configoption1"] != 'Outgoing' ? 1 : 0;

	$api->call("domain/setproducts/domain/".$domain."/incoming/".$incoming."/outgoing/".$outgoing."/archiving/$archiving/");
	if ($api->isError())
        {
            return $api->error();
	}
        
        $res = kwspamexperts_ChangePackage($params);
        if (!$res)
        {
            return $res;
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

         $langfilename = dirname(__FILE__).DS.'language'.DS.$language.'.php';
         if(file_exists($langfilename)) 
            require_once($langfilename);
         else
            require_once(dirname(__FILE__).DS.'language'.DS.'english.php');

         if(isset($lang))
             return $lang;
    }
}


/**
* FUNCTION kwspamexperts_ClientAreaCustomButtonArray()
* Display buttons in clientarea
* @return array
*/
function kwspamexperts_ClientAreaCustomButtonArray() {
      
        $buttonarray = array(
           'Management' => 'management'
        );
	return $buttonarray;
}

/**
* FUNCTION kwspamexperts_management
* Display extended pages in clientarea
* @params array
* @return array
*/ 
if (!function_exists('kwspamexperts_management')){
     function kwspamexperts_management($params){	
         global $CONFIG;	
         $lang              = kwspamexperts_getLang($params);         
         $api               = new kwspamexperts_api($params);
         $page              = (isset($_GET['page'])                      ? addslashes($_GET['page'])          : 'mainsite');
         $vars['main_lang'] = $lang['mainsite'];
         $vars['lang']      = $lang[(!isset($lang[$page])                ? 'mainsite'                         : $page)];
         $vars['serviceid'] = $params['serviceid'];
         $domain            = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"]  : $params['domain'];
         
         $allowPages = array('EditEmail','ManageRoutes','ManageAliases');
         
         if(empty($page) || !file_exists(dirname(__FILE__).DS.$page.'.php') || !file_exists(dirname(__FILE__).DS.'templates'.DS.$page.'.tpl') || !in_array($page, $allowPages))
         {
             $vars['_status']    = array('code' => 1, 'msg' => $lang['mainsite']['notfound']);
             return array('vars' => $vars);
         }     
         
         if(isset($_SESSION['spam_status']))
         {
             $vars['_status'] = $_SESSION['spam_status'];
             unset($_SESSION['spam_status']);
         }

         require_once(dirname(__FILE__).DS.$page.'.php');
         return array(
               'templatefile' => 'templates/'.$page,
               'breadcrumb'   => ' > <a href="#">Server Details</a>',
               'vars'         => $vars
         );
     }
}

/**
* FUNCTION kwspamexperts_ClientArea
* Display extended pages in clientarea
* @params array
* @return array
*/ 
function kwspamexperts_ClientArea($params) {
    global $CONFIG,$smarty;	
    $lang   = kwspamexperts_getLang($params);   
    $api    = new kwspamexperts_api($params);
    $smarty ->assign('lang',$lang['mainsite']);
    $domain = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"] : $params['domain'];
    $api    -> call("authticket/create/username/".$domain."/");
    
    if(strpos($params['configoption2'], 'http') !== false)
        $api_url = $params['configoption2'];
    else
        $api_url = 'https://'.$params['configoption2'];
    
    $auth = $api->getResponse();
    if($api->isSuccess()){
        $smarty->assign('api_url', $api_url);
        $smarty->assign('api_auth',$auth['result']);
    }
}