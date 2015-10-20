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
    $api ->call("domain/getroute/domain/".$domain."/");
    $routes = $api->getResponse();
    switch($_REQUEST['_action'])
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
            } else if(isset($_GET['routetodel']))
            {
                foreach($routes['result'] as $key => $val){
                    if($val == $_GET['routetodel'].'::'.$_GET['routeporttodel'])
                    {
                        $found = true;
                        unset($routes['result'][$key]);
                        break;
                    }  
                }
            }


            if($found){
                $api ->call("domain/edit/domain/".$domain."/destinations/".str_replace('::',':',json_encode($routes['result']))."/");

                if($api->isSuccess())
                    $_SESSION['spam_status'] = array('code'=>1,'msg'=>$vars['lang']['route_removed']);
                else
                    $_SESSION['spam_status'] = array('code'=>0,'msg'=>$api->error());

                $api->redirect("clientarea.php?action=productdetails&id=".$params['serviceid']."&modop=custom&a=management&page=ManageRoutes");         
            } else {
                $vars['_status'] =  array('code'=>0,'msg'=>'Resource not found!');
            }


        break;
        case 'addroute':
            if(isset($_POST['route']['hostname']) && isset($_POST['route']['port']))
            {
                foreach($routes['result'] as $key => $val)
                {
                    $routes['result'][$key] = str_replace('::', ':', $val);
                }

                $routes['result'][] = $_POST['route']['hostname'].":".$_POST['route']['port'];
                $api ->call("domain/edit/domain/".$domain."/destinations/".json_encode($routes['result'])."/");
                if($api->isSuccess())
                    $_SESSION['spam_status'] = array('code'=>1,'msg'=>$vars['lang']['route_added']);
                else 
                    $_SESSION['spam_status'] = array('code'=>0,'msg'=>$api->error());

                $api->redirect("clientarea.php?action=productdetails&id=".$params['serviceid']."&modop=custom&a=management&page=ManageRoutes");
            }
        break;

    }



    $api ->call("domain/getroute/domain/".$domain."/");
    if ($api->isSuccess())
    {
        $vars['domainroutes'] = array();
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
