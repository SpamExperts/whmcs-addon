<?php

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 * @modified Mariusz Miodowski <mariusz@modulesgarden.com>
 */

require_once 'core.php';

ob_start();
#ATTACH STYLES
echo  '
<link href="'.$ASSETS_DIR.'/css/bootstrap.css" rel="stylesheet">
<link href="'.$ASSETS_DIR.'/css/bootstrap-responsive.css" rel="stylesheet">
    
<link href="'.$ASSETS_DIR.'/css/template-styles.css" rel="stylesheet">

<link href="'.$ASSETS_DIR.'/css/modulesgarden.css" rel="stylesheet">
    
<!--FONTS-->
<link href="'.$ASSETS_DIR.'/css/font-awesome.css" rel="stylesheet">

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if IE 7]>
  <link rel="stylesheet" href="assets/css/font-awesome-ie7.css">
<![endif]-->

<div class="body" id="mg-wrapper" data-target=".body" data-spy="scroll" data-twttr-rendered="true">';

$infos = getInfos();
if($infos)
{
    echo '<div class="border-box">';
    foreach($infos as $info)
    {
        echo '<div class="control-group success">
                    <span class="help-block center">'.$info.'</span>
                </div>';
    }
    echo '</div>';
}

$errors = getErrors();
if($errors)
{
    echo '<div class="border-box">';
    foreach($errors as $error)
    {
        echo '<div class="control-group error">
                    <span class="help-block center">'.$error.'</span>
                </div>';
    }
    echo '</div>';
}

echo $CONTENT;

echo '</div>';


return ob_get_clean();
?>
