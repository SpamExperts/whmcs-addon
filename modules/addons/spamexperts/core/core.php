<?php

/**
 * Page router, loaded by output.php.
 */

//GET ROOT DIR
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
$ROOT_DIR = basename(dirname(__DIR__));

/*************** INCLUDES **********************/
//SOME USEFUL STUFF
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
require_once __DIR__ . DS . 'functions.php';
//Include ModulesGarden class

//INCLUDE USER FUNCTIONS IF FILE EXISTS
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
if (file_exists(ADDON_DIR . DS . 'core.php')) {
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once ADDON_DIR . DS . 'core.php';
}
//GET MODULE NAME
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
$module = basename(dirname(ADDON_DIR . DS . $ROOT_DIR));
//CREATE MODULE CONFIG
$MGC = new $module();

//ENABLE DEBUG
if ($MGC->debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

//CHECK CSRF TOKEN. WHMCS adds it to the admin forms but doesn't check it for addon pages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_token('WHMCS.admin.default');
}

//IS AJAX?
$AJAX = isset($_REQUEST['ajax']) ? 1 : 0;

//FIND PAGE FILES
$PAGE = isset($_REQUEST['modpage']) ? $_REQUEST['modpage'] : $MGC->default_page;
$available_pages = array('main', 'setupProducts', 'support');

//IS IT AJAX REQUEST? JUST INCLUDE CONTROLLER FILE AND VIEW
if ($AJAX) {
    if (
        !in_array($PAGE, $available_pages, true)
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        || !file_exists(ADDON_DIR . DS . 'ajax.pages' . DS . $PAGE . '.php')
    ) {
        die('Page does not exist!');
    }
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatch,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once ADDON_DIR . DS . 'ajax.pages' . DS . $PAGE . '.php';
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
    if (file_exists(ADDON_DIR . DS . 'ajax.views' . DS . $PAGE . '.php')) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatch,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        include_once ADDON_DIR . DS . 'ajax.views' . DS . $PAGE . '.php';
    }
    exit;
}

//NORMAL REQUEST?
//GET MENU
$TOP_MENU = $MGC->top_menu;

//ASSETS DIR
$ASSETS_DIR = '..' . DS . 'modules' . DS . 'addons' . DS . $ROOT_DIR . DS . 'core' . DS . 'assets';
//MODULE URL
$MODULE_URL = 'addonmodules.php?module=' . $_GET['module'];

$PAGE_HEADING = null;
$PAGE_MODULE_HEADING = null;
$PAGE_SUBMODULE_HEADING = null;
//GET PAGE CONTENT
ob_start();

global $lerror;

if (!$lerror) {
    //CONTROLLER
    // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
    if (
        isset($_REQUEST['modsubpage'])
        && in_array($PAGE, $available_pages)
        && in_array($_REQUEST['modsubpage'], $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI,PHPCS_SecurityAudit.BadFunctions.EasyRFI.ErrEasyRFI
        require ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php';
    } elseif (
        in_array($PAGE, $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $PAGE . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $PAGE . '.php';
    } else {
        die('Page does not exist!');
    }

    //LOCATE VIEW FILE AND LOAD IT
    if (
        isset($_REQUEST['modsubpage'])
        && in_array($PAGE, $available_pages)
        && in_array($_REQUEST['modsubpage'], $available_pages)
        && file_exists(ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI,PHPCS_SecurityAudit.BadFunctions.EasyRFI.ErrEasyRFI
        require ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php';
    } elseif (
        in_array($PAGE, $available_pages)
        && file_exists(ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $PAGE . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $PAGE . '.php';
    }
    // phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
} else {
    addError($lerror);
}

$CONTENT = ob_get_clean();
