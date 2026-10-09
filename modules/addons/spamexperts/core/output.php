<?php

/**
 * Admin area output, loaded by spamexperts_output().
 *
 * Styled with the Bootstrap and Font Awesome the WHMCS admin area already loads.
 */

require_once __DIR__ . DS . 'core.php';

echo '<div id="spamexperts-addon">';

#SHOW BODY
// phpcs:ignore PHPCS_SecurityAudit.Misc.IncludeMismatch.ErrMiscIncludeMismatchNoExt,PHPCS_SecurityAudit.BadFunctions.EasyRFI.WarnEasyRFI
require_once __DIR__ . DS . 'views' . DS . 'body.php';

echo '</div>';
