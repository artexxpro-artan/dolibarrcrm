<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

$res = 0;
if (!$res && file_exists(__DIR__.'/../../main.inc.php')) {
	$res = include __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once __DIR__.'/lib/fixito.lib.php';

if (isModEnabled('project')) {
	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
}

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var Form $form
 * @var Translate $langs
 * @var User $user
 */

$langs->loadLangs(array('fixito@fixito', 'propal', 'companies', 'projects'));

if (!$user->hasRight('fixito', 'fixito', 'write')) {
	accessforbidden();
}

$action = GETPOST('action', 'aZ09');

if ($action === 'add') {
	$name = GETPOST('customer_name', 'alphanohtml');
	$phone = GETPOST('customer_phone', 'alphanohtml');
	$email = GETPOST('customer_email', 'alphanohtml');
	$title = GETPOST('deal_title', 'alphanohtml');
	$amount = price2num(GETPOST('deal_amount', 'alphanohtml'));
	$note = GETPOST('deal_note', 'restricthtml');

	$db->begin();
	$socid = fixito_find_or_create_thirdparty($db, $user, $name, $phone, $email);
	if ($socid < 1) {
		setEventMessages($langs->trans('Error'), null, 'errors');
		$db->rollback();
	} else {
		$projectid = 0;
		if (isModEnabled('project')) {
			$project = new Project($db);
			$project->title = $title !== '' ? $title : $name;
			$project->socid = $socid;
			$project->usage_opportunity = 1;
			$project->opp_amount = $amount;
			$project->opp_percent = 50;
			$project->description = $note;
			$projectid = $project->create($user);
			if ($projectid < 0) {
				$projectid = 0;
			}
		}

		$propal = new Propal($db);
		$propal->socid = $socid;
		$propal->note_private = $note;
		if ($projectid > 0) {
			$propal->fk_project = $projectid;
		}
		$propalid = $propal->create($user);
		if ($propalid > 0) {
			$db->commit();
			setEventMessages($langs->trans('FixitoDealCreated'), null, 'mesgs');
			header('Location: '.dol_buildpath('/comm/propal/card.php', 1).'?id='.$propalid);
			exit;
		}
		$db->rollback();
		setEventMessages($propal->error, $propal->errors, 'errors');
	}
}

llxHeader('', $langs->trans('FixitoQuickDeal'));

print load_fiche_titre($langs->trans('FixitoQuickDeal'), '', 'fa-handshake');
print '<div class="info">'.$langs->trans('FixitoHelpQuickDeal').'</div>';

print '<form class="fixito-form" method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="add">';

print '<table class="border centpercent tableforfieldcreate">';

print '<tr><td class="fieldrequired">'.$langs->trans('FixitoCustomerName').'</td><td>';
print '<input class="minwidth300" type="text" name="customer_name" value="'.dol_escape_htmltag(GETPOST('customer_name', 'alphanohtml')).'" required>';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoCustomerPhone').'</td><td>';
print '<input class="minwidth200" type="text" name="customer_phone" value="'.dol_escape_htmltag(GETPOST('customer_phone', 'alphanohtml')).'">';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoCustomerEmail').'</td><td>';
print '<input class="minwidth300" type="email" name="customer_email" value="'.dol_escape_htmltag(GETPOST('customer_email', 'alphanohtml')).'">';
print '</td></tr>';

print '<tr><td class="fieldrequired">'.$langs->trans('FixitoDealTitle').'</td><td>';
print '<input class="quatrevingtpercent" type="text" name="deal_title" value="'.dol_escape_htmltag(GETPOST('deal_title', 'alphanohtml')).'" required>';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoDealAmount').'</td><td>';
print '<input class="maxwidth150" type="text" name="deal_amount" value="'.dol_escape_htmltag(GETPOST('deal_amount', 'alphanohtml')).'">';
print '</td></tr>';

print '<tr><td>'.$langs->trans('FixitoDealNote').'</td><td>';
print '<textarea class="quatrevingtpercent" rows="3" name="deal_note">'.dol_escape_htmltag(GETPOST('deal_note', 'restricthtml')).'</textarea>';
print '</td></tr>';

print '</table>';

print '<div class="center"><input class="button" type="submit" value="'.$langs->trans('FixitoSaveDeal').'"></div>';
print '</form>';

llxFooter();
$db->close();
