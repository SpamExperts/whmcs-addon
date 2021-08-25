<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved 
 * (2014-02-10, 10:27:46)
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
 * @author Maciej Husak <maciej@modulesgarden.com>
 */


class spamexperts_api 
{
    private $error      = null;
    private $url        = null;
    private $user       = null;
    private $pass       = null;
    private $action     = null;
    private $response   = null;
    private $http_code  = null;
    private $debug      = true;
    
    
    function __construct($params, $user = false)
    {
        $this->url      = $params['configoption1'];
        if($user)
        {
            $this->user     = $params['username'];
            $this->pass     = $params['password'];
        } else 
        {
            $this->user     = $params['configoption2'];
            $this->pass     = $params['configoption3'];
        }
    }

    public function call($action)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url."/api/".$action."/format/json/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.":".$this->pass);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        $this->response     = json_decode(curl_exec($ch),true);
        $this->http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->curl_error   = curl_error($ch);

        if($this->debug)
        {
            logModuleCall('spamexpertsresller',$action, $this->url."/api/".$action."/format/json/", print_r($this->response,true));
        }

        if(isset($this->response['messages']['error'][0]))
            $this->error = $this->response['messages']['error'][0];
        curl_close($ch);
    }
    
    public function isError()
    {   

        if (isset($this->response['messages']['error'][0])) 
        {
            $this->error = $this->response['messages']['error'][0];
            return true;
        }      
        else 
        {
            return false;
        }
    }
    
    public function isSuccess()
    {
        if($this->http_code == 200 && !isset($this->response['messages']['error'][0]))
            return true;
        else 
        {
            $result         = $this->getResponse();
            $this->error    = $this->response['messages']['error'][0].' '.$this->curl_error;
            return false;
        }
                
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function error(){
        return $this->error;
    }
   
    
    public function redirect($url)
    {
        ob_clean();
        header("Location: ".$url);
        die();
    }
}
