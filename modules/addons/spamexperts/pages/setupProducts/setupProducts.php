<?php

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

if (isset($_POST['action']) && $_POST['action'] === 'addProduct') {
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
    $data   = getApiSettings();
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
                    'configoption5' => $data['disable_manage_routes'] ?? 0,
                    'configoption6' => $data['disable_edit_contact'] ?? 0,
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
