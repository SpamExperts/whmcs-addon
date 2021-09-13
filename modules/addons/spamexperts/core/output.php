<?php

require_once 'core.php';

#ATTACH STYLES
// phpcs:disable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
echo '
   
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
<![endif]-->';
// phpcs:enable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn

echo '<div class="body" data-target=".body" data-spy="scroll" data-twttr-rendered="true" id="mg-wrapper">';

#SHOW SIDEBAR
// phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
require_once CORE_DIR.DS.'views'.DS.'sidebar.php';

#SHOW BODY
// phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
require_once CORE_DIR.DS.'views'.DS.'body.php';

echo '</div>';
?>
