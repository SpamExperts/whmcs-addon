<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 15:57:41)
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

switch($_REQUEST['_action'])
{
    case 'addalias':
	$api->call("domainalias/add/domain/".$domain."/alias/".$_POST['alias']['new']."/");
	if ($api->isSuccess())
            $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['alias_added']);
        else 
            $vars['_status']  = array('code'=>0,'msg'=>$api->error());
    break;
    case 'deletealias':
        if(isset($_POST['alias']['old_list']) && count($_POST['alias']['old_list'])>0)
        {
            foreach($_POST['alias']['old_list'] as $val)
            {
                $api->call("domainalias/remove/domain/".$domain."/alias/".$val."/");
                if ($api->isSuccess())
                {
                    $status .= $vars['lang']['alias_removed']."<br />";
                } else {
                    $vars['_status']  = array('code'=>0,'msg'=>$api->error()); 
                    break;
                }
            }
            
            $vars['_status']  = array('code'=>1,'msg'=> $status);
        } else if(isset($_POST['delete-item'])) {
            $api->call("domainalias/remove/domain/".$domain."/alias/".array_shift(array_keys($_POST['delete-item']))."/");
            if ($api->isSuccess())
                $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['alias_removed']);
            else 
                $vars['_status']  = array('code'=>0,'msg'=>$api->error());
        }
        
            
    break;
        
}

$api ->call("domainalias/list/domain/".$domain."/");

if ($api->isSuccess())
{
    $data                  = $api->getResponse();
    
    foreach($data['result'] as $key => $val)
    {
        if($val == $domain)
            unset($data['result'][$key]);
    }
    
    $vars['domainaliases'] = $data['result'];
} else {
    $vars['_status']       = array('code'=>0,'msg'=>$api->error());
}