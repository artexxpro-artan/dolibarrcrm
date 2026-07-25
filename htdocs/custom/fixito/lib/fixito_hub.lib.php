<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

/**
 * \file       htdocs/custom/fixito/lib/fixito_hub.lib.php
 * \brief      Smart branch hubs (sales, support, accounting, stock)
 */

/**
 * Branch definitions with workflow steps.
 *
 * @param Translate $langs Langs
 * @return array<string,array<string,mixed>>
 */
function fixito_hub_branches($langs)
{
	$langs->load('fixito@fixito');

	return array(
		'sales' => array(
			'key' => 'sales',
			'title' => $langs->trans('FixitoHubSales'),
			'subtitle' => $langs->trans('FixitoHubSalesDesc'),
			'icon' => 'fa-chart-line',
			'theme' => 'sales',
			'steps' => array(
				array('label' => $langs->trans('FixitoFlowNewCustomer'), 'desc' => $langs->trans('FixitoFlowNewCustomerDesc'), 'icon' => 'fa-user-plus', 'url' => '/societe/card.php?action=create&client=1'),
				array('label' => $langs->trans('FixitoQuickDeal'), 'desc' => $langs->trans('FixitoFlowDealDesc'), 'icon' => 'fa-bolt', 'url' => '/fixito/quickdeal.php', 'primary' => 1),
				array('label' => $langs->trans('FixitoFlowDealsList'), 'desc' => $langs->trans('FixitoFlowDealsListDesc'), 'icon' => 'fa-handshake', 'url' => '/comm/propal/list.php?leftmenu=propals'),
				array('label' => $langs->trans('FixitoQuickSale'), 'desc' => $langs->trans('FixitoFlowSaleDesc'), 'icon' => 'fa-shopping-cart', 'url' => '/fixito/quicksale.php', 'primary' => 1),
				array('label' => $langs->trans('FixitoFlowOrders'), 'desc' => $langs->trans('FixitoFlowOrdersDesc'), 'icon' => 'fa-file-invoice', 'url' => '/commande/list.php?leftmenu=orders'),
			),
		),
		'support' => array(
			'key' => 'support',
			'title' => $langs->trans('FixitoHubSupport'),
			'subtitle' => $langs->trans('FixitoHubSupportDesc'),
			'icon' => 'fa-headset',
			'theme' => 'support',
			'steps' => array(
				array('label' => $langs->trans('FixitoFlowNewTicket'), 'desc' => $langs->trans('FixitoFlowNewTicketDesc'), 'icon' => 'fa-ticket-alt', 'url' => '/ticket/card.php?action=create', 'module' => 'ticket'),
				array('label' => $langs->trans('FixitoFlowTickets'), 'desc' => $langs->trans('FixitoFlowTicketsDesc'), 'icon' => 'fa-list', 'url' => '/ticket/index.php?leftmenu=ticket', 'module' => 'ticket'),
				array('label' => $langs->trans('FixitoNewWarranty'), 'desc' => $langs->trans('FixitoFlowWarrantyNewDesc'), 'icon' => 'fa-shield-alt', 'url' => '/fixito/warranty_card.php?action=create', 'primary' => 1),
				array('label' => $langs->trans('FixitoWarranty'), 'desc' => $langs->trans('FixitoFlowWarrantyListDesc'), 'icon' => 'fa-clipboard-check', 'url' => '/fixito/warranty_list.php'),
				array('label' => $langs->trans('FixitoFlowInterventions'), 'desc' => $langs->trans('FixitoFlowInterventionsDesc'), 'icon' => 'fa-tools', 'url' => '/fichinter/list.php', 'module' => 'ficheinter'),
			),
		),
		'accounting' => array(
			'key' => 'accounting',
			'title' => $langs->trans('FixitoHubAccounting'),
			'subtitle' => $langs->trans('FixitoHubAccountingDesc'),
			'icon' => 'fa-calculator',
			'theme' => 'accounting',
			'steps' => array(
				array('label' => $langs->trans('FixitoFlowNewInvoice'), 'desc' => $langs->trans('FixitoFlowNewInvoiceDesc'), 'icon' => 'fa-file-invoice-dollar', 'url' => '/compta/facture/card.php?action=create'),
				array('label' => $langs->trans('FixitoFlowInvoices'), 'desc' => $langs->trans('FixitoFlowInvoicesDesc'), 'icon' => 'fa-receipt', 'url' => '/compta/facture/list.php?leftmenu=customers_bills'),
				array('label' => $langs->trans('FixitoFlowPayments'), 'desc' => $langs->trans('FixitoFlowPaymentsDesc'), 'icon' => 'fa-money-bill-wave', 'url' => '/compta/paiement/list.php'),
				array('label' => $langs->trans('FixitoFlowCustomers'), 'desc' => $langs->trans('FixitoFlowCustomersAccDesc'), 'icon' => 'fa-users', 'url' => '/societe/list.php?type=c&leftmenu=customers'),
			),
		),
		'stock' => array(
			'key' => 'stock',
			'title' => $langs->trans('FixitoHubStock'),
			'subtitle' => $langs->trans('FixitoHubStockDesc'),
			'icon' => 'fa-warehouse',
			'theme' => 'stock',
			'steps' => array(
				array('label' => $langs->trans('FixitoFlowNewProduct'), 'desc' => $langs->trans('FixitoFlowNewProductDesc'), 'icon' => 'fa-box', 'url' => '/product/card.php?action=create&type=0'),
				array('label' => $langs->trans('FixitoFlowProducts'), 'desc' => $langs->trans('FixitoFlowProductsDesc'), 'icon' => 'fa-th-large', 'url' => '/product/list.php?leftmenu=product'),
				array('label' => $langs->trans('FixitoFlowStockView'), 'desc' => $langs->trans('FixitoFlowStockViewDesc'), 'icon' => 'fa-cubes', 'url' => '/product/reassort.php?type=0', 'module' => 'stock'),
				array('label' => $langs->trans('FixitoFlowStockMove'), 'desc' => $langs->trans('FixitoFlowStockMoveDesc'), 'icon' => 'fa-dolly', 'url' => '/product/stock/movement_list.php', 'module' => 'stock'),
			),
		),
	);
}

/**
 * KPI snippets for dashboard.
 *
 * @param DoliDB $db DB
 * @return array<string,int>
 */
function fixito_hub_kpis($db)
{
	$kpi = array(
		'propals_open' => 0,
		'orders_month' => 0,
		'invoices_unpaid' => 0,
		'tickets_open' => 0,
	);

	if (isModEnabled('propale')) {
		$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."propal WHERE entity IN (".getEntity('propal').") AND fk_statut IN (1,2)";
		$res = $db->query($sql);
		if ($res && ($o = $db->fetch_object($res))) {
			$kpi['propals_open'] = (int) $o->nb;
		}
	}
	if (isModEnabled('commande')) {
		$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."commande WHERE entity IN (".getEntity('commande').")";
		$sql .= " AND date_commande >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
		$res = $db->query($sql);
		if ($res && ($o = $db->fetch_object($res))) {
			$kpi['orders_month'] = (int) $o->nb;
		}
	}
	if (isModEnabled('facture')) {
		$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."facture WHERE entity IN (".getEntity('invoice').") AND fk_statut = 1 AND paye = 0";
		$res = $db->query($sql);
		if ($res && ($o = $db->fetch_object($res))) {
			$kpi['invoices_unpaid'] = (int) $o->nb;
		}
	}
	if (isModEnabled('ticket')) {
		$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."ticket WHERE entity IN (".getEntity('ticket').") AND fk_statut NOT IN (8,9)";
		$res = $db->query($sql);
		if ($res && ($o = $db->fetch_object($res))) {
			$kpi['tickets_open'] = (int) $o->nb;
		}
	}

	return $kpi;
}

/**
 * Top branch nav pills.
 *
 * @param Translate $langs   Langs
 * @param string    $active Active branch key or empty
 * @return void
 */
function fixito_print_hub_nav($langs, $active = '')
{
	$branches = fixito_hub_branches($langs);
	print '<nav class="fixito-hub-nav">';
	print '<a class="fixito-hub-nav-item'.($active === '' ? ' is-active' : '').'" href="'.dol_buildpath('/fixito/fixitoindex.php', 1).'">';
	print '<span class="fa fa-home"></span> '.$langs->trans('FixitoDashboard').'</a>';
	foreach ($branches as $b) {
		$cls = 'fixito-hub-nav-item fixito-hub-nav-'.$b['theme'].($active === $b['key'] ? ' is-active' : '');
		print '<a class="'.$cls.'" href="'.dol_buildpath('/fixito/hub.php', 1).'?branch='.$b['key'].'">';
		print '<span class="fa '.$b['icon'].'"></span> '.dol_escape_htmltag($b['title']).'</a>';
	}
	print '</nav>';
}

/**
 * Render branch hub page body.
 *
 * @param Translate $langs   Langs
 * @param string    $branch  Branch key
 * @return void
 */
function fixito_print_hub_branch($langs, $branch)
{
	$branches = fixito_hub_branches($langs);
	if (empty($branches[$branch])) {
		print '<div class="error">'.$langs->trans('Error').'</div>';
		return;
	}
	$b = $branches[$branch];
	print '<div class="fixito-hub-hero fixito-hub-hero-'.dol_escape_htmltag($b['theme']).'">';
	print '<div class="fixito-hub-hero-icon"><span class="fa '.$b['icon'].'"></span></div>';
	print '<div><h1 class="fixito-hub-hero-title">'.dol_escape_htmltag($b['title']).'</h1>';
	print '<p class="fixito-hub-hero-sub">'.dol_escape_htmltag($b['subtitle']).'</p></div>';
	print '</div>';

	print '<div class="fixito-flow-grid">';
	$stepnum = 1;
	foreach ($b['steps'] as $step) {
		if (!empty($step['module']) && !isModEnabled($step['module'])) {
			continue;
		}
		$url = dol_buildpath($step['url'], 1);
		$primary = !empty($step['primary']) ? ' fixito-flow-card-primary' : '';
		print '<a class="fixito-flow-card'.$primary.'" href="'.$url.'">';
		print '<span class="fixito-flow-step-num">'.$stepnum.'</span>';
		print '<span class="fa '.$step['icon'].' fixito-flow-icon"></span>';
		print '<span class="fixito-flow-label">'.dol_escape_htmltag($step['label']).'</span>';
		print '<span class="fixito-flow-desc">'.dol_escape_htmltag($step['desc']).'</span>';
		print '</a>';
		$stepnum++;
	}
	print '</div>';
}

/**
 * Modern dashboard (KPI + branch cards + quick actions).
 *
 * @param Translate $langs Langs
 * @param DoliDB    $db    DB
 * @return void
 */
function fixito_print_dashboard($langs, $db)
{
	$langs->load('fixito@fixito');
	$kpi = fixito_hub_kpis($db);
	$branches = fixito_hub_branches($langs);

	$activeWarranty = 0;
	$expiringWarranty = 0;
	$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."fixito_warranty";
	$sql .= " WHERE entity IN (".getEntity('fixitowarranty').") AND status = 1";
	$resql = $db->query($sql);
	if ($resql && ($obj = $db->fetch_object($resql))) {
		$activeWarranty = (int) $obj->nb;
	}
	$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."fixito_warranty";
	$sql .= " WHERE entity IN (".getEntity('fixitowarranty').") AND status = 1";
	$sql .= " AND date_warranty_end IS NOT NULL AND date_warranty_end <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
	$resql = $db->query($sql);
	if ($resql && ($obj = $db->fetch_object($resql))) {
		$expiringWarranty = (int) $obj->nb;
	}

	print '<div class="fixito-hub-hero fixito-hub-hero-dashboard">';
	print '<div class="fixito-hub-hero-icon"><span class="fa fa-flag"></span></div>';
	print '<div><h1 class="fixito-hub-hero-title">'.dol_escape_htmltag($langs->trans('FixitoWelcome')).'</h1>';
	print '<p class="fixito-hub-hero-sub">'.dol_escape_htmltag($langs->trans('FixitoWelcomeDesc')).'</p></div>';
	print '</div>';

	print '<div class="fixito-kpi-row">';
	$kpis = array(
		array('value' => $kpi['propals_open'], 'label' => $langs->trans('FixitoKpiOpenDeals'), 'icon' => 'fa-handshake', 'theme' => 'sales'),
		array('value' => $kpi['orders_month'], 'label' => $langs->trans('FixitoKpiOrdersMonth'), 'icon' => 'fa-shopping-bag', 'theme' => 'sales'),
		array('value' => $kpi['invoices_unpaid'], 'label' => $langs->trans('FixitoKpiUnpaidInvoices'), 'icon' => 'fa-file-invoice-dollar', 'theme' => 'accounting'),
		array('value' => $kpi['tickets_open'], 'label' => $langs->trans('FixitoKpiOpenTickets'), 'icon' => 'fa-headset', 'theme' => 'support', 'module' => 'ticket'),
		array('value' => $activeWarranty, 'label' => $langs->trans('FixitoStatsActiveWarranties'), 'icon' => 'fa-shield-alt', 'theme' => 'support'),
	);
	foreach ($kpis as $item) {
		if (!empty($item['module']) && !isModEnabled($item['module'])) {
			continue;
		}
		print '<div class="fixito-kpi-card fixito-kpi-'.dol_escape_htmltag($item['theme']).'">';
		print '<span class="fa '.$item['icon'].' fixito-kpi-icon"></span>';
		print '<span class="fixito-kpi-value">'.(int) $item['value'].'</span>';
		print '<span class="fixito-kpi-label">'.dol_escape_htmltag($item['label']).'</span>';
		print '</div>';
	}
	print '</div>';

	print '<h2 class="fixito-section-title">'.$langs->trans('FixitoHubBranchesTitle').'</h2>';
	print '<div class="fixito-branch-grid">';
	foreach ($branches as $b) {
		$url = dol_buildpath('/fixito/hub.php', 1).'?branch='.$b['key'];
		print '<a class="fixito-branch-card fixito-branch-'.dol_escape_htmltag($b['theme']).'" href="'.$url.'">';
		print '<span class="fa '.$b['icon'].' fixito-branch-icon"></span>';
		print '<span class="fixito-branch-title">'.dol_escape_htmltag($b['title']).'</span>';
		print '<span class="fixito-branch-desc">'.dol_escape_htmltag($b['subtitle']).'</span>';
		print '<span class="fixito-branch-cta">'.$langs->trans('FixitoOpenBranch').' <span class="fa fa-arrow-left"></span></span>';
		print '</a>';
	}
	print '</div>';

	print '<h2 class="fixito-section-title">'.$langs->trans('FixitoQuickActions').'</h2>';
	print '<div class="fixito-quick-row">';
	$quick = array(
		array('url' => '/fixito/quickdeal.php', 'label' => $langs->trans('FixitoQuickDeal'), 'icon' => 'fa-bolt', 'primary' => 1),
		array('url' => '/fixito/quicksale.php', 'label' => $langs->trans('FixitoQuickSale'), 'icon' => 'fa-shopping-cart', 'primary' => 1),
		array('url' => '/fixito/warranty_card.php?action=create', 'label' => $langs->trans('FixitoNewWarranty'), 'icon' => 'fa-shield-alt', 'primary' => 0),
	);
	foreach ($quick as $q) {
		$cls = 'fixito-quick-btn'.(!empty($q['primary']) ? ' is-primary' : '');
		print '<a class="'.$cls.'" href="'.dol_buildpath($q['url'], 1).'">';
		print '<span class="fa '.$q['icon'].'"></span> '.dol_escape_htmltag($q['label']).'</a>';
	}
	print '</div>';

	if ($expiringWarranty > 0) {
		print '<div class="fixito-alert-banner">';
		print '<span class="fa fa-exclamation-triangle"></span> ';
		print $langs->trans('FixitoWarrantyExpiringBanner', $expiringWarranty);
		print ' <a href="'.dol_buildpath('/fixito/warranty_list.php', 1).'">'.$langs->trans('FixitoViewList').'</a>';
		print '</div>';
	}
}
