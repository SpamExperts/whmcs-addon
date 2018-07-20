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
if (!defined("WHMCS")) 
{
  die("This file cannot be accessed directly");
}

if(isset($_GET['_debug']) && $_GET['debug']=='turnon')
{
    error_reporting(E_ALL);
    ini_set('display_errors',1);
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
         "UsageLimits"=> array( 
             "Type"         => "yesno", 
             "Size"         => "25",
             "FriendlyName" => "Usage Limits",
             "Description"  => "Tick to allow access to the API for the reseller"
         ),
    );
    return $configarray;
}


/**
* FUNCTION spamexpertsreseller_CreateAccount
* Create Reseller Account
* @param array $params
* @return string
*/
function spamexpertsreseller_CreateAccount($params) 
{
        include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'class.connection.php');
        $api        = new spamexperts_api($params);
        $domain     = !empty($params["customfields"]["Domain"])  ? $params["customfields"]["Domain"] : $params['domain'];
        $password   = !empty($params['password'])                ? $params["password"]               : createServerPassword();
	$email      = !empty($params["customfields"]["Email"])   ? $params["customfields"]["Email"]  : $params['clientsdetails']['email'];
        $username   = !empty($params['username'])                ? $params['username']               : uniqid();
        
       
        $api->call('/reseller/add/username/'.$username.'/password/'.rawurlencode($password).'/email/'.rawurlencode($email).'/domainslimit/'.$params['configoption4'].'/api_usage/'.($params['configoption5']=='on' ? 1 : 0));
        if($api->isSuccess())
        {   
            // update password & username
            update_query("tblhosting", array("password" => encrypt($password),"username"=>$username), array("id" => $params['serviceid']));
            return "success";
        } else return $api->error();
        
}


/**
* FUNCTION spamexpertsreseller_TerminateAccount
* Terminate Reseller Account
* @param array $params
* @return string
*/
function spamexpertsreseller_TerminateAccount($params) 
{
        include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'class.connection.php');
        $api = new spamexperts_api($params);
        $api ->call("reseller/remove/username/".$params['username']."/");
        
        if ($api->isSuccess())
            return "success";
        
        return $api->error();
	
}


/**
* FUNCTION spamexpertsreseller_ChangePackage
* Upgrade Reseller Account
* @param array $params
* @return string
*/
function spamexpertsreseller_ChangePackage($params) {
        include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'class.connection.php');
        $api = new spamexperts_api($params);
        $api ->call("reseller/update/username/".$params['username']."/password/".rawurlencode($params['password'])."/domainslimit/".$params['configoption4']."/api_usage/".($params['configoption5']=='on' ? 1 : 0)."/");
        
        if ($api->isSuccess())
        {
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
function spamexpertsreseller_ClientArea($params) {
    $output = array('vars' => array());

    include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'class.connection.php');
    $lang = spamexpertsreseller_getLang($params);
    $auth = '';

    $api = new spamexperts_api($params);
    $api-> call("authticket/create/username/".$params['username']."/");

    if ($api->isSuccess()) {
        $res  = $api->getResponse();
        $auth = $res['result'];
    }

    $url = (strpos($params['configoption1'], 'http') === false)
        ? 'http://'.$params['configoption1']
        : $params['configoption1'];

    $output['vars']['api_url'] = $url.'/?authticket='.$auth;
    $output['vars']['lang'] = $lang['mainsite'];

    return $output;
}



/**
* FUNCTION spamexpertsreseller_getLang
* Get user language
* @params array
* @return string
*/ 
if(!function_exists('spamexpertsreseller_getLang')){
    function spamexpertsreseller_getLang($params){
         global $CONFIG;
         if(!empty($_SESSION['Language']))
             $language = strtolower($_SESSION['Language']);
         else if(isset($params['clientsdetails']['language']) && strtolower($params['clientsdetails']['language'])!='')
             $language = strtolower($params['clientsdetails']['language']);
         else
             $language = $CONFIG['Language'];

         $langfilename = dirname(__FILE__).DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language.'.php';
         if(file_exists($langfilename)) 
            require_once($langfilename);
         else
            require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'english.php');

         if(isset($lang))
             return $lang;
    }
}


/**
* FUNCTION spamexpertsreseller_ClientAreaCustomButtonArray()
* Display buttons in clientarea
* @return array
*/
function spamexpertsreseller_ClientAreaCustomButtonArray() {
      
        $buttonarray = array(
           'Management' => 'management'
        );
	return $buttonarray;
}


/**
* FUNCTION spamexpertsreseller_management
* Display extended pages in clientarea
* @params array
* @return array
*/ 
if (!function_exists('spamexpertsreseller_management')){
     function spamexpertsreseller_management($params){	
         include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'class.connection.php');
         global $CONFIG;	
         $lang              = spamexpertsreseller_getLang($params); 
         $api               = new spamexperts_api($params);
         $page              = (isset($_GET['page'])                      ? addslashes($_GET['page'])          : 'mainsite');
         $vars['main_lang'] = $lang['mainsite'];
         $vars['lang']      = $lang[(!isset($lang[$page])                ? 'mainsite'                         : $page)];
         $vars['serviceid'] = $params['serviceid'];
        
         if(empty($page) || !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$page.'.php') || !file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$page.'.tpl'))
         {
             $vars['_status']   = array('code' => 1, 'msg' => $lang['mainsite']['notfound']);
             return array('vars'=> $vars);
         }     
         
         if(isset($_SESSION['spam_status']))
         {
             $vars['_status'] = $_SESSION['spam_status'];
             unset($_SESSION['spam_status']);
         }
         
        
         require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.$page.'.php');

         return array(
               'templatefile' => 'templates/'.$page,
               'breadcrumb'   => ' > <a href="#">Server Details</a>',
               'vars'         => $vars
         );
     }
}