<?php

use Carbon\Carbon;
use WHMCS\Database\Capsule;

//define addons main dir
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
defined('ADDON_DIR') or define('ADDON_DIR', dirname(__DIR__));

if(!function_exists('getWHMCSconfig'))
{
    function getWHMCSconfig($k) 
    {
        try {
            $settings = Capsule::table('tblconfiguration')
                ->where('setting', $k)
                ->first();
            $config = $settings->value;
        } catch (\Exception $e) {
            addError("Cannot read configuration.");
            $config = '';
        }
        return $config;
    }
}

if(!function_exists('addError'))
{
    function addError($error)
    {
        $_SESSION[ADDON_NAME]['errors'][] = $error;
    }
}


if(!function_exists('addInfo'))
{
    function addInfo($info)
    {
        $_SESSION[ADDON_NAME]['infos'][] = $info;
    }
}

if(!function_exists('getErrors'))
{
    function getErrors()
    {
        $errors = $_SESSION[ADDON_NAME]['errors'];
        $_SESSION[ADDON_NAME]['errors'] = null;
        return $errors;
    }
}

if(!function_exists('getInfos'))
{
    function getInfos()
    {
        $infos = $_SESSION[ADDON_NAME]['infos'];
        $_SESSION[ADDON_NAME]['infos'] = null;
        return $infos;
    }
}

if(!function_exists('getAddonUrl'))
{
    function getAddonUrl($modpage = null, $modsubpage = null)
    {
        global $IS_CLIENTAREA;
        if($IS_CLIENTAREA)
            return 'index.php?m=' . ADDON_NAME.($modpage ? '&modpage=' . $modpage : '') . ($modsubpage ? '&modsubpage=' . $modsubpage : '');
        else
            return 'addonmodules.php?module=' . ADDON_NAME . ($modpage ? '&modpage=' . $modpage : '') . ($modsubpage ? '&modsubpage=' . $modsubpage : '');
    }
}


if(!function_exists('saveWHMCSconfig'))
{
    function saveWHMCSconfig($k, $v)
    {
        try {
            Capsule::table('tblconfiguration')
                ->updateOrInsert(
                    ['setting' => $k],
                    ['value' => $v, 'updated_at' => Carbon::now()]
                );
            addInfo('Configuration has been saved.');
            $saved = true;
        } catch (\Exception $e) {
            addError("Configuration update failed.");
            $saved = false;
        }
        return $saved;
    }
}

if(!function_exists('updateProductsConfig')){
    function updateProductsConfig($c)
    {
        $array = unserialize($c);

        if (!isset($array['disable_manage_routes'])) {
            $array['disable_manage_routes'] = '0';
        }

        if (!isset($array['disable_edit_contact'])) {
            $array['disable_edit_contact'] = '0';
        }

        try {
            Capsule::table('tblproducts')
                ->where('servertype', 'kwspamexperts')
                ->update([
                    'configoption2' => $array['url'],
                    'configoption3' => $array['user'],
                    'configoption4' => $array['password'],
                    'configoption5' => $array['disable_manage_routes'],
                    'configoption6' => $array['disable_edit_contact'],
                    'updated_at' => Carbon::now(),
                ]);
            $updated = true;
        } catch (\Exception $e) {
            addError("Products configuration update failed.");
            $updated = false;
        }
        return $updated;
    }
}
