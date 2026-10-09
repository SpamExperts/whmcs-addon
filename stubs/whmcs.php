<?php

/**
 * Editor stubs for the WHMCS symbols the modules use.
 *
 * WHMCS ships them ionCube encoded or in its own vendor directory, so IDEs
 * can't index them from this repository. Dev only: never loaded, and left
 * out of the release archive by .gitattributes.
 */

/**
 * WHMCS installation directory
 */
const ROOTDIR = '';

/**
 * @disregard P1003 Stub, the parameters are unused
 * @param string $hookPoint
 * @param int $priority
 * @param callable $function
 * @return void
 */
function add_hook($hookPoint, $priority, $function)
{
}

/**
 * @disregard P1003 Stub, the parameters are unused
 * @param string $module
 * @param string $action
 * @param string|array $requestString
 * @param string|array $responseData
 * @param string|array $processedData
 * @param array $replaceVars Values to mask in the log
 * @return void
 */
function logModuleCall($module, $action, $requestString, $responseData, $processedData = '', $replaceVars = [])
{
}

/**
 * @return string
 */
function createServerPassword()
{
    return '';
}

/**
 * @disregard P1003 Stub, the parameters are unused
 * @param string $string
 * @return string
 */
function encrypt($string)
{
    return '';
}

/**
 * @disregard P1003 Stub, the parameters are unused
 * @param string $type 'form' for a hidden input, 'plain' for the bare token
 * @return string
 */
function generate_token($type = 'form')
{
    return '';
}
