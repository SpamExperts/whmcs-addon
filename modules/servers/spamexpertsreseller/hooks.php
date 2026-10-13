<?php

use WHMCS\Service\Service;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

if (!function_exists('spamexpertsreseller_service')) {
    /**
     * Service of this module
     *
     * @param int $serviceid
     * @return Service|null Null when the service belongs to another module
     */
    function spamexpertsreseller_service($serviceid)
    {
        $service = $serviceid ? Service::find($serviceid) : null;
        if (!$service || !$service->product || $service->product->servertype !== 'spamexpertsreseller') {
            return null;
        }
        return $service;
    }
}

/**
 * The six theme strips the active class from every tab pane when the URL has
 * a hash, which hides the tab under the product details. Restore it for this
 * module's services, whatever their status.
 */
add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    if (
        !isset($vars['templatefile']) || $vars['templatefile'] !== 'clientareaproductdetails'
        || empty($vars['serviceid'])
    ) {
        return '';
    }

    if (!spamexpertsreseller_service($vars['serviceid'])) {
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

if (!function_exists('spamexpertsreseller_managePageHeader')) {
    /**
     * Header of the management page requested for a service of this module
     *
     * @param int $serviceid
     * @return string|null Null when no management page is requested
     */
    function spamexpertsreseller_managePageHeader($serviceid)
    {
        if (
            !isset($_GET['action'], $_GET['manage']) || $_GET['action'] !== 'productdetails'
            || !is_string($_GET['manage']) || !function_exists('spamexpertsreseller_getLang')
            || !spamexpertsreseller_service($serviceid)
        ) {
            return null;
        }

        // Only the management pages have a header
        $lang = spamexpertsreseller_getLang([]);
        return $lang[$_GET['manage']]['header'] ?? null;
    }
}

/**
 * Add a management page to the breadcrumb. WHMCS links its Product Details
 * item without the service id, which sends the client to the product list,
 * so add the id back.
 */
add_hook('ClientAreaPage', 1, function ($vars) {
    if (
        !isset($vars['templatefile']) || $vars['templatefile'] !== 'clientareaproductdetails'
        || empty($vars['serviceid']) || empty($vars['breadcrumb'])
    ) {
        return [];
    }

    $header = spamexpertsreseller_managePageHeader($vars['serviceid']);
    if ($header === null) {
        return [];
    }

    $url        = 'clientarea.php?action=productdetails&id=' . (int) $vars['serviceid'];
    $breadcrumb = $vars['breadcrumb'];
    foreach ($breadcrumb as $i => $item) {
        if (
            isset($item['link']) && str_contains($item['link'], 'action=productdetails')
            && !str_contains($item['link'], 'id=')
        ) {
            $breadcrumb[$i]['link'] = $url;
        }
    }
    $breadcrumb[] = ['link' => $url . '&manage=' . urlencode($_GET['manage']), 'label' => $header];

    return ['breadcrumb' => $breadcrumb];
});

/**
 * Link the overview's sidebar item to the overview page. As a tab toggle it
 * only adds #tabOverview to the URL, since it is the only tab in the sidebar,
 * and on a management page, which replaces the overview, it does nothing.
 * The management pages the page links to get a panel of their own, to
 * switch between them.
 *
 * @param \WHMCS\View\Menu\Item $primarySidebar
 */
add_hook('ClientAreaPrimarySidebar', 1, function ($primarySidebar) {
    $serviceid = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $overview  = $primarySidebar->getChild('Service Details Overview');
    $item      = $overview ? $overview->getChild('Information') : null;
    $service   = $item && isset($_GET['action']) && $_GET['action'] === 'productdetails'
        ? spamexpertsreseller_service($serviceid)
        : null;
    if (!$service || !function_exists('spamexpertsreseller_getLang')) {
        return;
    }

    $url     = 'clientarea.php?action=productdetails&id=' . $serviceid;
    $current = spamexpertsreseller_managePageHeader($serviceid) !== null ? $_GET['manage'] : null;

    $item->setUri($url);
    $item->setAttribute('dataToggleTab', false);
    if ($current !== null) {
        $item->setCurrent(false);
    }

    // The pages the client area page recorded, false when the panel cannot be used
    $pages = function_exists('spamexpertsreseller_shownPages') ? spamexpertsreseller_shownPages() : null;
    if ($pages === null || $pages === false) {
        return;
    }

    $lang  = spamexpertsreseller_getLang([]);
    $panel = $primarySidebar->addChild('spamexpertsreseller Management', [
        'label' => $lang['mainsite']['management'],
        'icon'  => 'fa-wrench',
        'order' => $overview->getOrder() + 5,
    ]);
    $order = 10;
    foreach ($pages as $page => $label) {
        $panel->addChild('spamexpertsreseller ' . $page, [
            'label' => $label,
            'uri'   => $url . '&manage=' . $page,
            'order' => $order,
        ])->setCurrent($page === $current);
        $order += 10;
    }
    $panel->addChild('spamexpertsreseller login', [
        'label' => $lang['mainsite']['login'],
        'uri'   => $url . '&manage=login',
        'order' => $order,
    ])->setAttribute('target', '_blank');
});
