<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 10:56:37)
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

if(isset($_POST['action']) && $_POST['action'] == 'addProduct')
{
    if(mysql_num_rows(mysql_query_safe("SELECT `id` FROM tblproductgroups WHERE `name`='SpamExperts'"))==0)
    {
        mysql_safequery("INSERT INTO tblproductgroups (`name`) VALUES(?)",array('SpamExperts'));
    } 
    
    $group  = mysql_fetch_assoc(mysql_query("SELECT `id` FROM tblproductgroups WHERE `name`='SpamExperts'"));     
    $api    = getWHMCSconfig('kwspamexperts_api');
    $data   = unserialize($api);
    if(mysql_safequery("INSERT INTO tblproducts 
        (
            `type`,
            `name`,
            `gid`,
            `configoption1`,
            `configoption2`,
            `configoption3`,
            `configoption4`,
            `configoption5`,
            `configoption6`,
            `servertype`,
            `showdomainoptions`,
            `paytype`
        ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)",
            array
            (
                'hostingaccount',
                $_POST['product']['name'],
                $group['id'],
                $_POST['product']['type'],
                $data['url'],
                $data['user'],
                $data['password'],
                $data['disable_manage_routes'],
                $data['disable_edit_contact'],
                'kwspamexperts',
                'on',
                'free'
            )
     ))
        addInfo('Product has been added');
     else
        addError(mysql_error ());

}