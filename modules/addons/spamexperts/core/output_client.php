<?php

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 * @modified Mariusz Miodowski <mariusz@modulesgarden.com>
 */

require_once 'core.php';

ob_start();
#ATTACH STYLES
// phpcs:disable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
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
    { ?>
        <div class="control-group success">
            <span class="help-block center">
                <?php echo htmlentities($info, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>
    <?php }
    echo '</div>';
}

$errors = getErrors();
if($errors)
{
    echo '<div class="border-box">';
    foreach($errors as $error)
    { ?>
        <div class="control-group error">
            <span class="help-block center">
                <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </div>';
    <?php }
    echo '</div>';
}

// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
echo $CONTENT;

echo '</div>';


return ob_get_clean();
?>
