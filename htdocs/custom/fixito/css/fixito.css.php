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

header('Content-Type: text/css; charset=UTF-8');
header('Cache-Control: max-age=10800, public, must-revalidate');

if (!isModEnabled('fixito') || !getDolGlobalString('FIXITO_RTL_ENHANCE')) {
	return;
}

?>

/* Fixito RTL & Persian UX */
.fixito-dashboard-cards {
	display: flex;
	flex-wrap: wrap;
	gap: 1rem;
	margin: 1rem 0;
}
.fixito-card {
	flex: 1 1 220px;
	min-width: 200px;
	padding: 1rem 1.25rem;
	border-radius: 8px;
	border: 1px solid #d0d0d0;
	background: #fafafa;
}
.fixito-card a {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	font-size: 1.1rem;
	font-weight: 600;
	text-decoration: none;
}
.fixito-card-icon {
	font-size: 1.5rem;
	color: #263c5c;
}
.fixito-form .tableforfieldcreate td.fieldrequired::after {
	content: " *";
	color: #c00;
}
html[dir="rtl"] .fixito-card a,
body[style*="direction: rtl"] .fixito-card a {
	flex-direction: row-reverse;
	text-align: right;
}
.fixito-jalali-input {
	direction: ltr;
	text-align: center;
}
