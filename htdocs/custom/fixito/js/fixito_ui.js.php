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

if (!isModEnabled('fixito')) {
	print '/* Fixito UI disabled */';
	return;
}

?>
(function () {
	'use strict';
	document.addEventListener('DOMContentLoaded', function () {
		var nav = document.querySelector('.fixito-hub-nav');
		if (nav && nav.scrollWidth > nav.clientWidth) {
			nav.classList.add('is-scrollable');
		}
		document.querySelectorAll('.fixito-flow-card, .fixito-branch-card').forEach(function (el) {
			el.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					el.click();
				}
			});
		});
	});
})();
