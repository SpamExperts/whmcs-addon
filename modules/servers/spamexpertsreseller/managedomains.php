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
 * Management page, loaded by spamexpertsreseller_page() and run in its scope.
 *
 * @var array $params Module parameters from WHMCS
 * @var bool $handle_actions Run the posted action instead of loading the page data
 * @var array $vars Template variables, returned by the page function
 *
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if (!defined("WHMCS")) 
{
  die("This file cannot be accessed directly");
}

$api = new spamexperts_api($params, true);

if($handle_actions)
{
    switch(isset($_POST['_action']) ? $_POST['_action'] : '')
    {
        case 'unbind':
            $unbindDomain = isset($_POST['unbind-domain']) ? $_POST['unbind-domain'] : '';
            if (!spamexpertsreseller_isHostname($unbindDomain)) {
                $vars['_status'] = array('code'=>0,'msg'=>$vars['lang']['invalid_domain']);
                break;
            }
            $api->call('/auth/getrole/');
            $role = $api->getResponse();
            if($role['result'] == 'admin')
            {
                $api->call('/reseller/unbinddomains/username/'.$params['username'].'/domains/'.rawurlencode($unbindDomain).'/');
            } else
            {
                $api->call('/domain/remove/domain/'.rawurlencode($unbindDomain).'/');
            }

            if ($api->isSuccess())
                $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['domain_unbinded']);
            else 
                $vars['_status']  = array('code'=>0,'msg'=>$api->error());
            break;
        case 'bind':
            $bindDomain = isset($_POST['bind']['domain']) ? $_POST['bind']['domain'] : '';
            if (!spamexpertsreseller_isHostname($bindDomain)) {
                $vars['_status'] = array('code'=>0,'msg'=>$vars['lang']['invalid_domain']);
                break;
            }
            $api->call('/auth/getrole/');
            $role = $api->getResponse();
            
            if($role['result'] == 'admin')
            {
                $api->call('/domain/exists/domain/'.rawurlencode($bindDomain).'/');
                if($api->isSuccess())
                {
                    $res = $api->getResponse();
                    if($res['result']['present'] != 1)
                    {
                        $api->call('/domain/add/domain/'.rawurlencode($bindDomain).'/');
                    }
                }  
                $api->call('/domain/getowner/domain/'.rawurlencode($bindDomain));
                if($api->isSuccess())
                {
                    $owner = $api->getResponse();
                    if(empty($owner['result']) || $owner['result']['username'] == $params['username'])
                    {
                        $api->call('/reseller/binddomains/username/'.$params['username'].'/domains/'.rawurlencode($bindDomain).'/');
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
                $api->call('/domain/add/domain/'.rawurlencode($bindDomain).'/');
                if ($api->isSuccess())
                    $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['domain_binded']);
                else 
                    $vars['_status']  = array('code'=>0,'msg'=>$api->error());
            }
            
            break;    
    }

    return;
}

$api->call('/domainslist/get/');
if($api->isSuccess())
{
    $list                   = $api->getResponse();
    $vars['domains_list']   = $list['result'];
} else {
    $vars['_status']        = array('code'=>0,'msg'=>$api->error());
}
