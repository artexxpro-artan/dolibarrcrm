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
require_once __DIR__.'/lib/fixito_hub.lib.php';

/**
 * @var Translate $langs
 * @var User $user
 */

$langs->load('fixito@fixito');

if (!$user->hasRight('fixito', 'fixito', 'read')) {
	accessforbidden();
}

$branch = GETPOST('branch', 'aZ09');
if ($branch === '') {
	$branch = 'sales';
}

llxHeader('', $langs->trans('FixitoHubTitle'), '', '', 0, 0, '', '', '', fixito_llx_body_class('fixito-page fixito-hub-page'));

fixito_print_hub_nav($langs, $branch);
print '<div class="fixito-page-content">';
fixito_print_hub_branch($langs, $branch);
print '</div>';

llxFooter();
