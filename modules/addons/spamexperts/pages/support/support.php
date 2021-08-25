<?php

use WHMCS\Database\Capsule;

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 11:55:42)
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

// phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
include_once(ROOTDIR.DS.'modules'.DS.'servers'.DS.'kwspamexperts'.DS.'class.connection.php');
global $CONFIG;
$addon = (array)Capsule::table('tbladdonmodules')
    ->select('value as version')
    ->where('module', 'spamexperts')
    ->first();
$api   = getWHMCSconfig('kwspamexperts_api');
$data  = unserialize($api);
$curl  = curl_version();

$api   = new kwspamexperts_api(
        array(
            'configoption2' => $data['url'],
            'configoption3' => $data['user'],
            'configoption4' => $data['password']
            )
       );
$api->call('/version/get/');

$spam = $api->getResponse();
