<?php
/**********************************************************************
 *  Addon (11.12.12)
 * *
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
 **********************************************************************/

class spamexperts
{
    //Module Name
    public $name = 'SpamExperts Addon';
    
    //System Name
    public $system_name = 'SpamExperts Addon';
    
    //Module Description
    public $description = 'Allows to simply create products, test connection with SpamExperts API and show WHMCS server configuration.';
    
    //Module Version
    public $version = '1.0';
    
    //Module Author
    public $author = '<a href="http://spamexperts.com" target="_blank">SpamExperts.com</a>';
    
    //Default Page
    public $default_page = 'main';
    
    //Default Client Page
    public $default_client_page = 'main';
    
    //Top Menu
    public $top_menu =  array
    (
        'main'                  => array 
        (
            'title'             => 'Configuration',
            'icon'              => 'wrench'
        ),
        'setupProducts'         =>  array
        (
            'title'             =>  'Setup Products',
            'icon'              =>  'tasks',
            
        ), 
        'support'               =>  array
        (
            'title'             =>  'Support',
            'icon'              =>  'magic',
            
        ), 
    );
    
    
    //Enable PHP Debug Info
    public $debug = 0;
    
    //Enable Logger
    public $logger = 1;
    
    /**
     * This function is call when administrator will activate your module
     */
    public function activate()
    {
    }
    
    /**
     * Functions is called when administrator will deactivate your module
     */
    public function deactivate()
    {
    }
    
    public function upgrade($vars)
    {
      
    }
}
?>