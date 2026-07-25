<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

/**
 * \file       htdocs/custom/fixito/class/warranty.class.php
 * \ingroup    fixito
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

/**
 * Warranty registry for after-sales / گارانتی
 */
class FixitoWarranty extends CommonObject
{
	/** @var string */
	public $module = 'fixito';

	/** @var string */
	public $element = 'fixitowarranty';

	/** @var string */
	public $table_element = 'fixito_warranty';

	/** @var string */
	public $picto = 'fa-shield';

	/** @var int */
	public $ismultientitymanaged = 1;

	const STATUS_ACTIVE = 1;
	const STATUS_EXPIRED = 2;
	const STATUS_CANCELLED = 9;

	/** @var array<string,array<string,mixed>> */
	public $fields = array(
		'rowid' => array('type' => 'integer', 'label' => 'TechnicalID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0),
		'ref' => array('type' => 'varchar(128)', 'label' => 'Ref', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1, 'index' => 1),
		'entity' => array('type' => 'integer', 'label' => 'Entity', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 0, 'default' => '1'),
		'fk_soc' => array('type' => 'integer:Societe:societe/class/societe.class.php', 'label' => 'ThirdParty', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
		'fk_product' => array('type' => 'integer:Product:product/class/product.class.php', 'label' => 'Product', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
		'serial_number' => array('type' => 'varchar(128)', 'label' => 'FixitoSerialNumber', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1, 'searchall' => 1),
		'label' => array('type' => 'varchar(255)', 'label' => 'Label', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
		'date_sale' => array('type' => 'date', 'label' => 'FixitoDateSale', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
		'date_warranty_end' => array('type' => 'date', 'label' => 'FixitoWarrantyEnd', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
		'warranty_months' => array('type' => 'integer', 'label' => 'FixitoWarrantyMonths', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1, 'default' => '12'),
		'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 200, 'notnull' => 1, 'visible' => 1),
	);

	/** @var int */
	public $rowid;

	/** @var string */
	public $ref;

	/** @var int */
	public $fk_soc;

	/** @var int */
	public $fk_product;

	/** @var string */
	public $serial_number;

	/** @var string */
	public $label;

	/** @var int */
	public $date_sale;

	/** @var int */
	public $date_warranty_end;

	/** @var int */
	public $warranty_months;

	/** @var int */
	public $status;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Create warranty record
	 *
	 * @param User $user User
	 * @param int  $notrigger No trigger
	 * @return int
	 */
	public function create($user, $notrigger = 0)
	{
		global $conf;

		$error = 0;
		$now = dol_now();

		if (empty($this->ref)) {
			$this->ref = '(PROV)';
		}
		if (empty($this->entity)) {
			$this->entity = $conf->entity;
		}
		if (empty($this->warranty_months)) {
			$this->warranty_months = (int) getDolGlobalString('FIXITO_DEFAULT_WARRANTY_MONTHS', 12);
		}
		if (!empty($this->date_sale) && empty($this->date_warranty_end)) {
			$this->date_warranty_end = dol_time_plus_duree($this->date_sale, $this->warranty_months, 'm');
		}
		if (empty($this->status)) {
			$this->status = self::STATUS_ACTIVE;
		}

		$sql = "INSERT INTO ".$this->db->prefix().$this->table_element."(";
		$sql .= "ref, entity, fk_soc, fk_product, serial_number, label,";
		$sql .= "date_sale, date_warranty_end, warranty_months, status,";
		$sql .= "date_creation, fk_user_creat";
		$sql .= ") VALUES (";
		$sql .= "'".$this->db->escape($this->ref)."',";
		$sql .= ((int) $this->entity).",";
		$sql .= ($this->fk_soc > 0 ? ((int) $this->fk_soc) : 'NULL').",";
		$sql .= ($this->fk_product > 0 ? ((int) $this->fk_product) : 'NULL').",";
		$sql .= "'".$this->db->escape($this->serial_number)."',";
		$sql .= "'".$this->db->escape($this->label)."',";
		$sql .= (!empty($this->date_sale) ? "'".$this->db->idate($this->date_sale)."'" : 'NULL').",";
		$sql .= (!empty($this->date_warranty_end) ? "'".$this->db->idate($this->date_warranty_end)."'" : 'NULL').",";
		$sql .= ((int) $this->warranty_months).",";
		$sql .= ((int) $this->status).",";
		$sql .= "'".$this->db->idate($now)."',";
		$sql .= ((int) $user->id);
		$sql .= ")";

		$this->db->begin();
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = $this->db->lasterror();
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id($this->db->prefix().$this->table_element);
			if ($this->ref === '(PROV)') {
				$this->ref = 'GRT-'.sprintf('%05d', $this->id);
				$sqlu = "UPDATE ".$this->db->prefix().$this->table_element." SET ref='".$this->db->escape($this->ref)."' WHERE rowid=".(int) $this->id;
				$this->db->query($sqlu);
			}
		}

		if ($error) {
			$this->db->rollback();
			return -1;
		}
		$this->db->commit();
		return $this->id;
	}

	/**
	 * Load object
	 *
	 * @param int    $id  Id
	 * @param string $ref Ref
	 * @return int
	 */
	public function fetch($id, $ref = '')
	{
		$sql = "SELECT * FROM ".$this->db->prefix().$this->table_element;
		if ($id > 0) {
			$sql .= " WHERE rowid = ".((int) $id);
		} else {
			$sql .= " WHERE ref = '".$this->db->escape($ref)."'";
		}
		$sql .= " AND entity IN (".getEntity('fixitowarranty').")";

		$resql = $this->db->query($sql);
		if ($resql && ($obj = $this->db->fetch_object($resql))) {
			$this->id = $obj->rowid;
			$this->rowid = $obj->rowid;
			$this->ref = $obj->ref;
			$this->entity = $obj->entity;
			$this->fk_soc = $obj->fk_soc;
			$this->fk_product = $obj->fk_product;
			$this->serial_number = $obj->serial_number;
			$this->label = $obj->label;
			$this->date_sale = $this->db->jdate($obj->date_sale);
			$this->date_warranty_end = $this->db->jdate($obj->date_warranty_end);
			$this->warranty_months = $obj->warranty_months;
			$this->status = $obj->status;
			return 1;
		}
		return 0;
	}

	/**
	 * Update warranty
	 *
	 * @param User $user User
	 * @return int
	 */
	public function update($user)
	{
		if (!empty($this->date_sale) && !empty($this->warranty_months)) {
			$this->date_warranty_end = dol_time_plus_duree($this->date_sale, $this->warranty_months, 'm');
		}
		$sql = "UPDATE ".$this->db->prefix().$this->table_element." SET";
		$sql .= " fk_soc=".($this->fk_soc > 0 ? ((int) $this->fk_soc) : 'NULL').",";
		$sql .= " fk_product=".($this->fk_product > 0 ? ((int) $this->fk_product) : 'NULL').",";
		$sql .= " serial_number='".$this->db->escape($this->serial_number)."',";
		$sql .= " label='".$this->db->escape($this->label)."',";
		$sql .= " date_sale=".(!empty($this->date_sale) ? "'".$this->db->idate($this->date_sale)."'" : 'NULL').",";
		$sql .= " date_warranty_end=".(!empty($this->date_warranty_end) ? "'".$this->db->idate($this->date_warranty_end)."'" : 'NULL').",";
		$sql .= " warranty_months=".((int) $this->warranty_months).",";
		$sql .= " status=".((int) $this->status).",";
		$sql .= " fk_user_modif=".((int) $user->id);
		$sql .= " WHERE rowid=".(int) $this->id;

		$resql = $this->db->query($sql);
		return $resql ? 1 : -1;
	}

	/**
	 * Delete
	 *
	 * @param User $user User
	 * @return int
	 */
	public function delete($user)
	{
		$sql = "DELETE FROM ".$this->db->prefix().$this->table_element." WHERE rowid=".(int) $this->id;
		$resql = $this->db->query($sql);
		return $resql ? 1 : -1;
	}

	/**
	 * Status label
	 *
	 * @param int $mode Mode
	 * @return string
	 */
	public function getLibStatut($mode = 0)
	{
		global $langs;
		$langs->load('fixito@fixito');
		if ($this->status == self::STATUS_ACTIVE) {
			return $langs->trans('FixitoWarrantyActive');
		}
		if ($this->status == self::STATUS_EXPIRED) {
			return $langs->trans('FixitoWarrantyExpired');
		}
		return $langs->trans('FixitoWarrantyCancelled');
	}
}
