<?php

class spamexperts
{
    //Module Name
    public $name = 'SpamExperts Addon';

    //System Name
    public $system_name = 'SpamExperts Addon';

    //Module Description
    // phpcs:ignore Generic.Files.LineLength.TooLong
    public $description = 'Allows to simply create products, test connection with SpamExperts API and show WHMCS server configuration.';

    //Module Version
    public $version = '2.0';

    //Module Author
    public $author = '<a href="https://spamexperts.com" target="_blank">SpamExperts.com</a>';

    //Default Page
    public $default_page = 'main';

    //Top Menu
    public $top_menu =  [
        'main'                  => [
            'title'             => 'Configuration',
        ],
        'setupProducts'         =>  [
            'title'             =>  'Setup Products',
        ],
        'support'               =>  [
            'title'             =>  'Support',
        ],
    ];


    //Enable PHP Debug Info
    public $debug = 0;

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

    public function upgrade()
    {
    }
}
