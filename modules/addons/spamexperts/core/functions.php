<?php
//define addons main dir
defined('ADDON_DIR') ? null: define('ADDON_DIR', dirname(dirname(__FILE__)));

if(function_exists('mysql_safequery') == false) {
    function mysql_safequery($query,$params=false) {
        if ($params) {
            foreach ($params as &$v) { $v = mysql_real_escape_string($v); }
            $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
            $sql_query = mysql_query($sql_query);
        } else {
            $sql_query = mysql_query($query);
        }
        
        return ($sql_query);
    }
}

if(function_exists('mysql_query_safe') == false) {
	/**
	 * This is better version of mysql_safequery
	 * @param string $query
	 * @param array $params
	 * @return mysql_query
	 * @throws Exception
	 */
    function mysql_query_safe($query, array $params = array()) {
        if (!empty($params)){
			// there is possibility to use % sign in query - this line escapes it!
			$query = str_replace('%', '%%', $query);
			
			foreach ($params as $k => $p){
				if ($p === null){
					$query = preg_replace('/\?/', 'NULL', $query, 1);
					unset($params[$k]);
				} elseif (is_int($p) || is_float($p)) {
					$query = preg_replace('/\?/', $p, $query, 1);
					unset($params[$k]);
				} else {
					$query = preg_replace('/\?/', "'%s'", $query, 1);
				}
			}
            foreach ($params as &$v)
				$v = mysql_real_escape_string($v);
			
            $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
            $sql_query = mysql_query($sql_query);
        } else {
            $sql_query = mysql_query($query);
        }
        
		$err = mysql_error();
        if (!$sql_query && $err){
			throw new Exception( $err );
        }
        return ($sql_query);
    }
}

if(!function_exists('mysql_get_array'))
{
    function mysql_get_array($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $arr = array();
        while($row = mysql_fetch_assoc($q))
        {
            $arr[] = $row;
        }
        
        return $arr;
    }
}

if(!function_exists('mysql_get_row'))
{
    function mysql_get_row($query, $params = false)
    {
        $q = mysql_safequery($query, $params);
        $row = mysql_fetch_assoc($q);
        return $row;
    }
}

if(!function_exists('saveWHMCSconfig'))
{
    function saveWHMCSconfig($k, $v) 
    {
        $q = mysql_safequery("SELECT `value` FROM tblconfiguration WHERE `setting` = ?",array($k));
        $ret=mysql_fetch_array($q);
        unset($q);
        
        if(isset($ret['value'])) 
        {
            return mysql_safequery("UPDATE tblconfiguration SET value = ? WHERE setting = ?",array( $v, $k));
        }
        else
        {
            return mysql_safequery("INSERT INTO tblconfiguration  (setting,value) VALUES (?,?)",array($k, $v));
        }
    }
}

if(!function_exists('getWHMCSconfig'))
{
    function getWHMCSconfig($k) 
    {
        $q = mysql_safequery("SELECT value FROM tblconfiguration WHERE setting = ?", array($k));
        $ret=mysql_fetch_array($q);
        unset($q);
        
        if($ret['value'])
        {
            return $ret['value'];
        }
    }
}

if(!function_exists('addError'))
{
    function addError($error)
    {
        $_SESSION[ADDON_NAME]['errors'][] = $error;
    }
}


if(!function_exists('addInfo'))
{
    function addInfo($info)
    {
        $_SESSION[ADDON_NAME]['infos'][] = $info;
    }
}

if(!function_exists('getErrors'))
{
    function getErrors()
    {
        $errors = $_SESSION[ADDON_NAME]['errors'];
        $_SESSION[ADDON_NAME]['errors'] = null;
        return $errors;
    }
}

if(!function_exists('getInfos'))
{
    function getInfos()
    {
        $infos = $_SESSION[ADDON_NAME]['infos'];
        $_SESSION[ADDON_NAME]['infos'] = null;
        return $infos;
    }
}

if(!function_exists('getAddonUrl'))
{
    function getAddonUrl($modpage = null, $modsubpage = null)
    {
        global $IS_CLIENTAREA;
        if($IS_CLIENTAREA)
            return 'index.php?m=' . ADDON_NAME.($modpage ? '&modpage=' . $modpage : '') . ($modsubpage ? '&modsubpage=' . $modsubpage : '');
        else
            return 'addonmodules.php?module=' . ADDON_NAME . ($modpage ? '&modpage=' . $modpage : '') . ($modsubpage ? '&modsubpage=' . $modsubpage : '');
    }
}


if(!function_exists('saveWHMCSconfig2'))
{
    function saveWHMCSconfig2($k, $v) 
    {
        $q = mysql_safequery("SELECT `value` FROM tblconfiguration WHERE `setting` = ?",array($k));
        $ret=mysql_fetch_array($q);
        unset($q);
        
        if(isset($ret['value'])) 
        {
            return mysql_safequery("UPDATE tblconfiguration SET value = ? WHERE setting = ?",array( $v, $k));
        }
        else
        {
            return mysql_safequery("INSERT INTO tblconfiguration  (setting,value) VALUES (?,?)",array($k, $v));
        }
    }
}

if(!function_exists('updateProductsConfig')){
    function updateProductsConfig($c){
        $array = unserialize($c);

        if(!isset($array['disable_manage_routes'])){
            $array['disable_manage_routes'] = '0';
        }

        if(!isset($array['disable_edit_contact'])){
            $array['disable_edit_contact'] = '0';
        }

        return mysql_safequery("UPDATE tblproducts SET configoption2 = ?, configoption3 = ?, configoption4 = ?, configoption5 = ?, configoption6 = ? WHERE servertype = 'kwspamexperts';",
                array(
                    $array['url'],
                    $array['user'],
                    $array['password'],
                    $array['disable_manage_routes'],
                    $array['disable_edit_contact']
                ));
    }
}
