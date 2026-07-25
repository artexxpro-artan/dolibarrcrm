<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

/**
 * \file       htdocs/custom/fixito/lib/fixito.lib.php
 * \brief      Admin tabs and helpers
 */

require_once __DIR__.'/fixito_jalali.lib.php';

/**
 * Prepare admin pages header
 *
 * @return array<array{string,string,string}>
 */
function fixitoAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load('fixito@fixito');
	$h = 0;
	$head = array();
	$head[$h][0] = dol_buildpath('/fixito/admin/setup.php', 1);
	$head[$h][1] = $langs->trans('Settings');
	$head[$h][2] = 'settings';
	$h++;

	return $head;
}

/**
 * Find third party by phone or name; create if not found.
 *
 * @param DoliDB $db       Database
 * @param User   $user     User
 * @param string $name     Company / customer name
 * @param string $phone    Phone
 * @param string $email    Email
 * @return int             socid or <0 on error
 */
function fixito_find_or_create_thirdparty($db, $user, $name, $phone = '', $email = '')
{
	require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

	$name = trim($name);
	if ($name === '') {
		return -1;
	}

	$soc = new Societe($db);
	if ($phone !== '') {
		$sql = "SELECT rowid FROM ".$db->prefix()."societe";
		$sql .= " WHERE entity IN (".getEntity('societe').")";
		$sql .= " AND (phone = '".$db->escape($phone)."' OR phone_mobile = '".$db->escape($phone)."')";
		$sql .= " ORDER BY rowid DESC LIMIT 1";
		$resql = $db->query($sql);
		if ($resql && ($obj = $db->fetch_object($resql))) {
			return (int) $obj->rowid;
		}
	}

	$soc->name = $name;
	$soc->client = 1;
	$soc->phone = $phone;
	if ($email !== '') {
		$soc->email = $email;
	}
	$soc->code_client = -1;
	$result = $soc->create($user);
	if ($result > 0) {
		return (int) $soc->id;
	}
	return -1;
}
