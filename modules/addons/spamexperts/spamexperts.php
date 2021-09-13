<?php

//SOME DEFINES
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('WHMCS_MAIN_DIR') or define('WHMCS_MAIN_DIR', substr(__DIR__, 0, strpos(__DIR__, 'modules'.DS.'addons')));
//Use module in admin mode
$IS_CLIENTAREA = false;

/**
 * Just change function name. Do not edit anything more.
 */ 


function spamexperts_config()
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'core'.DS.'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'config.php';
    
    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    return array
    (
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
    require_once __DIR__.DS.'core'.DS.'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->activate();
}

function spamexperts_deactivate()
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'core'.DS.'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->deactivate();
}


function spamexperts_upgrade($vars)
{
    //SOME USEFUL STUFF
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'core'.DS.'functions.php';

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->upgrade();
}

/**
 * Admin area output
 * @param type $vars 
 */
function spamexperts_output($vars)
{
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'config.php';
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'core'.DS.'output.php';
}

function spamexperts_clientarea($vars) 
{
    //Enable Client Area
    global $IS_CLIENTAREA;
    $IS_CLIENTAREA = true;

    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once __DIR__.DS.'config.php';
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    $content = require_once __DIR__.DS.'core'.DS.'output_client.php';
    
    return array(
        'pagetitle'     => 'Addon',
        'breadcrumb'	=> array('index.php?m=NicIT' => 'NicIT'),
        'templatefile'	=> 'core/base',
        'requirelogin'	=> isset($_SESSION['uid']) ? false : true,
        'vars' => array(
            'content'	=> $content,
        ),
    );
 
}


/**** HELPER ****/
function spamexperts_getModuleClass($file)
{
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
    return basename(dirname($file));
}
?>
