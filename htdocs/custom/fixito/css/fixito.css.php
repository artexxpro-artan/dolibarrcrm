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

if (!isModEnabled('fixito')) {
	return;
}

$modern = getDolGlobalString('FIXITO_MODERN_UI');
$rtl = getDolGlobalString('FIXITO_RTL_ENHANCE');

?>

/* Fixito modern shell */
body.fixito-modern-ui {
	--fixito-radius: 12px;
	--fixito-radius-sm: 8px;
	--fixito-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
	--fixito-shadow-hover: 0 12px 32px rgba(15, 23, 42, 0.12);
	--fixito-border: #e2e8f0;
	--fixito-text: #0f172a;
	--fixito-muted: #64748b;
	--fixito-sales: #0284c7;
	--fixito-support: #7c3aed;
	--fixito-accounting: #059669;
	--fixito-stock: #d97706;
	--fixito-primary: #1d4ed8;
	background: #f1f5f9;
}

body.fixito-modern-ui #id-right {
	background: transparent;
}

body.fixito-modern-ui .fixito-page-content {
	max-width: 1200px;
	margin: 0 auto 2rem;
	padding: 0 0.5rem;
}

/* Hub navigation */
.fixito-hub-nav {
	display: flex;
	flex-wrap: wrap;
	gap: 0.5rem;
	margin: 0 0 1.25rem;
	padding: 0.75rem;
	background: #fff;
	border-radius: var(--fixito-radius);
	border: 1px solid var(--fixito-border);
	box-shadow: var(--fixito-shadow);
	position: sticky;
	top: 0;
	z-index: 50;
}

.fixito-hub-nav.is-scrollable {
	flex-wrap: nowrap;
	overflow-x: auto;
	-webkit-overflow-scrolling: touch;
}

.fixito-hub-nav-item {
	display: inline-flex;
	align-items: center;
	gap: 0.45rem;
	padding: 0.55rem 1rem;
	border-radius: 999px;
	font-size: 0.9rem;
	font-weight: 600;
	color: var(--fixito-muted);
	text-decoration: none !important;
	border: 1px solid transparent;
	transition: background 0.15s, color 0.15s, border-color 0.15s;
	white-space: nowrap;
}

.fixito-hub-nav-item:hover {
	background: #f8fafc;
	color: var(--fixito-text);
}

.fixito-hub-nav-item.is-active {
	background: #eff6ff;
	color: var(--fixito-primary);
	border-color: #bfdbfe;
}

.fixito-hub-nav-sales.is-active { background: #e0f2fe; color: var(--fixito-sales); border-color: #7dd3fc; }
.fixito-hub-nav-support.is-active { background: #ede9fe; color: var(--fixito-support); border-color: #c4b5fd; }
.fixito-hub-nav-accounting.is-active { background: #d1fae5; color: var(--fixito-accounting); border-color: #6ee7b7; }
.fixito-hub-nav-stock.is-active { background: #ffedd5; color: var(--fixito-stock); border-color: #fdba74; }

/* Hero */
.fixito-hub-hero {
	display: flex;
	align-items: center;
	gap: 1.25rem;
	padding: 1.5rem 1.75rem;
	margin-bottom: 1.25rem;
	border-radius: var(--fixito-radius);
	background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 55%, #3b82f6 100%);
	color: #fff;
	box-shadow: var(--fixito-shadow);
}

.fixito-hub-hero-sales { background: linear-gradient(135deg, #0c4a6e, #0284c7); }
.fixito-hub-hero-support { background: linear-gradient(135deg, #4c1d95, #7c3aed); }
.fixito-hub-hero-accounting { background: linear-gradient(135deg, #064e3b, #059669); }
.fixito-hub-hero-stock { background: linear-gradient(135deg, #78350f, #d97706); }
.fixito-hub-hero-dashboard { background: linear-gradient(135deg, #1e293b, #334155); }

.fixito-hub-hero-icon {
	width: 56px;
	height: 56px;
	border-radius: 14px;
	background: rgba(255, 255, 255, 0.15);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.5rem;
	flex-shrink: 0;
}

.fixito-hub-hero-title {
	margin: 0 0 0.35rem;
	font-size: 1.45rem;
	font-weight: 700;
	color: #fff !important;
}

.fixito-hub-hero-sub {
	margin: 0;
	opacity: 0.92;
	font-size: 0.95rem;
	line-height: 1.5;
}

/* KPI */
.fixito-kpi-row {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
	gap: 0.75rem;
	margin-bottom: 1.5rem;
}

.fixito-kpi-card {
	background: #fff;
	border: 1px solid var(--fixito-border);
	border-radius: var(--fixito-radius-sm);
	padding: 1rem;
	text-align: center;
	box-shadow: var(--fixito-shadow);
}

.fixito-kpi-icon {
	display: block;
	font-size: 1.1rem;
	margin-bottom: 0.35rem;
	opacity: 0.85;
}

.fixito-kpi-value {
	display: block;
	font-size: 1.65rem;
	font-weight: 800;
	color: var(--fixito-text);
	line-height: 1.2;
}

.fixito-kpi-label {
	display: block;
	font-size: 0.78rem;
	color: var(--fixito-muted);
	margin-top: 0.25rem;
}

.fixito-kpi-sales .fixito-kpi-icon { color: var(--fixito-sales); }
.fixito-kpi-support .fixito-kpi-icon { color: var(--fixito-support); }
.fixito-kpi-accounting .fixito-kpi-icon { color: var(--fixito-accounting); }

.fixito-section-title {
	font-size: 1.05rem;
	font-weight: 700;
	color: var(--fixito-text);
	margin: 1.5rem 0 0.75rem;
}

/* Branch cards */
.fixito-branch-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
	gap: 1rem;
	margin-bottom: 1rem;
}

.fixito-branch-card {
	display: flex;
	flex-direction: column;
	gap: 0.35rem;
	padding: 1.25rem;
	background: #fff;
	border: 1px solid var(--fixito-border);
	border-radius: var(--fixito-radius);
	text-decoration: none !important;
	box-shadow: var(--fixito-shadow);
	transition: transform 0.15s, box-shadow 0.15s;
	border-top: 4px solid var(--fixito-primary);
}

.fixito-branch-card:hover {
	transform: translateY(-2px);
	box-shadow: var(--fixito-shadow-hover);
}

.fixito-branch-sales { border-top-color: var(--fixito-sales); }
.fixito-branch-support { border-top-color: var(--fixito-support); }
.fixito-branch-accounting { border-top-color: var(--fixito-accounting); }
.fixito-branch-stock { border-top-color: var(--fixito-stock); }

.fixito-branch-icon {
	font-size: 1.35rem;
	color: var(--fixito-text);
}

.fixito-branch-title {
	font-size: 1.05rem;
	font-weight: 700;
	color: var(--fixito-text);
}

.fixito-branch-desc {
	font-size: 0.85rem;
	color: var(--fixito-muted);
	line-height: 1.45;
	flex: 1;
}

.fixito-branch-cta {
	font-size: 0.8rem;
	font-weight: 600;
	color: var(--fixito-primary);
	margin-top: 0.5rem;
}

/* Quick actions */
.fixito-quick-row {
	display: flex;
	flex-wrap: wrap;
	gap: 0.65rem;
	margin-bottom: 1.5rem;
}

.fixito-quick-btn {
	display: inline-flex;
	align-items: center;
	gap: 0.5rem;
	padding: 0.65rem 1.15rem;
	border-radius: var(--fixito-radius-sm);
	background: #fff;
	border: 1px solid var(--fixito-border);
	font-weight: 600;
	font-size: 0.9rem;
	color: var(--fixito-text);
	text-decoration: none !important;
	box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

.fixito-quick-btn.is-primary {
	background: var(--fixito-primary);
	border-color: #1e40af;
	color: #fff !important;
}

.fixito-quick-btn.is-primary:hover {
	background: #1e40af;
}

.fixito-alert-banner {
	padding: 0.85rem 1rem;
	background: #fffbeb;
	border: 1px solid #fcd34d;
	border-radius: var(--fixito-radius-sm);
	color: #92400e;
	font-size: 0.9rem;
}

.fixito-alert-banner a {
	font-weight: 700;
}

/* Flow grid (branch steps) */
.fixito-flow-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 1rem;
}

.fixito-flow-card {
	position: relative;
	display: flex;
	flex-direction: column;
	gap: 0.4rem;
	padding: 1.15rem 1rem 1rem 1rem;
	min-height: 130px;
	background: #fff;
	border: 1px solid var(--fixito-border);
	border-radius: var(--fixito-radius);
	text-decoration: none !important;
	box-shadow: var(--fixito-shadow);
	transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
}

.fixito-flow-card:hover {
	transform: translateY(-2px);
	box-shadow: var(--fixito-shadow-hover);
	border-color: #cbd5e1;
}

.fixito-flow-card-primary {
	border-color: #93c5fd;
	background: linear-gradient(180deg, #eff6ff 0%, #fff 40%);
}

.fixito-flow-step-num {
	position: absolute;
	top: 0.65rem;
	left: 0.75rem;
	width: 22px;
	height: 22px;
	border-radius: 50%;
	background: #e2e8f0;
	color: #475569;
	font-size: 0.7rem;
	font-weight: 800;
	display: flex;
	align-items: center;
	justify-content: center;
}

html[dir="rtl"] .fixito-flow-step-num,
body[style*="direction: rtl"] .fixito-flow-step-num {
	left: auto;
	right: 0.75rem;
}

.fixito-flow-icon {
	font-size: 1.35rem;
	color: var(--fixito-primary);
	margin-top: 0.5rem;
}

.fixito-flow-label {
	font-weight: 700;
	font-size: 0.95rem;
	color: var(--fixito-text);
}

.fixito-flow-desc {
	font-size: 0.8rem;
	color: var(--fixito-muted);
	line-height: 1.4;
}

/* Forms inside Fixito shell */
body.fixito-modern-ui .fixito-form-panel {
	background: #fff;
	border: 1px solid var(--fixito-border);
	border-radius: var(--fixito-radius);
	padding: 1.25rem 1.5rem;
	box-shadow: var(--fixito-shadow);
	margin-top: 0.5rem;
}

body.fixito-modern-ui .fixito-form-panel .border.centpercent {
	border: none !important;
}

body.fixito-modern-ui .fixito-form-panel .button {
	border-radius: var(--fixito-radius-sm);
	padding: 0.55rem 1.5rem;
	font-weight: 600;
}

body.fixito-modern-ui .fixito-lead-hint {
	margin: 0 0 1rem;
	padding: 0.75rem 1rem;
	background: #f8fafc;
	border-radius: var(--fixito-radius-sm);
	border-right: 3px solid var(--fixito-primary);
	color: var(--fixito-muted);
	font-size: 0.9rem;
	line-height: 1.5;
}

html[dir="ltr"] body.fixito-modern-ui .fixito-lead-hint {
	border-right: none;
	border-left: 3px solid var(--fixito-primary);
}

/* Legacy dashboard cards (fallback) */
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
	border-radius: var(--fixito-radius-sm, 8px);
	border: 1px solid var(--fixito-border, #d0d0d0);
	background: #fff;
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
.fixito-jalali-input {
	direction: ltr;
	text-align: center;
}

<?php if ($rtl) { ?>
html[dir="rtl"] .fixito-card a,
body[style*="direction: rtl"] .fixito-card a {
	flex-direction: row-reverse;
	text-align: right;
}
html[dir="rtl"] .fixito-hub-hero,
body[style*="direction: rtl"] .fixito-hub-hero {
	flex-direction: row-reverse;
	text-align: right;
}
html[dir="rtl"] .fixito-branch-cta .fa-arrow-left:before,
body[style*="direction: rtl"] .fixito-branch-cta .fa-arrow-left:before {
	content: "\f061";
}
<?php } ?>
