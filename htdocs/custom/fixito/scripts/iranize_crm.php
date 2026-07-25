<?php
/* Copyright (C) 2026 Artexx Pro / Fixito — CLI: php scripts/iranize_crm.php (from htdocs/custom/fixito) */

if (php_sapi_name() !== 'cli') {
	die('CLI only');
}

if (!defined('NOLOGIN')) {
	define('NOLOGIN', '1');
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1');
}
if (!defined('NOREQUIREUSER')) {
	define('NOREQUIREUSER', '1');
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', '1');
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
