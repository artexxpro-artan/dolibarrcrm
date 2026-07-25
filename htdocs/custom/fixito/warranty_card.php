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
require_once __DIR__.'/lib/fixito.lib.php';
require_once __DIR__.'/lib/fixito_jalali.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Form $form
 * @var Translate $langs
 * @var User $user
 */

$langs->loadLangs(array('fixito@fixito', 'companies', 'products'));

$id = GETPOSTINT('id');
$action = GETPOST('action', 'aZ09');

$object = new FixitoWarranty($db);
if ($id > 0) {
	$object->fetch($id);
}

if ($action === 'create' && !$user->hasRight('fixito', 'fixito', 'write')) {
	accessforbidden();
}
if ($id > 0 && !$user->hasRight('fixito', 'fixito', 'read')) {
	accessforbidden();
}

if ($action === 'add' || $action === 'update') {
	if (!$user->hasRight('fixito', 'fixito', 'write')) {
		accessforbidden();
	}
	$object->fk_soc = GETPOSTINT('fk_soc');
	$object->fk_product = GETPOSTINT('fk_product');
	$object->serial_number = GETPOST('serial_number', 'alphanohtml');
	$object->label = GETPOST('label', 'alphanohtml');
	$object->warranty_months = GETPOSTINT('warranty_months');
	if ($object->warranty_months <= 0) {
		$object->warranty_months = (int) getDolGlobalString('FIXITO_DEFAULT_WARRANTY_MONTHS', 12);
	}

	$date_sale_str = GETPOST('date_sale', 'alphanohtml');
	if ($date_sale_str !== '' && getDolGlobalString('FIXITO_JALALI_ENABLED')) {
		$object->date_sale = fixito_jalali_string_to_timestamp($date_sale_str);
	} else {
		$object->date_sale = dol_stringtotime($date_sale_str);
	}
	if (empty($object->date_sale)) {
		$object->date_sale = dol_now();
	}

	if ($action === 'add') {
		$result = $object->create($user);
		if ($result > 0) {
			setEventMessages($langs->trans('FixitoWarrantyCreated'), null, 'mesgs');
			header('Location: '.$_SERVER['PHP_SELF'].'?id='.$object->id);
			exit;
		}
		setEventMessages($object->error, $object->errors, 'errors');
	} else {
		$object->id = $id;
		$result = $object->update($user);
		if ($result > 0) {
			setEventMessages($langs->trans('RecordSaved'), null, 'mesgs');
		} else {
			setEventMessages($langs->trans('Error'), null, 'errors');
		}
	}
}

if ($action === 'delete' && $id > 0 && $user->hasRight('fixito', 'warranty', 'delete')) {
	$object->fetch($id);
	if ($object->delete($user) > 0) {
		setEventMessages($langs->trans('RecordDeleted'), null, 'mesgs');
		header('Location: '.dol_buildpath('/fixito/warranty_list.php', 1));
		exit;
	}
}

$title = ($action === 'create') ? $langs->trans('FixitoNewWarranty') : $object->ref;
llxHeader('', $title, '', '', 0, 0, '', '', '', fixito_llx_body_class('fixito-page fixito-form-page'));

require_once __DIR__.'/lib/fixito_hub.lib.php';
fixito_print_hub_nav($langs, 'support');
print '<div class="fixito-page-content">';

if ($action === 'create' || $action === 'edit') {
	print '<div class="fixito-form-panel fixito-form">';
	$formaction = ($action === 'create') ? 'add' : 'update';
	print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="'.$formaction.'">';
	if ($id > 0) {
		print '<input type="hidden" name="id" value="'.$id.'">';
	}

	print '<table class="border centpercent tableforfieldcreate">';
	print '<tr><td class="fieldrequired">'.$langs->trans('ThirdParty').'</td><td>';
	print $form->select_company($object->fk_soc, 'fk_soc', '', 'SelectThirdParty', 0, 0, array(), 0, 'minwidth300', 0, '', '', 1);
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('Product').'</td><td>';
	print $form->select_produits($object->fk_product, 'fk_product', '', 0, 0, 1, 2, '', 1, array(), 0, '1', 0, 'minwidth300');
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('FixitoSerialNumber').'</td><td>';
	print '<input class="minwidth200" name="serial_number" value="'.dol_escape_htmltag($object->serial_number).'" required>';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('Label').'</td><td>';
	print '<input class="quatrevingtpercent" name="label" value="'.dol_escape_htmltag($object->label).'">';
	print '</td></tr>';
	print '<tr><td class="fieldrequired">'.$langs->trans('FixitoDateSale').'</td><td>';
	$dsdisplay = '';
	if (!empty($object->date_sale) && getDolGlobalString('FIXITO_JALALI_ENABLED')) {
		$dsdisplay = fixito_print_jalali_date($object->date_sale);
	} elseif (!empty($object->date_sale)) {
		$dsdisplay = dol_print_date($object->date_sale, 'day');
	}
	print '<input class="maxwidth150 fixito-jalali-input" name="date_sale" value="'.dol_escape_htmltag($dsdisplay).'" placeholder="1403/01/01">';
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('FixitoWarrantyMonths').'</td><td>';
	print '<input class="maxwidth75" type="number" name="warranty_months" value="'.(int) ($object->warranty_months ? $object->warranty_months : getDolGlobalString('FIXITO_DEFAULT_WARRANTY_MONTHS', 12)).'">';
	print '</td></tr>';
	print '</table>';
	print '<div class="center"><input class="button" type="submit" value="'.$langs->trans('FixitoSaveWarranty').'"></div>';
	print '</form>';
	print '</div>';
} else {
	print load_fiche_titre($object->ref, '', 'fa-shield');
	print '<div class="fichecenter">';
	print '<table class="border centpercent tableforfield">';
	print '<tr><td class="titlefield">'.$langs->trans('ThirdParty').'</td><td>';
	if ($object->fk_soc > 0) {
		require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
		$soc = new Societe($db);
		$soc->fetch($object->fk_soc);
		print $soc->getNomUrl(1);
	}
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('FixitoSerialNumber').'</td><td>'.dol_escape_htmltag($object->serial_number).'</td></tr>';
	print '<tr><td>'.$langs->trans('FixitoDateSale').'</td><td>';
	print fixito_to_persian_digits(fixito_print_jalali_date($object->date_sale));
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('FixitoWarrantyEnd').'</td><td>';
	print fixito_to_persian_digits(fixito_print_jalali_date($object->date_warranty_end));
	print '</td></tr>';
	print '<tr><td>'.$langs->trans('Status').'</td><td>'.$object->getLibStatut(1).'</td></tr>';
	print '</table>';
	print '<div class="tabsAction">';
	if ($user->hasRight('fixito', 'fixito', 'write')) {
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&action=edit">'.$langs->trans('Modify').'</a>';
	}
	print '<a class="butAction" href="'.dol_buildpath('/fixito/warranty_list.php', 1).'">'.$langs->trans('BackToList').'</a>';
	print '</div>';
	print '</div>';
}

print '</div>';

llxFooter();
$db->close();
