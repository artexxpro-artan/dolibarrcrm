<?php
/* Copyright (C) 2026 Artexx Pro / Fixito
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * \file       htdocs/custom/fixito/core/modules/modFixito.class.php
 * \ingroup    fixito
 * \brief      Fixito – Iranized CRM layer for Dolibarr (RTL, Jalali, quick deals/sales/warranty)
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Description and activation class for module Fixito
 */
class modFixito extends DolibarrModules
{
	/**
	 * Constructor.
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs;

		$this->db = $db;

		$this->numero = 104900;
		$this->rights_class = 'fixito';
		$this->family = 'crm';
		$this->module_position = '05';
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = 'ModuleFixitoDesc';
		$this->descriptionlong = 'FixitoIranizedCRMDescLong';
		$this->editor_name = 'Artexx Pro';
		$this->editor_url = 'https://artexxpro.ir';
		$this->version = '1.0.0';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'fa-flag';

		$this->module_parts = array(
			'triggers' => 0,
			'login' => 0,
			'substitutions' => 0,
			'menus' => 0,
			'tpl' => 0,
			'barcode' => 0,
			'models' => 0,
			'printing' => 0,
			'theme' => 0,
			'css' => array(
				'/fixito/css/fixito.css.php',
			),
			'js' => array(
				'/fixito/js/fixito_jalali.js.php',
				'/fixito/js/fixito_ui.js.php',
			),
			'hooks' => array(
				'data' => array(
					'main',
					'globalcard',
					'adminmodules',
				),
				'entity' => '0',
			),
			'moduleforexternal' => 0,
		);

		$this->dirs = array('/fixito/temp');
		$this->config_page_url = array('setup.php@fixito');
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->langfiles = array('fixito@fixito');
		$this->phpmin = array(8, 0);
		$this->need_dolibarr_version = array(19, -3);

		$this->const = array(
			1 => array('FIXITO_JALALI_ENABLED', 'chaine', '1', 'Enable Jalali calendar display', 0, 'current', 0),
			2 => array('FIXITO_RTL_ENHANCE', 'chaine', '1', 'Enable RTL UI enhancements', 0, 'current', 0),
			3 => array('FIXITO_DEFAULT_WARRANTY_MONTHS', 'chaine', '12', 'Default warranty duration in months', 0, 'current', 0),
			4 => array('FIXITO_MODERN_UI', 'chaine', '1', 'Enable Fixito modern dashboard UI', 0, 'current', 0),
		);

		$this->overwrite_translation = array(
			'fa_IR:Proposal' => 'معامله',
			'fa_IR:Proposals' => 'معاملات',
			'fa_IR:CommercialProposal' => 'معامله فروش',
			'fa_IR:NewProp' => 'ثبت معامله جدید',
			'fa_IR:NewPropal' => 'ثبت معامله',
			'fa_IR:Propal' => 'معامله',
			'fa_IR:ThirdParty' => 'مشتری / طرف حساب',
			'fa_IR:ThirdParties' => 'مشتریان',
			'fa_IR:Customer' => 'مشتری',
			'fa_IR:Customers' => 'مشتریان',
			'fa_IR:Order' => 'سفارش فروش',
			'fa_IR:Orders' => 'سفارش‌های فروش',
			'fa_IR:Invoice' => 'فاکتور',
			'fa_IR:Invoices' => 'فاکتورها',
			'fa_IR:Opportunity' => 'فرصت فروش',
			'fa_IR:Opportunities' => 'فرصت‌های فروش',
			'fa_IR:FormatDateShort' => '%Y/%m/%d',
			'fa_IR:FormatDateShortInput' => '%Y/%m/%d',
			'fa_IR:FormatDateShortJava' => 'yyyy/MM/dd',
			'fa_IR:FormatDateShortJavaInput' => 'yyyy/MM/dd',
			'fa_IR:FormatDateShortJQuery' => 'yy/mm/dd',
			'fa_IR:FormatDateShortJQueryInput' => 'yy/mm/dd',
			'fa_IR:SeparatorDecimal' => '.',
			'fa_IR:SeparatorThousand' => ',',
		);

		if (!isModEnabled('fixito')) {
			$conf->fixito = new stdClass();
			$conf->fixito->enabled = 0;
		}

		$this->rights = array();
		$r = 0;
		$this->rights[$r][0] = $this->numero.sprintf('%02d', ($r + 1));
		$this->rights[$r][1] = 'ReadFixito';
		$this->rights[$r][4] = 'fixito';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero.sprintf('%02d', ($r + 1));
		$this->rights[$r][1] = 'CreateUpdateFixito';
		$this->rights[$r][4] = 'fixito';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero.sprintf('%02d', ($r + 1));
		$this->rights[$r][1] = 'DeleteFixitoWarranty';
		$this->rights[$r][4] = 'warranty';
		$this->rights[$r][5] = 'delete';
		$r++;

		$this->menu = array();
		$r = 0;
		$this->menu[$r++] = array(
			'fk_menu' => '',
			'type' => 'top',
			'titre' => 'ModuleFixitoName',
			'prefix' => img_picto('', $this->picto, 'class="pictofixedwidth valignmiddle"'),
			'mainmenu' => 'fixito',
			'leftmenu' => '',
			'url' => '/fixito/fixitoindex.php',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoDashboard',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_home',
			'url' => '/fixito/fixitoindex.php',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoHubSales',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_hub_sales',
			'url' => '/fixito/hub.php?branch=sales',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoHubSupport',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_hub_support',
			'url' => '/fixito/hub.php?branch=support',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoHubAccounting',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_hub_accounting',
			'url' => '/fixito/hub.php?branch=accounting',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoHubStock',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_hub_stock',
			'url' => '/fixito/hub.php?branch=stock',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoQuickDeal',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_quickdeal',
			'url' => '/fixito/quickdeal.php',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "write")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoQuickSale',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_quicksale',
			'url' => '/fixito/quicksale.php',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "write")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoWarranty',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_warranty',
			'url' => '/fixito/warranty_list.php',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "read")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoNewWarranty',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_warranty_new',
			'url' => '/fixito/warranty_card.php?action=create',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->hasRight("fixito", "fixito", "write")',
			'target' => '',
			'user' => 2,
		);
		$this->menu[$r++] = array(
			'fk_menu' => 'fk_mainmenu=fixito',
			'type' => 'left',
			'titre' => 'FixitoDeployTitle',
			'mainmenu' => 'fixito',
			'leftmenu' => 'fixito_deploy',
			'url' => '/fixito/admin/deploy.php',
			'langs' => 'fixito@fixito',
			'position' => 1000 + $r,
			'enabled' => "isModEnabled('fixito')",
			'perms' => '$user->admin',
			'target' => '',
			'user' => 0,
		);
	}

	/**
	 * Function called when module is enabled.
	 *
	 * @param string $options Options when enabling module
	 * @return int<-1,1> 1 if OK, <=0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		dol_include_once('/fixito/lib/fixito.lib.php');

		$result = $this->_load_tables('/fixito/sql/');
		if ($result < 0) {
			return -1;
		}

		$this->remove($options);

		fixito_apply_iran_defaults($this->db, $conf->entity);

		$sql = array();

		return $this->_init($sql, $options);
	}
}
