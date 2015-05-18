<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-11, 09:51:59)
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
if (!defined("WHMCS")) {
  die("This file cannot be accessed directly");
}
if(isset($_POST['primaryemail'])){
   
    $api->call('domaincontact/set/domain/'.$domain.'/email/'.$_POST['primaryemail'].'/');
    if ($api->isSuccess())
        $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['email_changed']);
    else 
        $vars['_status']  = array('code'=>0,'msg'=>$api->error());
}

if(isset($_POST['adminemail'])){
    $api->call('domainadmincontact/set/domain/'.$domain.'/email/'.$_POST['adminemail'].'/');
    if ($api->isSuccess())
        $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['email_changed']);
    else 
        $vars['_status']  = array('code'=>0,'msg'=>$api->error());
}


$api ->call("domaincontact/get/domain/".$domain."/");
if ($api->isSuccess())
{
    $vars['primarycontact'] = $api->getResponse();
}

$api ->call("domainadmincontact/get/domain/'.$domain.'/");
if ($api->isSuccess())
{
    $vars['admincontact'] = $api->getResponse();
}

/* DEPRACTED
 * 
$find  = array('http://','http://');
$url   = 'https://'.urlencode($params['configoption3']).':'.urlencode($params['configoption4']).'@'.str_replace($find, '', $params['configoption2']);

$admin = file_get_contents($url.'/cgi-bin/api?call=api_get_administrator_contact&domain='.$domain);
$admin = array_filter(explode("\n",$admin));
$old_a = explode(" ",$admin[0]);
$admin = explode(" ",$admin[1]);

if(!empty($admin[1]))
    $vars['admincontact']   = $admin[1];
else if (!empty($old_a[1]))
    $vars['admincontact']   = $old_a[1];


$primary = file_get_contents($url.'/cgi-bin/api?call=api_get_contact&domain='.$domain);
$primary = array_filter(explode("\n",$primary));
$primary = explode(" ",$primary[1]);
$old_p   = explode(" ",$old_p[0]);

if(!empty($primary[1]))
    $vars['primarycontact'] = $primary[1];
elseif(!empty($old_p[1]))
    $vars['primarycontact'] = $old_p[1];
 * 
 * 
 */