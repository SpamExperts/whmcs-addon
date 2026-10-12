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
    $moduleConfig = new $module();
    return array(
        'name'          =>  $moduleConfig->system_name,
        'description'   =>  $moduleConfig->description,
        'version'       =>  $moduleConfig->version,
        'author'        =>  $moduleConfig->author
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
    $moduleConfig = new $module();
    $moduleConfig->activate();
}

function spamexperts_deactivate()
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $moduleConfig = new $module();
    $moduleConfig->deactivate();
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
    $moduleConfig = new $module();
    $moduleConfig->upgrade();
}

/**
 * Admin area output
 * @param array $vars
 */
function spamexperts_output($vars)
{
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'config.php';
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__ . DS . 'core' . DS . 'output.php';
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
