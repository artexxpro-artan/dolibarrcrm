<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

$res = 0;
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
	$res = include __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

require_once __DIR__.'/lib/fixito.lib.php';
require_once __DIR__.'/class/warranty.class.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 */

$langs->load('fixito@fixito');

if (!$user->hasRight('fixito', 'fixito', 'read')) {
	accessforbidden();
}

$active = 0;
$expiring = 0;
$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."fixito_warranty";
$sql .= " WHERE entity IN (".getEntity('fixitowarranty').") AND status = 1";
$resql = $db->query($sql);
if ($resql && ($obj = $db->fetch_object($resql))) {
	$active = (int) $obj->nb;
}
$sql = "SELECT COUNT(*) as nb FROM ".$db->prefix()."fixito_warranty";
$sql .= " WHERE entity IN (".getEntity('fixitowarranty').") AND status = 1";
$sql .= " AND date_warranty_end IS NOT NULL AND date_warranty_end <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$resql = $db->query($sql);
if ($resql && ($obj = $db->fetch_object($resql))) {
	$expiring = (int) $obj->nb;
}

llxHeader('', $langs->trans('FixitoDashboard'));

print load_fiche_titre($langs->trans('FixitoWelcome'), '', 'fa-flag');

print '<div class="info">'.$langs->trans('FixitoWelcomeDesc').'</div>';

print '<div class="fixito-dashboard-cards fichecenter">';

$cards = array(
	array('url' => dol_buildpath('/fixito/quickdeal.php', 1), 'label' => $langs->trans('FixitoShortcutDeal'), 'icon' => 'fa-handshake', 'help' => $langs->trans('FixitoHelpQuickDeal')),
	array('url' => dol_buildpath('/fixito/quicksale.php', 1), 'label' => $langs->trans('FixitoShortcutSale'), 'icon' => 'fa-shopping-cart', 'help' => $langs->trans('FixitoHelpQuickSale')),
	array('url' => dol_buildpath('/fixito/warranty_card.php?action=create', 1), 'label' => $langs->trans('FixitoShortcutWarranty'), 'icon' => 'fa-shield', 'help' => $langs->trans('FixitoHelpWarranty')),
);

foreach ($cards as $card) {
	print '<div class="fixito-card box">';
	print '<a href="'.$card['url'].'">';
	print '<span class="fa '.$card['icon'].' fixito-card-icon"></span>';
	print '<span class="fixito-card-title">'.$card['label'].'</span>';
	print '</a>';
	print '<p class="opacitymedium">'.$card['help'].'</p>';
	print '</div>';
}

print '</div>';

print '<br><table class="noborder centpercent">';
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans('FixitoWarranty').'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('FixitoStatsActiveWarranties').'</td><td class="right"><strong>'.$active.'</strong></td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('FixitoStatsExpiringSoon').'</td><td class="right"><strong>'.$expiring.'</strong></td></tr>';
print '</table>';

llxFooter();
$db->close();
