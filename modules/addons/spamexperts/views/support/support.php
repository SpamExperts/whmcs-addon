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
 * Support view, loaded by core.php after pages/support/support.php.
 *
 * @var array $CONFIG WHMCS configuration
 * @var array $addon Addon module row with the installed version
 * @var array $curl curl_version() output
 * @var kwspamexperts_api $api
 * @var array|null $spam SpamExperts version response
 *
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

$serversDir = ROOTDIR . DS . 'modules' . DS . 'servers' . DS;
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
$hasServerModule = file_exists($serversDir . 'kwspamexperts' . DS . 'kwspamexperts.php');
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
$hasResellerModule = file_exists($serversDir . 'spamexpertsreseller' . DS . 'spamexpertsreseller.php');
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="2">Information</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="50%">WHMCS Version</td>
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
            <td><?php echo htmlentities($spam['result'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
        </tr>
    </tbody>
</table>

<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="2">Diagnostic</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="50%">WHMCS Version</td>
            <td>
                <?php if (version_compare($CONFIG['Version'], '5.0.0', '<')) { ?>
                    <span class="text-danger">Required Version >= 5.0.0</span>
                <?php } else { ?>
                    <span class="text-success">OK</span>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>PHP Version</td>
            <td>
                <?php if (version_compare(PHP_VERSION, '5.0.0', '<')) { ?>
                    <span class="text-danger">Required Version >= 5.0.0</span>
                <?php } else { ?>
                    <span class="text-success">OK</span>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>SpamExperts API Connection</td>
            <td>
                <?php if ($api->isSuccess()) { ?>
                    <span class="text-success">OK</span>
                <?php } else { ?>
                    <span class="text-danger">
                        <?php echo htmlentities($api->error(), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>SpamExperts Module</td>
            <td>
                <?php if ($hasServerModule) { ?>
                    <span class="text-success">OK</span>
                <?php } else { ?>
                    <span class="text-danger">Module not exists.</span>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>SpamExperts Reseller Module</td>
            <td>
                <?php if ($hasResellerModule) { ?>
                    <span class="text-success">OK</span>
                <?php } else { ?>
                    <span class="text-danger">Module not exists.</span>
                <?php } ?>
            </td>
        </tr>
    </tbody>
</table>
