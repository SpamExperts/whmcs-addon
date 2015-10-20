<?php

//SOME DEFINES
defined('DS') ? null : define('DS',DIRECTORY_SEPARATOR);
defined('WHMCS_MAIN_DIR') ? null : define('WHMCS_MAIN_DIR', substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'modules'.DS.'addons')));
//Use module in admin mode
$IS_CLIENTAREA = false;

/**
 * Just change function name. Do not edit anything more.
 */ 


function spamexperts_config()
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';
    
    require_once dirname(__FILE__).DS.'config.php';
    
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
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';
    
    require_once dirname(__FILE__).DS.'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->activate();
}

function spamexperts_deactivate()
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';
    
    require_once dirname(__FILE__).DS.'config.php';

    $module = spamexperts_getModuleClass(__FILE__);
    $MGC = new $module();
    $MGC->deactivate();
}


function spamexperts_upgrade($vars)
{
    //SOME USEFUL STUFF
    require_once dirname(__FILE__).DS.'core'.DS.'functions.php';
    
    require_once dirname(__FILE__).DS.'config.php';

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
    require_once dirname(__FILE__).DS.'config.php';
    require_once dirname(__FILE__).DS.'core'.DS.'output.php';
}

function spamexperts_clientarea($vars) 
{
    //Enable Client Area
    global $IS_CLIENTAREA;
    $IS_CLIENTAREA = true;
    
    require_once dirname(__FILE__).DS.'config.php';
    $content = require_once dirname(__FILE__).DS.'core'.DS.'output_client.php';
    
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
    $dirname = dirname($file);
    $basename = basename($dirname);
    return $basename;
}
?>