<?php
/* Copyright (C) 2026 Artexx Pro / Fixito
 * One-shot deployment wizard for dolibarrcrm.artexxpro.ir
 */

$res = 0;
if (!$res && file_exists(__DIR__.'/../../../main.inc.php')) {
	$res = include __DIR__.'/../../../main.inc.php';
}
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
	$res = include __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once __DIR__.'/../lib/fixito.lib.php';
require_once __DIR__.'/../lib/fixito_iran_crm.lib.php';

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
$token = newToken();

if ($action === 'activate_fixito' && !isModEnabled('fixito')) {
	$ret = activateModule('modFixito', 1, 0);
	if (!empty($ret['errors'])) {
		setEventMessages('', $ret['errors'], 'errors');
	} else {
		fixito_apply_iran_defaults($db, $conf->entity);
		setEventMessages($langs->trans('FixitoModuleActivated'), null, 'mesgs');
	}
}

if ($action === 'enable_crm_modules') {
	$modules = array('modSociete', 'modPropale', 'modCommande', 'modFacture', 'modProjet', 'modProduct');
	foreach ($modules as $mod) {
		if (!getDolGlobalString('MAIN_MODULE_'.strtoupper(preg_replace('/^mod/i', '', $mod)))) {
			$ret = activateModule($mod, 0, 0);
			if (!empty($ret['errors'])) {
				setEventMessages($mod.': '.implode(', ', $ret['errors']), null, 'warnings');
			}
		}
	}
	setEventMessages($langs->trans('FixitoCrmModulesEnabled'), null, 'mesgs');
}

if ($action === 'apply_iran') {
	fixito_apply_iran_defaults($db, $conf->entity);
	setEventMessages($langs->trans('FixitoIranDefaultsApplied'), null, 'mesgs');
}

if ($action === 'iranize_full') {
	$result = fixito_iranize_crm_full($db, $conf->entity);
	$msg = $langs->trans('FixitoIranCrmDone');
	if (!empty($result['modules']['activated'])) {
		$msg .= ' ('.implode(', ', $result['modules']['activated']).')';
	}
	setEventMessages($msg, $result['modules']['errors'], empty($result['modules']['errors']) ? 'mesgs' : 'warnings');
}

$crmModulesOk = isModEnabled('societe') && isModEnabled('propale') && isModEnabled('commande') && isModEnabled('facture');
$currencyIrr = (getDolGlobalString('MAIN_MONNAIE') === 'IRR');
$customOk = fixito_is_custom_path_configured();
$fixitoOn = isModEnabled('fixito');

llxHeader('', $langs->trans('FixitoDeployTitle'));

print load_fiche_titre($langs->trans('FixitoDeployTitle'), '', 'fa-flag');

print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans('FixitoDeployCheck').'</td><td>'.$langs->trans('Status').'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('FixitoDeployCustomPath').'</td><td>'.($customOk ? img_picto('', 'tick') : img_picto('', 'warning').' '.$langs->trans('FixitoDeployCustomPathHelp')).'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('ModuleFixitoName').'</td><td>'.($fixitoOn ? img_picto('', 'tick') : $langs->trans('FixitoDeployNotActive')).'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('FixitoIranCrmModules').'</td><td>'.($crmModulesOk ? img_picto('', 'tick') : img_picto('', 'warning')).'</td></tr>';
print '<tr class="oddeven"><td>'.$langs->trans('FixitoIranCurrency').'</td><td>'.($currencyIrr ? 'IRR '.img_picto('', 'tick') : dol_escape_htmltag(getDolGlobalString('MAIN_MONNAIE'))).'</td></tr>';
print '</table><br>';

if (!$customOk) {
	print '<div class="warning">'.$langs->trans('FixitoDeployCustomPathHelp').'</div>';
	print '<pre class="small">$dolibarr_main_url_root_alt=\'/custom\';\n$dolibarr_main_document_root_alt=\'/var/www/.../htdocs/custom\';</pre>';
}

print '<div class="tabsAction">';

if (!$fixitoOn && $customOk) {
	print '<form class="inline-block" method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	print '<input type="hidden" name="token" value="'.$token.'">';
	print '<input type="hidden" name="action" value="activate_fixito">';
	print '<input class="button" type="submit" value="'.$langs->trans('FixitoDeployActivate').'">';
	print '</form>';
}

print '<form class="inline-block" method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$token.'">';
print '<input type="hidden" name="action" value="iranize_full">';
print '<input class="button buttonforaction" type="submit" value="'.$langs->trans('FixitoIranizeFull').'">';
print '</form>';

print '<form class="inline-block" method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$token.'">';
print '<input type="hidden" name="action" value="enable_crm_modules">';
print '<input class="button" type="submit" value="'.$langs->trans('FixitoDeployEnableCrm').'">';
print '</form>';

print '<form class="inline-block" method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$token.'">';
print '<input type="hidden" name="action" value="apply_iran">';
print '<input class="button" type="submit" value="'.$langs->trans('FixitoDeployApplyIran').'">';
print '</form>';

print '<a class="button" href="'.dol_buildpath('/fixito/fixitoindex.php', 1).'">'.$langs->trans('FixitoDashboard').'</a>';

print '</div>';

llxFooter();
$db->close();
