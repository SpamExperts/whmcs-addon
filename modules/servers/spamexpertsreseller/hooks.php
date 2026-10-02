<?php

use WHMCS\Service\Service;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * The six theme strips the active class from every tab pane when the URL has
 * a hash, which hides the tab under the product details. Restore it for this
 * module's services, whatever their status.
 */
add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    if (!isset($vars['templatefile']) || $vars['templatefile'] !== 'clientareaproductdetails'
        || empty($vars['serviceid'])
    ) {
        return '';
    }

    $service = Service::find($vars['serviceid']);
    if (!$service || !$service->product || $service->product->servertype !== 'spamexpertsreseller') {
        return '';
    }

    return <<<'HTML'
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.product-details-tab-container').each(function(){
            var content = jQuery(this);
            if (content.children('.tab-pane.active').length) {
                return;
            }
            var href = content.siblings('.nav-tabs').find('li.active > a').attr('href');
            var pane = href ? content.children(href) : jQuery();
            (pane.length ? pane : content.children('.tab-pane').first()).addClass('active');
        });
    });
</script>
HTML;
});
