<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 11:55:29)
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
?>
<table class="table">
    <thead>
        <tr>
            <th colspan="2">Information</th>
        </tr>
    </thead>
    <tr>
        <td>WHMCS Version</td>
        <td><?php echo htmlentities($CONFIG['Version'], ENT_QUOTES, 'UTF-8');?></td>
    </tr>
    <tr>
        <td>PHP Version</td>
        <td><?php echo htmlentities(PHP_VERSION, ENT_QUOTES, 'UTF-8');?></td>
    </tr>
    <tr>
        <td>Curl Version</td>
        <td><?php echo htmlentities($curl['version'], ENT_QUOTES, 'UTF-8');?></td>
    </tr>
    <tr>
        <td>Addon Version</td>
        <td><?php echo htmlentities($addon['version'], ENT_QUOTES, 'UTF-8');?></td>
    </tr>
    <tr>
        <td>SpamExperts Version</td>
        <td><?php echo htmlentities($spam['result'], ENT_QUOTES, 'UTF-8');?></td>
    </tr>
    <tr>
        <th colspan="2">Diagnostic</th>
    </tr>
    <tr>
        <td>WHMCS Version</td>
        <td><?php if(version_compare($CONFIG['Version'], '5.0.0', '<')) echo '<span class="error">Required Version >= 5.0.0</span>'; else echo '<span class="success">OK</span>';?></td>
    </tr>
    <tr>
        <td>PHP Version</td>
        <td><?php if(version_compare(PHP_VERSION, '5.0.0', '<')) echo '<span class="error">Required Version >= 5.0.0</span>'; else echo '<span class="success">OK</span>';?></td>
    </tr>
    <tr>
        <td>SpamExperts API Connection</td>
        <td>
            <?php if($api->isSuccess()) { ?>
                <span class="success">OK</span>';
            <?php } else { ?>
                <span class="error">
                    <?php echo htmlentities($api->error(), ENT_QUOTES, 'UTF-8'); ?>
                </span>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>SpamExperts Module</td>
        <td>
            <?php
            // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
            if(file_exists(ROOTDIR.DS.'modules'.DS.'servers'.DS.'kwspamexperts'.DS.'kwspamexperts.php'))
                echo '<span class="success">OK</span>';
            else
                echo '<span class="error">Module not exists.</span>';
            ?>
        </td>
    </tr>
    <tr>
        <td>SpamExperts Reseller Module</td>
        <td>
            <?php
            // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
            if(file_exists(ROOTDIR.DS.'modules'.DS.'servers'.DS.'spamexpertsreseller'.DS.'spamexpertsreseller.php'))
                echo '<span class="success">OK</span>';
            else
                echo '<span class="error">Module not exists.</span>';
            ?>
        </td>
    </tr>
</table>

<style type="text/css">
    .error {color:red;}
    .success {color:green;}
</style>
