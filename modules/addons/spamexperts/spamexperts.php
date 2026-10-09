<?php

//SOME DEFINES
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * Just change function name. Do not edit anything more.
 */


function spamexperts_config()
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    return array(
        'name'          =>  $MGC->system_name,
        'description'   =>  $MGC->description,
        'version'       =>  $MGC->version,
        'author'        =>  $MGC->author
    );
}

function spamexperts_activate()
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->activate();
}

function spamexperts_deactivate()
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->deactivate();
}


/**
 * Addon upgrade
 * @param array $vars
 */
function spamexperts_upgrade($vars)
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->upgrade();
}

/**
 * Admin area output
 * @param array $vars
 */
function spamexperts_output($vars)
{
    //Use module in admin mode
    global $IS_CLIENTAREA;
    $IS_CLIENTAREA = false;

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'output.php';
}

/**
 * Client area output
 * @param array $vars
 * @return array
 */
function spamexperts_clientarea($vars)
{
    //Enable Client Area
    global $IS_CLIENTAREA;
    $IS_CLIENTAREA = true;

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    $content = require_once __DIR__ . DS . 'core' . DS . 'output_client.php';

    return array(
        'pagetitle'     => 'Addon',
        'breadcrumb'    => array('index.php?m=NicIT' => 'NicIT'),
        'templatefile'  => 'core/base',
        'requirelogin'  => isset($_SESSION['uid']) ? false : true,
        'vars' => array(
            'content'   => $content,
        ),
    );
}


/**** HELPER ****/
/**
 * Module class name, the name of the addon directory
 * @param string $file
 * @return string
 */
function spamexperts_getModuleClass($file)
{
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
    return basename(dirname($file));
}
