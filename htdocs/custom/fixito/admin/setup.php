<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

$res = 0;
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
	$res = include __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once __DIR__.'/lib/fixito.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Form $form
 * @var Translate $langs
 * @var User $user
 */

$langs->loadLangs(array('admin', 'fixito@fixito'));

if (!$user->admin) {
	accessforbidden();
}

$action = GETPOST('action', 'aZ09');

if ($action === 'update') {
	$jalali = GETPOST('FIXITO_JALALI_ENABLED', 'alpha');
	$rtl = GETPOST('FIXITO_RTL_ENHANCE', 'alpha');
	$digits = GETPOST('FIXITO_PERSIAN_DIGITS', 'alpha');
	$months = GETPOST('FIXITO_DEFAULT_WARRANTY_MONTHS', 'int');

	dolibarr_set_const($db, 'FIXITO_JALALI_ENABLED', $jalali, 'chaine', 0, '', $conf->entity);
	dolibarr_set_const($db, 'FIXITO_RTL_ENHANCE', $rtl, 'chaine', 0, '', $conf->entity);
	dolibarr_set_const($db, 'FIXITO_PERSIAN_DIGITS', $digits, 'chaine', 0, '', $conf->entity);
	dolibarr_set_const($db, 'FIXITO_DEFAULT_WARRANTY_MONTHS', (string) $months, 'chaine', 0, '', $conf->entity);

	setEventMessages($langs->trans('SetupSaved'), null, 'mesgs');
}

llxHeader('', $langs->trans('FixitoSettings'));

$head = fixitoAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans('FixitoSettings'), -1, 'fa-flag');

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans('Parameter').'</td><td>'.$langs->trans('Value').'</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans('FixitoJalaliEnabled').'</td><td>';
print $form->selectyesno('FIXITO_JALALI_ENABLED', getDolGlobalString('FIXITO_JALALI_ENABLED', '1'), 1);
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans('FixitoRtlEnhance').'</td><td>';
print $form->selectyesno('FIXITO_RTL_ENHANCE', getDolGlobalString('FIXITO_RTL_ENHANCE', '1'), 1);
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans('FixitoPersianDigits').'</td><td>';
print $form->selectyesno('FIXITO_PERSIAN_DIGITS', getDolGlobalString('FIXITO_PERSIAN_DIGITS', '0'), 1);
print '</td></tr>';

print '<tr class="oddeven"><td>'.$langs->trans('FixitoDefaultWarrantyMonths').'</td><td>';
print '<input class="maxwidth50" type="number" min="1" max="120" name="FIXITO_DEFAULT_WARRANTY_MONTHS" value="'.dol_escape_htmltag(getDolGlobalString('FIXITO_DEFAULT_WARRANTY_MONTHS', '12')).'">';
print '</td></tr>';

print '</table>';

print '<div class="center"><input type="submit" class="button" value="'.$langs->trans('Save').'"></div>';
print '</form>';

print dol_get_fiche_end();

llxFooter();
$db->close();
