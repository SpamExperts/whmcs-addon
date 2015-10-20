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

if(! $params['configoption6']){
    if(isset($_POST['primaryemail'])){

        $api->call('domaincontact/set/domain/'.$domain.'/email/'.$_POST['primaryemail'].'/');
        if ($api->isSuccess())
            $vars['_status'] = array('code'=>1,'msg'=>$vars['lang']['email_changed']);
        else 
            $vars['_status'] = array('code'=>0,'msg'=>$api->error());
    }

    if(isset($_POST['adminemail'])){
        $api->call('domainadmincontact/set/domain/'.$domain.'/email/'.$_POST['adminemail'].'/');
        if ($api->isSuccess())
            $vars['_status'] = array('code'=>1,'msg'=>$vars['lang']['email_changed']);
        else 
            $vars['_status'] = array('code'=>0,'msg'=>$api->error());
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
} else {   
    $vars['_status'] = array('code'=>2,'msg'=>$vars['lang']['email_denied']);
}
