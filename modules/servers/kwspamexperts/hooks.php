<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-03-26, 12:13:56)
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


if (!function_exists('hide_pass_in_clientarea_spamexperts')) {
    function hide_pass_in_clientarea_spamexperts() {
        global $smarty;

        if (!defined('Smarty::SMARTY_VERSION')) {
            if($smarty->_tpl_vars['filename'] == 'clientarea' &&
                (
                    $smarty->_tpl_vars['modulename'] == 'kwspamexperts' ||
                    $smarty->_tpl_vars['modulename'] == 'spamexpertsreseller'
                )
            ) {
                $smarty->_tpl_vars['password'] = '********';
            }
        } else {
            if ($smarty->getVariable('filename')->value == 'clientarea' &&
                (
                    $smarty->getVariable('modulename')->value == 'kwspamexperts' ||
                    $smarty->getVariable('modulename')->value == 'spamexpertsreseller'
                )
            ) {
                $smarty->assign('password', '********');
            }
        }
    }
}

add_hook('ClientAreaHeadOutput', 1, 'hide_pass_in_clientarea_spamexperts');