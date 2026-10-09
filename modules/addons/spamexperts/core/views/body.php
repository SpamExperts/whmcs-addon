<?php

/**
 * Page body, loaded by core/output.php. The variables are set by core.php.
 *
 * @var string $PAGE Requested page
 * @var array $TOP_MENU
 * @var string $MODULE_URL
 * @var string $CONTENT Output of the page and its view
 */
if ($TOP_MENU) {
    echo '<ul class="nav nav-tabs admin-tabs">';
    foreach ($TOP_MENU as $page => $menu) {
        // phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
        echo '<li' . ($page === $PAGE ? ' class="active"' : '') . '>'
            . '<a href="' . htmlspecialchars($MODULE_URL . '&modpage=' . $page, ENT_QUOTES, 'UTF-8') . '">'
            . $menu['title'] . '</a></li>';
    }
    echo '</ul>';
}

echo '<div class="tab-content admin-tabs"><div class="tab-pane active">';

$infos = getInfos();
if ($infos) {
    // phpcs:disable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
    foreach ($infos as $info) {
        echo '<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    ' . $info . '
                </div>';
    }
    // phpcs:enable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
}


$errors = getErrors();
if ($errors) {
    // phpcs:disable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
    foreach ($errors as $error) {
        echo '<div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                    ' . $error . '
                </div>';
    }
    // phpcs:enable PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
}

// phpcs:ignore PHPCS_SecurityAudit.BadFunctions.EasyXSS.EasyXSSwarn
echo $CONTENT;

echo '</div></div>';
