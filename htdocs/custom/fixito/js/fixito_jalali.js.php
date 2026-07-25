<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', 1);
}

$res = 0;
if (!$res && file_exists(__DIR__.'/../../../main.inc.php')) {
	$res = @include __DIR__.'/../../../main.inc.php';
}
if (!$res && file_exists(__DIR__.'/../../../../main.inc.php')) {
	$res = @include __DIR__.'/../../../../main.inc.php';
}
if (!$res) {
	die('Include of main fails');
}

header('Content-Type: application/javascript; charset=UTF-8');
header('Cache-Control: max-age=10800, public, must-revalidate');

if (!isModEnabled('fixito') || !getDolGlobalString('FIXITO_JALALI_ENABLED')) {
	print '/* Fixito Jalali disabled */';
	return;
}

print '/* Fixito Jalali helpers */'."\n";
readfile(__DIR__.'/fixito_jalali.inc.js');
