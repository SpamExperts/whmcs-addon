<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 10:23:59)
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

if(isset($_POST['action']) && $_POST['action'] == 'testconnection')
{
    include_once(ROOTDIR.DS.'modules'.DS.'servers'.DS.'kwspamexperts'.DS.'class.connection.php');

    $api = new kwspamexperts_api(
            array(
                'configoption2' => $_POST['url'],
                'configoption3' => $_POST['user'],
                'configoption4' => $_POST['password']
                )
           );

    $api->call('/version/get/');
    
    if($api->isSuccess())
        die('success');
    else
        die($api->error ());
    
}