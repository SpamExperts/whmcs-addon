<?php

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use WHMCS\Database\Capsule;

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

if(isset($_POST['action']) && $_POST['action'] === 'addProduct')
{
    try {
        if (!Capsule::table('tblproductgroups')->where('name', 'SpamExperts')->exists()) {
            Capsule::table('tblproductgroups')
                ->insert([
                    'name' => 'SpamExperts',
                    'created_at' => Carbon::now()
                ]);
        }
    } catch (Exception $e) {
        addError('Product group cannot be set');
    }

    $group_id = Capsule::table('tblproductgroups')->where('name', 'SpamExperts')->value('id');
    $api    = getWHMCSconfig('kwspamexperts_api');
    $data   = unserialize($api);
    try {
        if (empty($_POST['product']['name'])) {
            throw new Exception('Product name cannot be empty');
        }

        Capsule::table('tblproducts')
            ->updateOrInsert(
                [
                    'type' => 'hostingaccount',
                    'gid' => $group_id,
                    'configoption1' => $_POST['product']['type'],
                    'servertype' => 'kwspamexperts',
                    'showdomainoptions' => 'on',
                    'paytype' => 'free',
                ],
                [
                    'name' => $_POST['product']['name'],
                    'configoption2' => $data['url'],
                    'configoption3' => $data['user'],
                    'configoption4' => $data['password'],
                    'configoption5' => $data['disable_manage_routes'] ?: 0,
                    'configoption6' => $data['disable_edit_contact'] ?: 0,
                    'updated_at' => Carbon::now(),
                ]
            );
        addInfo('Product has been configured');
    } catch (QueryException $e) {
        addError("Error when trying to add the product");
    } catch (Exception $e) {
        addError($e->getMessage());
    }
}
