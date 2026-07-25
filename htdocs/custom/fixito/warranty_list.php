<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

$res = 0;
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
	$res = include __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

require_once __DIR__.'/class/warranty.class.php';
require_once __DIR__.'/lib/fixito_jalali.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Translate $langs
 * @var User $user
 */

$langs->loadLangs(array('fixito@fixito', 'companies', 'products'));

if (!$user->hasRight('fixito', 'fixito', 'read')) {
	accessforbidden();
}

$search = GETPOST('search', 'alphanohtml');

llxHeader('', $langs->trans('FixitoWarranty'));

print load_fiche_titre($langs->trans('FixitoWarranty'), '', 'fa-shield');

print '<form method="GET">';
print '<input class="minwidth300" type="search" name="search" placeholder="'.$langs->trans('FixitoSearchWarranty').'" value="'.dol_escape_htmltag($search).'">';
print ' <input class="button" type="submit" value="'.$langs->trans('Search').'">';
print ' <a class="button" href="'.dol_buildpath('/fixito/warranty_card.php', 1).'?action=create">'.$langs->trans('FixitoNewWarranty').'</a>';
print '</form><br>';

$sql = "SELECT w.rowid, w.ref, w.serial_number, w.label, w.date_sale, w.date_warranty_end, w.status,";
$sql .= " s.nom as thirdparty";
$sql .= " FROM ".$db->prefix()."fixito_warranty as w";
$sql .= " LEFT JOIN ".$db->prefix()."societe as s ON s.rowid = w.fk_soc";
$sql .= " WHERE w.entity IN (".getEntity('fixitowarranty').")";
if ($search !== '') {
	$sql .= " AND (w.serial_number LIKE '%".$db->escape($search)."%'";
	$sql .= " OR w.ref LIKE '%".$db->escape($search)."%'";
	$sql .= " OR s.nom LIKE '%".$db->escape($search)."%')";
}
$sql .= " ORDER BY w.date_warranty_end ASC";

$resql = $db->query($sql);

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans('Ref').'</td>';
print '<td>'.$langs->trans('ThirdParty').'</td>';
print '<td>'.$langs->trans('FixitoSerialNumber').'</td>';
print '<td>'.$langs->trans('FixitoDateSale').'</td>';
print '<td>'.$langs->trans('FixitoWarrantyEnd').'</td>';
print '<td>'.$langs->trans('Status').'</td>';
print '</tr>';

if ($resql) {
	$num = $db->num_rows($resql);
	$i = 0;
	while ($i < $num) {
		$obj = $db->fetch_object($resql);
		print '<tr class="oddeven">';
		print '<td><a href="'.dol_buildpath('/fixito/warranty_card.php', 1).'?id='.$obj->rowid.'">'.$obj->ref.'</a></td>';
		print '<td>'.dol_escape_htmltag($obj->thirdparty).'</td>';
		print '<td>'.dol_escape_htmltag($obj->serial_number).'</td>';
		$ds = $db->jdate($obj->date_sale);
		$de = $db->jdate($obj->date_warranty_end);
		if (getDolGlobalString('FIXITO_JALALI_ENABLED')) {
			print '<td class="fixito-jalali-date" data-ts="'.$ds.'">'.fixito_to_persian_digits(fixito_print_jalali_date($ds)).'</td>';
			print '<td class="fixito-jalali-date" data-ts="'.$de.'">'.fixito_to_persian_digits(fixito_print_jalali_date($de)).'</td>';
		} else {
			print '<td>'.dol_print_date($ds, 'day').'</td>';
			print '<td>'.dol_print_date($de, 'day').'</td>';
		}
		$w = new FixitoWarranty($db);
		$w->status = $obj->status;
		print '<td>'.$w->getLibStatut(1).'</td>';
		print '</tr>';
		$i++;
	}
	if ($num === 0) {
		print '<tr class="oddeven"><td colspan="6"><span class="opacitymedium">'.$langs->trans('NoRecordFound').'</span></td></tr>';
	}
}

print '</table>';

llxFooter();
$db->close();
