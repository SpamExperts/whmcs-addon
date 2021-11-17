<?php
const CORE_DIR = __DIR__;
//GET ROOT DIR 
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
$ROOT_DIR = basename(dirname(__DIR__));

/*************** INCLUDES **********************/
//SOME USEFUL STUFF
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
require_once CORE_DIR.DS.'functions.php';
//PAGINATION INTERFACE
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
require_once CORE_DIR.DS.'class.MG_Pagination.php';
//Include ModulesGarden class

//INCLUDE USER FUNCTIONS IF FILE EXISTS
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
if(file_exists(ADDON_DIR.DS.'core.php'))
{
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once ADDON_DIR.DS.'core.php';
}
//GET MODULE NAME
// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
$module = basename(dirname(ADDON_DIR.DS.$ROOT_DIR));
//CREATE MODULE CONFIG
$MGC = new $module();
//DEFINE MODULE NAME
define('ADDON_NAME', $module);

//ENABLE DEBUG
if(isset($_GET['debug']) && $_GET['debug'] == 1)
{
    $_SESSION['MODULESGARDEN_DEBUG'] = 1;
}

//DISABLE DEBUG
if(isset($_GET['debug']) && $_GET['debug'] == 0)
{
    $_SESSION['MODULESGARDEN_DEBUG'] = 0;
}

if((isset($_SESSION['MODULESGARDEN_DEBUG']) && $_SESSION['MODULESGARDEN_DEBUG']) || $MGC->debug)
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

//IS AJAX?
$AJAX = isset($_REQUEST['ajax']) ? 1 : 0;

//FIND PAGE FILES
$PAGE = isset($_REQUEST['modpage']) ? $_REQUEST['modpage'] : ($IS_CLIENTAREA ? $MGC->default_client_page : $MGC->default_page);
if($AJAX)
{
    $PAGE_FILE = 'ajax.pages'.DS.$PAGE . '.php';
}
elseif($IS_CLIENTAREA)
{
    $PAGE_FILE = 'pages_client'.DS.$PAGE.'.php';
}
else
{
    $PAGE_FILE = 'pages'.DS.$PAGE.'.php';
}
//IS IT AJAX REQUEST? JUST INCLUDE CONTROLLER FILE AND VIEW
if($AJAX)
{
    //PAGINATION REQUEST?
    if($_REQUEST['pagination'] == 1)
    {

        $p = new MG_Pagination($_REQUEST['parent']);
        if(isset($_REQUEST['get']))
        {
            // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
            echo $p->getPagination();
            die();
        }
        elseif(isset($_REQUEST['order_by']))
        {
            $p->setOrderBy($_REQUEST['order_by'], 'ASC');
        }
        if(isset($_REQUEST['check']))
        {
            // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
            echo json_encode(array
            (
                'next'      =>  $p->isNext(),
                'prev'      =>  $p->isPrev(),
                'current'   =>  $p->getCurrentPage(),
            ));
            die();
        }
        elseif(isset($_REQUEST['reset']))
        {
            $p->resetFilter();
        }
        elseif(isset($_REQUEST['next']))
        {
            $p->next();
        }
        elseif(isset($_REQUEST['prev']))
        {
            $p->prev();
        }
        elseif(isset($_REQUEST['page']))
        {
            $p->setPage($_REQUEST['page']);
        }
        else
        {
            foreach($_REQUEST['filters'] as $field_name => $field_value)
            {
                if($field_value)
                {
                    $p->addFilter($field_name, $field_value);
                }
                else
                {
                    $p->removeFilter($field_name);
                }
                $p->setPage(0);
            }
        }
        //$p->resetFilter();
        $p->__destruct();
    }
    // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatch,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
    require_once ADDON_DIR.DS.$PAGE_FILE;
    // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
    if(file_exists(ADDON_DIR.DS.'ajax.views'.DS.$PAGE.'.php'))
    {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatch,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        include_once ADDON_DIR.DS.'ajax.views'.DS.$PAGE.'.php';
    }
    exit;
}

//NORMAL REQUEST?
//GET MENUS
$TOP_MENU = $MGC->top_menu;
$SIDE_MENU = $MGC->side_menu;

//ASSETS DIR
if($IS_CLIENTAREA)
{
    $ASSETS_DIR = 'modules'.DS.'addons'.DS.$ROOT_DIR.DS.'core'.DS.'assets';
}
else
{
    $ASSETS_DIR = '..'.DS.'modules'.DS.'addons'.DS.$ROOT_DIR.DS.'core'.DS.'assets';
}
//MODULE URL
$MODULE_URL = 'addonmodules.php?module='.$_GET['module'];

$PAGE_HEADING = null;
$PAGE_MODULE_HEADING = null;
$PAGE_SUBMODULE_HEADING = null;
//GET PAGE CONTENT
ob_start();

global $lerror;

$available_pages = array('main', 'setupProducts', 'support');

if(!$lerror)
{
    //CONTROLLER
    // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
    if (!$IS_CLIENTAREA && isset($_REQUEST['modsubpage'])
        && in_array($PAGE, $available_pages)
        && in_array($_REQUEST['modsubpage'], $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI,PHPCS_SecurityAudit.BadFunctions.EasyRFI.ErrEasyRFI
        require ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php';
    } elseif ($IS_CLIENTAREA && isset($_REQUEST['modsubpage'])
        && in_array($PAGE, $available_pages)
        && in_array($_REQUEST['modsubpage'], $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages_client' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI,PHPCS_SecurityAudit.BadFunctions.EasyRFI.ErrEasyRFI
        require ADDON_DIR . DS . 'pages_client' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php';
    } elseif (!$IS_CLIENTAREA
        && in_array($PAGE, $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $PAGE . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require ADDON_DIR . DS . 'pages' . DS . $PAGE . DS . $PAGE . '.php';
    } elseif ($IS_CLIENTAREA
        && in_array($PAGE, $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages_client' . DS . $PAGE . DS . $PAGE . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require ADDON_DIR . DS . 'pages_client' . DS . $PAGE . DS . $PAGE . '.php';
    } else {
        die('Page does not exist!');
    }

    //LOCATE VIEW FILE AND LOAD IT
    if (!$IS_CLIENTAREA && isset($_REQUEST['modsubpage'])
        && in_array($PAGE, $available_pages)
        && in_array($_REQUEST['modsubpage'], $available_pages)
        && file_exists(ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI,PHPCS_SecurityAudit.BadFunctions.EasyRFI.ErrEasyRFI
        require ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php';
    } elseif ($IS_CLIENTAREA && isset($_REQUEST['modsubpage'])
        && in_array($PAGE, $available_pages)
        && in_array($_REQUEST['modsubpage'], $available_pages)
        && file_exists(ADDON_DIR . DS . 'pages_views' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI,PHPCS_SecurityAudit.BadFunctions.EasyRFI.ErrEasyRFI
        require ADDON_DIR . DS . 'views_client' . DS . $PAGE . DS . $_REQUEST['modsubpage'] . '.php';
    } elseif (!$IS_CLIENTAREA
        && in_array($PAGE, $available_pages)
        && file_exists(ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $PAGE . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require ADDON_DIR . DS . 'views' . DS . $PAGE . DS . $PAGE . '.php';
    } elseif ($IS_CLIENTAREA
        && in_array($PAGE, $available_pages)
        && file_exists(ADDON_DIR . DS . 'views_client' . DS . $PAGE . DS . $PAGE . '.php')
    ) {
        // phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
        require ADDON_DIR . DS . 'views_client' . DS . $PAGE . DS . $PAGE . '.php';
    }
    // phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
} else {
    addError($lerror);
}

$CONTENT = ob_get_clean();
?>
