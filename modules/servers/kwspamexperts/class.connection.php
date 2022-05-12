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
class kwspamexperts_api
{
    private $error = null;
    private $url = null;
    private $user = null;
    private $pass = null;
    private $response = null;
    private $debug = true;

    public function __construct($params)
    {
        $this->url = $params['configoption2'];
        $this->user = html_entity_decode($params['configoption3']);
        $this->pass = html_entity_decode($params['configoption4']);
    }

    public function call($action, $format = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . "/api/" . $action . ($format ? "/format/json/" : ""));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->pass);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnFilesystem
        $this->response = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $hasErrorMessage = isset($this->response['messages']['error'][0]);
        $genericError = 'Something went wrong with this service. Please contact your admin to check its configuration.';
        $apiError = $hasErrorMessage ? $this->response['messages']['error'][0] : $genericError;

        $this->error = null;
        if ($http_code !== 200 || $hasErrorMessage) {
            $this->error = $apiError;
            if ($curl_error) {
                $this->error .= ' ' . $curl_error;
            }
        }

        if ($this->debug) {
            logModuleCall('spamexpertsresller', $action, $this->url . "/api/" . $action . ($format ? "/format/json/" : ""), print_r($this->response, true));
        }
    }

    public function isError()
    {
        return isset($this->error);
    }

    public function isSuccess()
    {
        return !isset($this->error);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function error()
    {
        return $this->error;
    }


    public function redirect($url)
    {
        ob_clean();
        header("Location: " . $url);
        die();
    }
}
