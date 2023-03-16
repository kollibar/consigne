<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file        class/consigneproduct.class.php
 * \ingroup     consigne
 * \brief       This file is a CRUD class file for ConsigneProduct (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for ConsigneProduct
 */
class ConsigneProduct extends CommonObject
{

	
	const AJOUT_A_LA_FACTURE = 1;
	const AJOUT_A_LA_COMMANDE_PROPAL = 0;

	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'consigneproduct';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'consigne_consigneproduct';
	/**
	 * @var int  Does consigneproduct support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	public $ismultientitymanaged = 0;
	/**
	 * @var int  Does consigneproduct support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;
	/**
	 * @var string String with name of icon for consigneproduct. Must be the part after the 'object_' into object_consigneproduct.png
	 */
	public $picto = 'consigneproduct@consigne';


	/**
	 *  'type' if the field format.
	 *  'label' the translation key.
	 *  'enabled' is a condition when the field must be managed.
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only. Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'position' is the sort order of field.
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'help' is a string visible as a tooltip on field
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *  'default' is a default value for creation (can still be replaced by the global setup of default values)
	 *  'showoncombobox' if field must be shown into the label of combobox
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		// tous les produits
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'visible'=>-1, 'enabled'=>1, 'position'=>1, 'notnull'=>1, 'index'=>1, 'comment'=>"Id",),
		'entity' => array('type'=>'integer', 'label'=>'Entity', 'visible'=>-1, 'enabled'=>1, 'position'=>20, 'notnull'=>1, 'index'=>1,),
		'description' => array('type'=>'text', 'label'=>'Descrption', 'visible'=>-1, 'enabled'=>1, 'position'=>60, 'notnull'=>-1,),
		'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'visible'=>-2, 'enabled'=>1, 'position'=>500, 'notnull'=>1,),
		'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'visible'=>-2, 'enabled'=>1, 'position'=>501, 'notnull'=>1,),
		'fk_user_creat' => array('type'=>'integer', 'label'=>'UserAuthor', 'visible'=>-2, 'enabled'=>1, 'position'=>510, 'notnull'=>1,),
		'fk_user_modif' => array('type'=>'integer', 'label'=>'UserModif', 'visible'=>-2, 'enabled'=>1, 'position'=>511, 'notnull'=>-1,),
		'status' => array('type'=>'integer', 'label'=>'Status', 'visible'=>1, 'enabled'=>1, 'position'=>1000, 'notnull'=>1, 'index'=>1,
					'arrayofkeyval'=>array('0'=>'Draft', '1'=>'Active', '-1'=>'Cancel')),
		'fk_product' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'Product', 'visible'=>-1, 'enabled'=>1, 'position'=>2, 'notnull'=>1, 'index'=>1, 
					'comment'=>"id produit associé",),
		'est_emballage_consigne' => array('type'=>'boolean', 'label'=>'EstEmballageConsigne', 'visible'=>1, 'enabled'=>1, 'position'=>10, 'notnull'=>1, 
					'default'=> false ,'comment'=>"0: non, 1: oui",),
		'est_emballage_consigne_vendu' => array('type'=>'boolean', 'label'=>'EstEmballageConsigneVendu', 'visible'=>1, 'enabled'=>1, 'position'=>45, 'notnull'=>1,
					'default' => false, 'comment'=>"0: non, 1: oui indique si ce produit correspond à un emballage consigne vendu",),

		// produit ni emballage_consigne ni emballage_consigne_vendu
		'fk_product_emballage_consigne' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'ProduitEmballageConsigne', 'visible'=>1, 'enabled'=>1, 'position'=>40, 
					'notnull'=>-1, 'default' => null, 'comment'=>"si est le produit vendu d'un emballage consigné non retourné sinon null",),

		// produits emballage_consigne
		'fk_product_emballage_vendu' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'EmballageVendu', 'visible'=>1, 'enabled'=>'($object->est_emballage_consigne)', 
					'position'=>20, 'notnull'=>-1, 'default' => null, 'comment'=>"si le produit vendu doit être différent du produit consigné (pour compta)",),
		'fk_product_emballage_retour' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'EmballageRetour', 'visible'=>1, 'enabled'=>'($object->est_emballage_consigne)',
					'position'=>25, 'notnull'=>-1, 'default' => null, 'comment'=>"si le produit en retour doit être différent du produit consigné (pour compta)",),
		'suivi_emballage' => array('type'=>'boolean', 'label'=>'SuiviEmballage', 'visible'=>1, 'enabled'=>'($object->est_emballage_consigne)', 'position'=>15, 'notnull'=>-1, 
					'comment'=>"0: non, 1: oui         indique si l'emballage doit être suivi par client",),
		'ajout_a' => array('type'=>'integer', 'label'=>'AjoutA', 'enabled'=>'($object->est_emballage_consigne)', 'visible'=>1, 'position'=>50, 'notnull'=>-1,'default'=>'0',
					'arrayofkeyval'=>array('0'=>'LaCommandePropal','1'=>'LaFacture')),
		'indissociable' => array('type'=>'boolean', 'label'=>'IndissociableDeSonProduit', 'visible'=>1, 'enabled'=>'($object->est_emballage_consigne)', 'position'=>51, 'notnull'=>-1, 
					'default' => false, 'comment'=>"0: non, 1: oui indique si ce produit (consigne) est indissociable du produit auquel il est lié (quatité maintenue identique)",),
		'prix_produit_inclu_consigne' => array('type'=>'boolean', 'label'=>'prixProduitIncluConsigne', 'visible'=>1, 'enabled'=>'($object->est_emballage_consigne && $object->ajout_a == 1)', 
					'position'=>53, 'notnull'=>-1, 'default' => false,'comment'=>"0: non, 1: oui indique si ce produit (consigne) est indissociable du produit auquel il est lié (quatité maintenue identique)",),

		'est_cache_bordereau_livraison' => array('type'=>'boolean', 'label'=>'EstCacheBordereauLivraison', 'visible'=>1, 'enabled'=>'($object->est_emballage_consigne && $object->ajout_a == 1)',
					'position'=>52, 'notnull'=>-1, 'default' => false, 'comment'=>"0: non, 1: oui indique si ce produit est masqué sur les bordereau de livraison",),

		// a supprimer prochainement
		'colisage' => array('type'=>'integer', 'label'=>'ColisagePar', 'visible'=>1, 'enabled'=>1, 'position'=>55, 'notnull'=>-1,),
	);
	public $rowid;
	public $description;
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $status;
	public $fk_product;
	public $entity;
	public $est_emballage_consigne;
	public $suivi_emballage;
	public $fk_product_emballage_vendu;
	public $fk_product_emballage_retour;
	public $fk_product_emballage_consigne;
	public $est_emballage_consigne_vendu;
	public $colisage;

	public $est_cache_bordereau_livraison;
	public $ajout_a;
	public $indissociable;
	public $prix_produit_inclu_consigne;
	// END MODULEBUILDER PROPERTIES

	/* mode d'ajout consigne :
	*
	*	* ajout à la commande/propal
	*			-> masqué (ou non) sur les bl
	*			-> indissociable (ou non)
	*	* ajout à la facturation
	*			-> indissociable
	*			-> prix produit inclue la consigne (ou non)
	*/

	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'consigneproductdet';
	/**
	 * @var int    Field with ID of parent key if this field has a parent
	 */
	//public $fk_element = 'fk_consigneproduct';
	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'ConsigneProductline';
	/**
	 * @var array  Array of child tables (child tables to delete before deleting a record)
	 */
	//protected $childtables=array('consigneproductdet');
	/**
	 * @var ConsigneProductLine[]     Array of subtable lines
	 */
	//public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf;
		global $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID)) $this->fields['rowid']['visible']=0;
		if (empty($conf->multicompany->enabled)) $this->fields['entity']['enabled']=0;

		// traduit tous les champs à choix multiple de fields
		foreach ($this->fields as $key => $value) {
			if (!empty($this->fields[$key]['arrayofkeyval']) && is_array($this->fields[$key]['arrayofkeyval'])) {
				foreach ($this->fields[$key]['arrayofkeyval'] as $k => $v) {
					$this->fields[$key]['arrayofkeyval'][$k]=$langs->trans($v);
				}
			}
		}
	}


	/**
	 * Initialise un objet consigneproduct par defaut
	 *
	 */

	public function init()
	{
		$this->now=dol_now();
		//public $rowid;
		$this->description='';
		$this->date_creation=$now;
		$this->tms=$now;
		//public $fk_user_creat;
		//public $fk_user_modif;
		$this->status=0;
		$this->fk_product=0;
		$this->entity=0;
		$this->est_emballage_consigne=0;
		$this->suivi_emballage=0;
		$this->fk_product_emballage_vendu=null;
		$this->fk_product_emballage_retour=null;
		$this->fk_product_emballage_consigne=null;
		$this->est_emballage_consigne_vendu=0;
		$this->est_cache_bordereau_livraison=0;
		$this->colisage=null;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		return $this->createCommon($user, $notrigger);
	}

	/**
	 * Clone and object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $hookmanager, $langs;
	    $error = 0;

	    dol_syslog(__METHOD__, LOG_DEBUG);

	    $object = new self($this->db);

	    $this->db->begin();

	    // Load source object
	    $object->fetchCommon($fromid);
	    // Reset some properties
	    unset($object->id);
	    unset($object->fk_user_creat);
	    unset($object->import_key);

	    // Clear fields
	    $object->ref = "copy_of_".$object->ref;
	    $object->title = $langs->trans("CopyOf")." ".$object->title;
	    // ...

	    // Create clone
		$object->context['createfromclone'] = 'createfromclone';
	    $result = $object->createCommon($user);
	    if ($result < 0) {
	        $error++;
	        $this->error = $object->error;
	        $this->errors = $object->errors;
	    }

	    // End
	    if (!$error) {
	        $this->db->commit();
	        return $object;
	    } else {
	        $this->db->rollback();
	        return -1;
	    }
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && ! empty($this->table_element_line)) $this->fetchLines();
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	/*public function fetchLines()
	{
		$this->lines=array();

		// Load lines with object ConsigneProductLine

		return count($this->lines)?1:0;
	}*/

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		$error = 0;

		if (isset($this->description)) $this->ref=trim($this->description);

		$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
		$sql.= " SET description = '" . $this->db->escape($this->description) ."'";
		$sql.= ", tms = '".$this->db->idate(dol_now()) ."'";

		$sql.= ", est_cache_bordereau_livraison = '" . (! $this->est_cache_bordereau_livraison ? 0: 1) ."'";
		$sql.= ", colisage = ".$this->colisage;

		$sql.= ", est_emballage_consigne = '" . (! $this->est_emballage_consigne ? 0: 1) ."'";
		$sql.= ", est_emballage_consigne_vendu = '" . (! $this->est_emballage_consigne_vendu ? 0: 1) ."'";
		$sql.= ", suivi_emballage = '" . (! $this->suivi_emballage ? 0: 1) ."'";

		$sql.= ", fk_product_emballage_vendu = '" . ( ! $this->fk_product_emballage_vendu ? "-1" : $this->fk_product_emballage_vendu )."'";
		$sql.= ", fk_product_emballage_retour = '" . ( ! $this->fk_product_emballage_retour ? "-1" : $this->fk_product_emballage_retour ) ."'";
		$sql.= ", fk_product_emballage_consigne = '" . ( ! $this->fk_product_emballage_consigne ? "-1" : $this->fk_product_emballage_consigne ) ."'";

		$sql.= ", status = '" . $this->db->escape($this->status) ."'";
		$sql.= ", entity = '" . $this->db->escape($this->entity) ."'";
		$sql.= ", fk_user_modif = ".($user->id > 0 ? $user->id : 'NULL');
		$sql.= ", fk_product = ".$this->fk_product;



		$sql.= " WHERE rowid = " . $this->id;

		dol_syslog(get_class($this)."::update", LOG_DEBUG);


		$this->db->begin();
		if (! $error)
		{
			$res = $this->db->query($sql);
			if ($res===false)
			{
				$error++;
				$this->errors[] = $this->db->lasterror();
			}
		}

		// Update extrafield
		if (! $error)
		{
			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{
				$result=$this->insertExtraFields();
				if ($result < 0)
				{
					$error++;
				}
			}
		}

		// Triggers
		if (! $error && ! $notrigger)
		{
			// Call triggers
			$result=$this->call_trigger(strtoupper(get_class($this)).'_MODIFY',$user);
			if ($result < 0) { $error++; } //Do also here what you must do to rollback action if trigger fail
			// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();
			return -1;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		return $this->deleteCommon($user, $notrigger);
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto					Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option						On what the link point to ('nolink', ...)
     *  @param	int  	$notooltip					1=Disable tooltip
     *  @param  string  $morecss            		Add more css on link
     *  @param  int     $save_lastsearch_value    	-1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *	@return	string								String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $morecss='', $save_lastsearch_value=-1)
	{
		global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("ConsigneProduct") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = dol_buildpath('/consigne/consigneproduct_card.php',1).'?id='.$this->id;

        if ($option != 'nolink')
        {
	        // Add param to save lastsearch_values or not
	        $add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
	        if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
	        if ($add_save_lastsearch_values) $url.='&save_lastsearch_values=1';
        }

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowConsigneProduct");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		$result .= $linkstart;
		if ($withpicto) $result.=img_object(($notooltip?'':$label), ($this->picto?$this->picto:'generic'), ($notooltip?(($withpicto != 2) ? 'class="paddingright"' : ''):'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip?0:1);
		if ($withpicto != 2) $result.= $this->ref;
		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		return $result;
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}

	/**
	 *	Charge les informations d'ordre info dans l'objet commande
	 *
	 *	@param  int		$id       Id of order
	 *	@return	void
	 */
	function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql.= ' fk_user_creat, fk_user_modif';
		$sql.= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql.= ' WHERE t.rowid = '.$id;
		$result=$this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation   = $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture   = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);

		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}


	/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK
	 *
	 * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	public function doScheduledJob()
	{
		global $conf, $langs;

		$this->output = '';
		$this->error='';

		dol_syslog(__METHOD__, LOG_DEBUG);

		// ...

		return 0;
	}

	public function check(){

		if( $this->est_emballage_consigne =="1") { // est un emballage consigne
			//$this->est_emballage_consigne =1;

			// donc ne peut pas avoir de consigne
			$this->fk_product_emballage_consigne = null;
			// et ne peut pas être un emballage vendu
			$this->est_emballage_consigne_vendu = "0";
		} else  { // n'est pas un emballage consigne
			// donc ne peut pas avoir ni emballage consigne vendu, ni emballage consigne retourn
			$this->fk_product_emballage_consigne_vendu=null;
			$this->fk_product_emballage_consigne_retour=null;
		}

		if( $this->est_emballage_consigne_vendu  =="1") { // est un emballage consigne vendu
			//$this->est_emballage_consigne_vendu =1;

			// donc ne peut pas avoir de consigne
			$this->fk_product_emballage_consigne = null;
			// ni d'emballage consigne vendu
			$this->fk_product_emballage_vendu = null;
			// ni d'emballage de retour de consigne
			$this->fk_product_emballage_retour = null;
		}

		return true;
	}
}

/**
 * Class ConsigneProductLine. You can also remove this and generate a CRUD class for lines objects.
 */
/*
class ConsigneProductLine
{
	// @var int ID
	public $id;
	// @var mixed Sample line property 1
	public $prop1;
	// @var mixed Sample line property 2
	public $prop2;
}
*/
