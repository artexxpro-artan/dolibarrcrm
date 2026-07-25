<?php
/* Copyright (C) 2026 Artexx Pro / Fixito — CLI: php scripts/iranize_crm.php (from htdocs/custom/fixito) */

if (php_sapi_name() !== 'cli') {
	die('CLI only');
}

$res = 0;
if (file_exists(__DIR__.'/../../../main.inc.php')) {
	$res = include __DIR__.'/../../../main.inc.php';
}
if (!$res) {
	fwrite(STDERR, "Cannot load main.inc.php\n");
	exit(1);
}

require_once __DIR__.'/../lib/fixito_iran_crm.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 */

$result = fixito_iranize_crm_full($db, $conf->entity);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n";
