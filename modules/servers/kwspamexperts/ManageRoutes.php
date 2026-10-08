<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 14:09:40)
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

if(! $params['configoption5']){
    if($handle_actions){
    $api ->call("domain/getroute/domain/".$domain."/");
    $routes = $api->getResponse();
    switch(isset($_POST['_action']) ? $_POST['_action'] : '')
    {
        case 'deleteroute':
            $found = false;
            if(isset($_POST['routes']['old_list']) && count($_POST['routes']['old_list']) > 0)
            {
                 if(count($_POST['routes']['old_list']) == count($routes['result'])){
                    $vars['_status'] = array('code' => 0,'msg' => $vars['lang']['remove_info']);
                    break;
                 }
                 foreach($routes['result'] as $key => $val)
                 {
                     $source = explode('::',$val);
                     if(in_array($source[0],$_POST['routes']['old_list'])){
                         unset($routes['result'][$key]);
                         $found = true;
                     }
                 }
            } else if(isset($_POST['routetodel']))
            {
                foreach($routes['result'] as $key => $val){
                    if($val == $_POST['routetodel'].'::'.$_POST['routeporttodel'])
                    {
                        $found = true;
                        unset($routes['result'][$key]);
                        break;
                    }  
                }
            }


            if($found){
                $api ->call("domain/edit/domain/".$domain."/destinations/".rawurlencode(str_replace('::',':',json_encode($routes['result'])))."/");

                if($api->isSuccess())
                    $vars['_status'] = array('code'=>1,'msg'=>$vars['lang']['route_removed']);
                else
                    $vars['_status'] = array('code'=>0,'msg'=>$api->error());
            } else {
                $vars['_status'] =  array('code'=>0,'msg'=>'Resource not found!');
            }


        break;
        case 'addroute':
            if(isset($_POST['route']['hostname']) && isset($_POST['route']['port']))
            {
                $hostname = $_POST['route']['hostname'];
                $port     = $_POST['route']['port'];
                $validHost = kwspamexperts_isHostname($hostname) || filter_var($hostname, FILTER_VALIDATE_IP) !== false;
                if ($port !== '') {
                    $port = filter_var($port, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 65535)));
                }
                if (!$validHost || $port === false) {
                    $vars['_status'] = array('code'=>0,'msg'=>$vars['lang']['invalid_route']);
                    break;
                }

                foreach($routes['result'] as $key => $val)
                {
                    $routes['result'][$key] = str_replace('::', ':', $val);
                }

                $routes['result'][] = $port === '' ? $hostname : $hostname.":".$port;
                $api ->call("domain/edit/domain/".$domain."/destinations/".rawurlencode(json_encode($routes['result']))."/");
                if($api->isSuccess())
                    $vars['_status'] = array('code'=>1,'msg'=>$vars['lang']['route_added']);
                else 
                    $vars['_status'] = array('code'=>0,'msg'=>$api->error());
            }
        break;

    }

    return;
    }

    $vars['domainroutes'] = array();
    $api ->call("domain/getroute/domain/".$domain."/");
    if ($api->isSuccess())
    {
        $data                 = $api->getResponse();

        foreach($data['result'] as $key => $val){
            $vars['domainroutes'][] = explode(':',$val);
        }

    } else {
        $vars['_status']       = array('code'=>0,'msg'=>$api->error());
    } 
}else {   
    $vars['_status']       = array('code'=>2,'msg'=>$vars['lang']['route_denied']);
}
