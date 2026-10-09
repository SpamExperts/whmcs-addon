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
 * Management page, loaded by kwspamexperts_page() and run in its scope.
 *
 * @var kwspamexperts_api $api
 * @var string $domain Domain of the service
 * @var bool $handle_actions Run the posted action instead of loading the page data
 * @var array $vars Template variables, returned by the page function
 *
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

if (!defined("WHMCS"))
{
  die("This file cannot be accessed directly");
}

if($handle_actions)
{
switch(isset($_POST['_action']) ? $_POST['_action'] : '')
{
    case 'addalias':
        $alias = isset($_POST['alias']['new']) ? $_POST['alias']['new'] : '';
        if (!kwspamexperts_isHostname($alias)) {
            $vars['_status'] = array('code'=>0,'msg'=>$vars['lang']['invalid_alias']);
            break;
        }
	$api->call("domainalias/add/domain/".$domain."/alias/".rawurlencode($alias)."/");
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
                if (!kwspamexperts_isHostname($val)) {
                    $vars['_status'] = array('code'=>0,'msg'=>$vars['lang']['invalid_alias']);
                    break 2;
                }
            }

            foreach($_POST['alias']['old_list'] as $val)
            {
                $api->call("domainalias/remove/domain/".$domain."/alias/".rawurlencode($val)."/");
                if (!$api->isSuccess())
                {
                    $vars['_status']  = array('code'=>0,'msg'=>$api->error());
                    break 2;
                }
            }

            $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['alias_removed']);
        } else if(isset($_POST['delete-item'])) {
            $alias = key((array) $_POST['delete-item']);
            if (!kwspamexperts_isHostname($alias)) {
                $vars['_status'] = array('code'=>0,'msg'=>$vars['lang']['invalid_alias']);
                break;
            }
            $api->call("domainalias/remove/domain/".$domain."/alias/".rawurlencode($alias)."/");
            if ($api->isSuccess())
                $vars['_status']  = array('code'=>1,'msg'=>$vars['lang']['alias_removed']);
            else 
                $vars['_status']  = array('code'=>0,'msg'=>$api->error());
        }
        
            
    break;
        
}

return;
}

$vars['domainaliases'] = array();
$api ->call("domainalias/list/domain/".$domain."/");

if ($api->isSuccess())
{
    $data                  = $api->getResponse();
    
    foreach($data['result'] as $key => $val)
    {
        if($val == $domain)
            unset($data['result'][$key]);
    }
    
    $vars['domainaliases'] = array_filter((array) $data['result']);
} else {
    $vars['_status']       = array('code'=>0,'msg'=>$api->error());
}