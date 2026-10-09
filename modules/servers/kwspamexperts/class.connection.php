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
    /** @var string|null */
    private $error = null;
    /** @var string|null */
    private $url = null;
    /** @var string|null */
    private $user = null;
    /** @var string|null */
    private $pass = null;
    /** @var array|null Decoded API response */
    private $response = null;

    /**
     * @param array $params Module parameters
     */
    public function __construct($params)
    {
        $this->url = $params['configoption2'];
        // WHMCS stores the password field HTML-encoded. Text fields are stored raw
        // on WHMCS 9 but may be encoded on older versions, so decode both.
        $this->user = html_entity_decode($params['configoption3'], ENT_QUOTES);
        $this->pass = html_entity_decode($params['configoption4'], ENT_QUOTES);
    }

    /**
     * @param string $action API path, without the /api/ prefix
     * @param bool $format Append /format/json/ to the path
     * @return void
     */
    public function call($action, $format = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . "/api/" . $action . ($format ? "/format/json/" : ""));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->pass);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);
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

        $secrets = $this->secretsToMask($action);
        // replaceVars is only documented to mask the request and response, so
        // mask the action here too.
        logModuleCall(
            'kwspamexperts',
            str_replace($secrets, '********', $action),
            $this->url . "/api/" . $action . ($format ? "/format/json/" : ""),
            print_r($this->response, true),
            '',
            $secrets
        );
    }

    // Values masked in the module log: the password in the
    // request path and the SSO ticket returned by authticket/create.
    /**
     * @param string $action
     * @return string[]
     */
    private function secretsToMask($action)
    {
        $secrets = array();
        if (preg_match('#/password/([^/]+)#', $action, $match)) {
            $secrets[] = $match[1];
        }
        if (
            strpos($action, 'authticket/') !== false
            && isset($this->response['result'])
            && is_string($this->response['result'])
            && $this->response['result'] !== ''
        ) {
            $secrets[] = $this->response['result'];
        }
        return $secrets;
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
}
