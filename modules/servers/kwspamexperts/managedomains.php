<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-11, 13:00:09)
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

$api = new spamexperts_api($params, true);

if(isset($_POST['_action']))
    switch($_POST['_action'])
    {
        case 'unbind':
            $api->call('/auth/getrole/');
            $role = $api->getResponse();
            if($role['result'] == 'admin')
            {
                $api->call('/reseller/unbinddomains/username/'.$params['username'].'/domains/'.$_POST['unbind-domain'].'/');
            } else
            {
                $api->call('/domain/remove/domain/'.$_POST['unbind-domain'].'/');
            }

            if ($api->isSuccess())
                $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['domain_unbinded']);
            else 
                $vars['_status']  = array('code'=>0,'msg'=>$api->error());
            break;
        case 'bind':
            $api->call('/auth/getrole/');
            $role = $api->getResponse();
            
            if($role['result'] == 'admin')
            {
                $api->call('/domain/exists/domain/'.$_POST['bind']['domain'].'/');
                if($api->isSuccess())
                {
                    $res = $api->getResponse();
                    if($res['result']['present'] != 1)
                    {
                        $api->call('/domain/add/domain/'.$_POST['bind']['domain'].'/');
                    }
                }  
                $api->call('/domain/getowner/domain/'.$_POST['bind']['domain']);
                if($api->isSuccess())
                {
                    $owner = $api->getResponse();
                    if(empty($owner['result']) || $owner['result']['username'] == $params['username'])
                    {
                        $api->call('/reseller/binddomains/username/'.$params['username'].'/domains/'.$_POST['bind']['domain'].'/');
                        if ($api->isSuccess())
                            $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['domain_binded']);
                        else 
                            $vars['_status']  = array('code'=>0,'msg'=>$api->error());
                    } else 
                    {
                        $vars['_status'] = array('code' => 0, 'msg' => $vars['lang']['invalid_owner']);
                    }
                } else 
                {
                    $vars['_status']  = array('code'=>0,'msg'=>$api->error());
                }      
            } else 
            {
                $api->call('/domain/add/domain/'.$_POST['bind']['domain'].'/');
                if ($api->isSuccess())
                    $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['domain_binded']);
                else 
                    $vars['_status']  = array('code'=>0,'msg'=>$api->error());
            }
            
            break;    
    }

$api->call('/domainslist/get/');
if($api->isSuccess())
{
    $list                   = $api->getResponse();
    $vars['domains_list']   = $list['result'];
} else {
    $vars['_status']        = array('code'=>0,'msg'=>$api->error());
}
