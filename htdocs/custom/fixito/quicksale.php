<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

$res = 0;
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
	$res = include __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once __DIR__.'/lib/fixito.lib.php';

/**
 * @var DoliDB $db
 * @var Form $form
 * @var Translate $langs
 * @var User $user
 */

$langs->loadLangs(array('fixito@fixito', 'orders', 'products', 'companies'));

if (!$user->hasRight('fixito', 'fixito', 'write')) {
	accessforbidden();
}

if (!isModEnabled('commande')) {
	accessforbidden('Module order not enabled');
}

$action = GETPOST('action', 'aZ09');

if ($action === 'add') {
	$name = GETPOST('customer_name', 'alphanohtml');
	$phone = GETPOST('customer_phone', 'alphanohtml');
	$email = GETPOST('customer_email', 'alphanohtml');
	$label = GETPOST('product_label', 'alphanohtml');
	$fk_product = GETPOSTINT('fk_product');
	$qty = price2num(GETPOST('qty', 'alphanohtml'));
	$price = price2num(GETPOST('unit_price', 'alphanohtml'));
	$validate = GETPOSTINT('validate_order');

	if ($qty <= 0) {
		$qty = 1;
	}

	$db->begin();
	$socid = fixito_find_or_create_thirdparty($db, $user, $name, $phone, $email);
	if ($socid < 1) {
		setEventMessages($langs->trans('Error'), null, 'errors');
		$db->rollback();
	} else {
		$commande = new Commande($db);
		$commande->socid = $socid;
		$orderid = $commande->create($user);
		if ($orderid > 0) {
			$desc = $label;
			$product = new Product($db);
			if ($fk_product > 0 && $product->fetch($fk_product) > 0) {
				$desc = $product->label;
				if ($price <= 0) {
					$price = $product->price;
				}
			}
			$resultline = $commande->addline($desc, $price, $qty, 0, 0, $fk_product);
			if ($resultline > 0 && $validate) {
				$commande->valid($user);
			}
			if ($resultline > 0) {
				$db->commit();
				setEventMessages($langs->trans('FixitoSaleCreated'), null, 'mesgs');
				header('Location: '.dol_buildpath('/commande/card.php', 1).'?id='.$orderid);
				exit;
			}
		}
		$db->rollback();
		setEventMessages($commande->error, $commande->errors, 'errors');
	}
}

llxHeader('', $langs->trans('FixitoQuickSale'), '', '', 0, 0, '', '', '', fixito_llx_body_class('fixito-page fixito-form-page'));

require_once __DIR__.'/lib/fixito_hub.lib.php';
fixito_print_hub_nav($langs, 'sales');

print '<div class="fixito-page-content">';
print '<p class="fixito-lead-hint">'.$langs->trans('FixitoHelpQuickSale').'</p>';
print '<div class="fixito-form-panel fixito-form">';
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="add">';

print '<table class="border centpercent tableforfieldcreate">';

print '<tr><td class="fieldrequired">'.$langs->trans('FixitoCustomerName').'</td><td>';
print '<input class="minwidth300" type="text" name="customer_name" required>';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoCustomerPhone').'</td><td>';
print '<input class="minwidth200" type="text" name="customer_phone">';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoCustomerEmail').'</td><td>';
print '<input class="minwidth300" type="email" name="customer_email">';
print '</td></tr>';

print '<tr><td>'.$langs->trans('Product').'</td><td>';
print $form->select_produits(GETPOSTINT('fk_product'), 'fk_product', '', 0, 0, 1, 2, '', 1, array(), 0, '1', 0, 'minwidth300');
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoProductLabel').'</td><td>';
print '<input class="quatrevingtpercent" type="text" name="product_label" placeholder="'.$langs->trans('FixitoProductLabel').'">';
print '</td></tr>';

print '<tr><td class="fieldrequired">'.$langs->trans('FixitoQty').'</td><td>';
print '<input class="maxwidth75" type="text" name="qty" value="1">';
print '</td></tr>';

print '<tr><td class="fieldrequired">'.$langs->trans('FixitoUnitPrice').'</td><td>';
print '<input class="maxwidth150" type="text" name="unit_price" value="">';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoValidateOrder').'</td><td>';
print $form->selectyesno('validate_order', 1, 1);
print '</td></tr>';

print '</table>';

print '<div class="center"><input class="button" type="submit" value="'.$langs->trans('FixitoSaveSale').'"></div>';
print '</form>';
print '</div></div>';

llxFooter();
$db->close();
