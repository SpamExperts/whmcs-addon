<?php

if (!defined("WHMCS")) {
  die("This file cannot be accessed directly");
}

if(isset($_POST['action']) && $_POST['action']=='savechanges')
{
    if(saveWHMCSconfig2('kwspamexperts_api', serialize($_POST['conf']))){
        updateProductsConfig(serialize($_POST['conf'])); 
        if(mysql_error())
            addError ("Products configuration update failed: " . mysql_error());
        addInfo('Configuration has been saved.');
    }
    else 
        addError (mysql_error());
}

$api  = getWHMCSconfig('kwspamexperts_api');
$data = unserialize($api);



