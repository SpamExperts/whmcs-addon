<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-11, 13:45:26)
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

$reseller_id = 0;

$api->call('/reseller/list/');

if($api->isSuccess())
{
    $resellers = $api->getResponse();

    if (isset($resellers['result'][0]))
    {
        foreach($resellers['result'][0] as $val) {

            if($val['username'] == $params['username'])
            {
                $reseller_id = $val['id'];
                break;
            }
        }
    }
}

if ($reseller_id)
{
    $api->call('/reseller/getbandwidthusage/reseller_id/'.$reseller_id);
    if($api->isSuccess())
    {
        $result = $api->getResponse();
        $lines  = array_filter(explode("\n",$result['result']));
        $data   = array();

        foreach($lines as $val)
        {
            $data[]      = explode(',',$val);
        }

        $vars['data']    = $data;
    } else {
        $vars['_status'] = array('code'=>0,'msg'=>$api->error());
    }
} else {
    $vars['_status'] = array('code'=>0,'msg'=>"No reseller found");
}

