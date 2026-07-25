<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

/**
 * \file       htdocs/custom/fixito/lib/fixito_iran_crm.lib.php
 * \brief      Full Iran CRM profile (modules, IRR, fa_IR, sales stack)
 */

require_once __DIR__.'/fixito.lib.php';

/**
 * Modules required for Iranian sales CRM (معامله، فروش، فاکتور، مشتری، کالا).
 *
 * @return string[]
 */
function fixito_iran_crm_module_classes()
{
	return array(
		'modSociete',
		'modCategorie',
		'modProduct',
		'modPropale',
		'modCommande',
		'modFacture',
		'modProjet',
		'modBanque',
		'modAgenda',
		'modFixito',
	);
}

/**
 * Enable sales CRM modules.
 *
 * @param DoliDB $db Database
 * @return array{activated:string[],errors:string[]}
 */
function fixito_iran_crm_activate_modules($db)
{
	global $conf;

	require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

	$out = array('activated' => array(), 'errors' => array());

	foreach (fixito_iran_crm_module_classes() as $modName) {
		$const = 'MAIN_MODULE_'.strtoupper(preg_replace('/^mod/i', '', $modName));
		if (getDolGlobalString($const)) {
			continue;
		}
		$ret = activateModule($modName, 1, 1);
		if (!empty($ret['errors'])) {
			$out['errors'] = array_merge($out['errors'], $ret['errors']);
		} else {
			$out['activated'][] = $modName;
		}
	}

	return $out;
}

/**
 * Apply Iran company, currency and CRM behaviour constants.
 *
 * @param DoliDB $db     Database
 * @param int    $entity Entity id
 * @return void
 */
function fixito_iran_crm_apply_constants($db, $entity)
{
	if (!function_exists('dolibarr_set_const')) {
		require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
	}

	fixito_apply_iran_defaults($db, $entity);

	$consts = array(
		'MAIN_MONNAIE' => 'IRR',
		'MAIN_INFO_SOCIETE_COUNTRY' => 'IR',
		'MAIN_INFO_SOCIETE_NOM' => 'آرتکس پرو',
		'MAIN_INFO_SOCIETE_TOWN' => 'تهران',
		'MAIN_INFO_SOCIETE_ADDRESS' => 'ایران',
		'MAIN_INFO_SOCIETE_MAIL' => 'crm@artexxpro.ir',
		'MAIN_DEFAULT_LANG_DEFAULT' => 'fa_IR',
		'MAIN_LANG_DEFAULT' => 'fa_IR',
		'MAIN_SIZE_LISTE_LIMIT' => '25',
		'MAIN_MODULE_MULTICURRENCY' => '0',
		'PROJECT_USE_OPPORTUNITIES' => '1',
		'MAIN_MENU_STANDARD_FORCED' => 'eldy',
		'MAIN_OPTIMIZEFORTEXTBROWSER' => '0',
		'MAIN_SHOW_TECHNICAL_INFO' => '0',
		'MAIN_SECURITY_MIN_PASSWORD_LENGTH' => '6',
		'FACTURE_TVAOPTION' => '0',
		'PRODUIT_MULTIPRICES' => '0',
		'SOCIETE_CODECLIENT_ADDON' => 'mod_codeclient_leopard',
	);

	foreach ($consts as $name => $value) {
		dolibarr_set_const($db, $name, $value, 'chaine', 0, '', $entity);
	}

	// Ensure IRR is default in currency table for entity usage
	$sql = "UPDATE ".$db->prefix()."c_currencies SET active = 1 WHERE code_iso = 'IRR'";
	$db->query($sql);
	$sql = "UPDATE ".$db->prefix()."c_currencies SET active = 0 WHERE code_iso IN ('EUR','USD')";
	$db->query($sql);
}

/**
 * Set all internal users to Persian.
 *
 * @param DoliDB $db Database
 * @return void
 */
function fixito_iran_crm_set_users_farsi($db)
{
	$sql = "UPDATE ".$db->prefix()."user SET lang = 'fa_IR' WHERE statut = 1";
	$db->query($sql);
}

/**
 * One-shot: Iranian CRM profile (modules + IRR + fa_IR).
 *
 * @param DoliDB $db Database
 * @param int    $entity Entity
 * @return array<string,mixed>
 */
function fixito_iranize_crm_full($db, $entity)
{
	$mods = fixito_iran_crm_activate_modules($db);
	fixito_iran_crm_apply_constants($db, $entity);
	fixito_iran_crm_set_users_farsi($db);

	return array(
		'modules' => $mods,
		'currency' => 'IRR',
		'lang' => 'fa_IR',
	);
}
