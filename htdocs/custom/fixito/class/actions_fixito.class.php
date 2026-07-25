<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

/**
 * \file    htdocs/custom/fixito/class/actions_fixito.class.php
 * \ingroup fixito
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';
dol_include_once('/fixito/lib/fixito_jalali.lib.php');

/**
 * Fixito hooks – RTL, Jalali, UX
 */
class ActionsFixito extends CommonHookActions
{
	/** @var DoliDB */
	public $db;

	/** @var string */
	public $error = '';

	/** @var string[] */
	public $errors = array();

	/** @var mixed[] */
	public $results = array();

	/** @var ?string */
	public $resprints;

	/**
	 * @param DoliDB $db Database
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Inject RTL font and html lang hints
	 *
	 * @param array<string,mixed> $parameters Parameters
	 * @param CommonObject        $object     Object
	 * @param ?string             $action     Action
	 * @param HookManager         $hookmanager Hook manager
	 * @return int
	 */
	public function addHtmlHeader($parameters, &$object, &$action, $hookmanager)
	{
		global $langs;

		if (!isModEnabled('fixito')) {
			return 0;
		}

		if ($langs->defaultlang === 'fa_IR' || $langs->trans('DIRECTION') === 'rtl') {
			$this->resprints .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.3.3/Vazirmatn-font-face.css">'."\n";
			$this->resprints .= '<style>body, .button, input, select, textarea { font-family: Vazirmatn, Tahoma, sans-serif !important; }</style>'."\n";
		}

		return 0;
	}

	/**
	 * Footer script for Jalali date display on lists
	 *
	 * @param array<string,mixed> $parameters Parameters
	 * @param CommonObject        $object     Object
	 * @param ?string             $action     Action
	 * @param HookManager         $hookmanager Hook manager
	 * @return int
	 */
	public function printCommonFooter($parameters, &$object, &$action, $hookmanager)
	{
		if (!isModEnabled('fixito') || !getDolGlobalString('FIXITO_JALALI_ENABLED')) {
			return 0;
		}

		$this->resprints .= '<script nonce="'.getNonce().'">if (window.FixitoJalali) { FixitoJalali.localizeDisplayedDates(); }</script>';

		return 0;
	}
}
