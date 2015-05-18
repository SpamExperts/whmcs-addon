<?php
define('CORE_DIR', dirname(__FILE__));
//GET ROOT DIR 
$ROOT_DIR = dirname(__FILE__);
$ROOT_DIR = substr($ROOT_DIR, 0, strrpos($ROOT_DIR, DS));
$ROOT_DIR = substr($ROOT_DIR, strrpos($ROOT_DIR, DS), strlen($ROOT_DIR));

/*************** INCLUDES **********************/
//SOME USEFUL STUFF
require_once CORE_DIR.DS.'functions.php';
//PAGINATION INTERFACE
require_once CORE_DIR.DS.'class.MG_Pagination.php';
//MG_Langs
require_once CORE_DIR.DS.'class.MG_Lang.php';
//Validation class
require_once CORE_DIR.DS.'class.MG_Validation.php';
//Include ModulesGarden class
require_once CORE_DIR.DS.'class.ModulesGarden.php';

//INCLUDE USER FUNCTIONS IF FILE EXISTS
if(file_exists(ADDON_DIR.DS.'core.php'))
{
    require_once ADDON_DIR.DS.'core.php';
}
//GET MODULE NAME
$module = ModulesGarden::getModuleClass(ADDON_DIR.DS.$ROOT_DIR);
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
            echo $p->getPagination();
            die();
        }
        elseif(isset($_REQUEST['order_by']))
        {
            $p->setOrderBy($_REQUEST['order_by'], 'ASC');
        }
        if(isset($_REQUEST['check']))
        {
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
    require_once ADDON_DIR.DS.$PAGE_FILE;
    if(file_exists(ADDON_DIR.DS.'ajax.views'.DS.$PAGE.'.php'))
    {
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

if(!$lerror)
{
    //CONTROLLER
    if (!$IS_CLIENTAREA && isset($_REQUEST['modsubpage']) && file_exists(ADDON_DIR.DS.'pages'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php'))
    {
            require ADDON_DIR.DS.'pages'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php';

    }
    elseif ($IS_CLIENTAREA && isset($_REQUEST['modsubpage']) && file_exists(ADDON_DIR.DS.'pages_client'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php'))
    {
        require ADDON_DIR.DS.'pages_client'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php';

    } 
    elseif (!$IS_CLIENTAREA && file_exists(ADDON_DIR.DS.'pages'.DS.$PAGE.DS.$PAGE.'.php'))
    {
            require ADDON_DIR.DS.'pages'.DS.$PAGE.DS.$PAGE.'.php';
    }
    elseif ($IS_CLIENTAREA && file_exists(ADDON_DIR.DS.'pages_client'.DS.$PAGE.DS.$PAGE.'.php'))
    {
        require ADDON_DIR.DS.'pages_client'.DS.$PAGE.DS.$PAGE.'.php';
    }
    else 
    {
        die('Page does not exists!');
    }

        //LOCATE VIEW FILE AND LOAD IT
    if (!$IS_CLIENTAREA && isset($_REQUEST['modsubpage']) && file_exists(ADDON_DIR.DS.'views'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php'))
    {
            require ADDON_DIR.DS.'views'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php';
    }
    elseif($IS_CLIENTAREA && isset($_REQUEST['modsubpage']) && file_exists(ADDON_DIR.DS.'pages_views'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php'))
    {
        require ADDON_DIR.DS.'views_client'.DS.$PAGE.DS.$_REQUEST['modsubpage'].'.php';
    }
    elseif (!$IS_CLIENTAREA && file_exists(ADDON_DIR.DS.'views'.DS.$PAGE.DS.$PAGE.'.php'))
    {
        require ADDON_DIR.DS.'views'.DS.$PAGE.DS.$PAGE.'.php';
    }
    elseif ($IS_CLIENTAREA && file_exists(ADDON_DIR.DS.'views_client'.DS.$PAGE.DS.$PAGE.'.php'))
    {
            require ADDON_DIR.DS.'views_client'.DS.$PAGE.DS.$PAGE.'.php';
    }
}
else
{
    addError($lerror);
}

$CONTENT = ob_get_clean();
?>
