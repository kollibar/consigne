<?php
/* Copyright (C) 2018 Thomas Kolli <thomas@brasserieteddybeer.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    core/triggers/interface_99_modConsigne_ConsigneTriggers.class.php
 * \ingroup consigne
 * \brief   Example trigger.
 *
 * Put detailed description here.
 *
 * \remarks You can create other triggers by copying this one.
 * - File name should be either:
 *      - interface_99_modConsigne_MyTrigger.class.php
 *      - interface_99_all_MyTrigger.class.php
 * - The file must stay in core/triggers
 * - The class name must be InterfaceMytrigger
 * - The constructor method must be named InterfaceMytrigger
 * - The name property name must be MyTrigger
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for Consigne module
 */
class InterfaceConsigneTriggers extends DolibarrTriggers
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "demo";
		$this->description = "Consigne triggers.";
		// 'development', 'experimental', 'dolibarr' or version
		$this->version = 'development';
		$this->picto = 'consigne@consigne';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc()
	{
		return $this->description;
	}


	/**
	 * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param string 		$action 	Event action code
	 * @param CommonObject 	$object 	Object
	 * @param User 			$user 		Object user
	 * @param Translate 	$langs 		Object langs
	 * @param Conf 			$conf 		Object conf
	 * @return int              		<0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
        if (empty($conf->consigne->enabled)) return 0;     // Module not active, we do nothing

	    // Put here code you want to execute when a Dolibarr business events occurs.
		// Data and type of action are stored into $object and $action

	include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	include_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
	include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
	include_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
	//include_once DOL_DOCUMENT_ROOT.'/commande/class/api_orders.class.php';

	dol_include_once('/consigne/class/consigneproduct.class.php');
	dol_include_once('/consigne/class/facturationconsigne.class.php');
	dol_include_once('/consigne/class/mouvementconsigne.class.php');
	dol_include_once('/consigne/class/retourconsigne.class.php');
	
	dol_include_once('/consigne/lib/liaisons.lib.php');

	$db=$object->db;

	// chargement des champs extra
	$extrafields = new ExtraFields($db);


	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

        switch ($action) {
/*
            // Users
		    case 'USER_CREATE':
		    case 'USER_MODIFY':
		    case 'USER_NEW_PASSWORD':
		    case 'USER_ENABLEDISABLE':
		    case 'USER_DELETE':
		    case 'USER_SETINGROUP':
		    case 'USER_REMOVEFROMGROUP':

		    case 'USER_LOGIN':
		    case 'USER_LOGIN_FAILED':
		    case 'USER_LOGOUT':
		    case 'USER_UPDATE_SESSION':      // Warning: To increase performances, this action is triggered only if constant MAIN_ACTIVATE_UPDATESESSIONTRIGGER is set to 1.

		        // Actions
		    case 'ACTION_MODIFY':
		    case 'ACTION_CREATE':
		    case 'ACTION_DELETE':

		        // Groups
		    case 'GROUP_CREATE':
		    case 'GROUP_MODIFY':
		    case 'GROUP_DELETE':

		        // Companies
		    case 'COMPANY_CREATE':
		    case 'COMPANY_MODIFY':
		    case 'COMPANY_DELETE':

		        // Contacts
		    case 'CONTACT_CREATE':
		    case 'CONTACT_MODIFY':
		    case 'CONTACT_DELETE':
		    case 'CONTACT_ENABLEDISABLE':

			break;*/
		        // Products
		    case 'PRODUCT_CREATE':
					$product=new Product($db);

					$consigneProduct = new ConsigneProduct($db);
					$object->id;
					$id=0;
					while( $id < $object->id){
						$consigneProduct->init();
						$consigneProduct->fk_product=$object->id;
						$id=$consigneProduct->create($user);
						if( $id < 0) {
							/// A FAIRE => ajouter log erreur
							return -1;
						}

						dol_syslog(__FILE__.": Suppresion des consigneProduct non liées à un objet",LOG_DEBUG);
						if( $id < $object->id){
							$sqlP = "SELECT * FROM ".MAIN_DB_PREFIX.$product->table_element." as t WHERE t.rowid = $id";
							$resqlP = $db->query($sqlP);
							$nbP = $db->num_rows($resqlP);

							if( $nbP === 0 ) { // aucun produit avec cet id
								dol_syslog(__FILE__.": Suppression consigneProduct ". $consignedProduct->rowid,LOG_DEBUG);
								$consigneProduct->delete($user); // on supprime l'objet consigneProduct
							}
						}
					}
					return 1;

				    case 'PRODUCT_MODIFY':
					break;
				    case 'PRODUCT_DELETE':
					// ajouter suppression consigne
					$consigneProduct = new ConsigneProduct($db);

					dol_syslog(__FILE__.": Suppression consigneProduct ". $object->id." (car suppression de l'objet lié)",LOG_DEBUG);

					$consigneProduct->fetch($object->id);
					$consigneProduct->delete($user); // puis suppression
					return 1;
/*
		    case 'PRODUCT_PRICE_MODIFY':
		    case 'PRODUCT_SET_MULTILANGS':
		    case 'PRODUCT_DEL_MULTILANGS':

		        //Stock mouvement
		    case 'STOCK_MOVEMENT':

		        //MYECMDIR
		    case 'MYECMDIR_DELETE':
		    case 'MYECMDIR_CREATE':
		    case 'MYECMDIR_MODIFY':

		        // Customer orders
		    case 'ORDER_CREATE':
		    case 'ORDER_CLONE':
			break;*/
		    case 'ORDER_VALIDATE':
			// a la validation de la commande, ajoute les consignes
			break;
		    case 'ORDER_DELETE':
		    case 'ORDER_CANCEL':
		    case 'ORDER_SENTBYMAIL':
		    case 'ORDER_CLASSIFY_BILLED':
		    case 'ORDER_SETDRAFT':
			break;
		    case 'LINEORDER_INSERT':
					$id_produit=$object->fk_product;

					//$product=new Product($db);
					$consigneProduct=new ConsigneProduct($db);
					$consigneProduct->fetch($id_produit);



					if(  $consigneProduct->fk_product_emballage_consigne != null ){ // le produit a un emballage consigné!

						$consigneProduct_consigne = new ConsigneProduct($db);
						$consigneProduct_consigne->fetch($consigneProduct->fk_product_emballage_consigne);

						if( $consigneProduct_consigne->ajout_a == ConsigneProduct::AJOUT_A_LA_COMMANDE_PROPAL ){	// cet emballage consigné doit être ajouté à la commande !
							$id_commande=$object->fk_commande;

							$commande=new Commande($db);
							$result=$commande->fetch($object->fk_commande);

							if( $result < 0){ //erreur
								dol_syslog(__FILE__."::fetch(".$commande->element.$id_commande."):: erreur :".$result, LOG_ERR);
								return -1;
							} else if( $result == 0){ // erreur objet $id_commande non trouvé. Ne devrais pas arriver
								dol_syslog(__FILE__."::fetch(".$commande->element.$id_commande."):: non trouvé", LOG_ERR);
								return -1;
							}

							$nbLigne=count($commande->lines);

							$commande->thirdparty=new Societe($db);
							$result=$commande->thirdparty->fetch($commande->socid);

							$prod=new Product($db);
							$result=$prod->fetch($consigneProduct->fk_product_emballage_consigne);

							$commande->add_product($consigneProduct->fk_product_emballage_consigne,$object->qty); // on ajoute la consigne !
							if( count($commande->lines) == $nbLigne+1){ // insertion OK

								$ligne=$commande->lines[$nbLigne];

								$ligne->rang=$object->rang+1;

								$ligne->price=$ligne->subprice;

								$ligne->total_ht = price2num($ligne->subprice * $object->qty,'MT');
								$ligne->total_tva = price2num($ligne->total_ht* $ligne->tva_tx/100,'MT');
								$ligne->total_ttc = price2num($ligne->total_ht + $ligne->total_tva,'MT');

								$ligne->fk_commande=$object->fk_commande;
								$ligne->fk_multicurrency=$object->fk_multicurrency;
								$ligne->multicurrency_code=$object->multicurrency_code;

								$tx=$object->multicurrency_total_ttc/$object->total_ttc;

								$ligne->multicurrency_total_ttc=price2num($ligne->total_ttc * $tx,'MT');
								$ligne->multicurrency_total_ht=price2num($ligne->total_ht * $tx,'MT');
								$ligne->multicurrency_total_tva=price2num($ligne->total_tva * $tx,'MT');
								$ligne->multicurrency_subprice=price2num($ligne->subprice * $tx,'MU');

								$ligne->label=$prod->label;

								$prodLie=new Product($db);
								$result=$prodLie->fetch($object->fk_product);

								$ligne->desc=$prod->description.($prod->description?"\r":"")."\t(pour ".$prodLie->label.")";

								/*$tva_tx = get_default_tva($mysoc, $object->thirdparty, $prod->id);
								$tva_npr = get_default_npr($mysoc, $object->thirdparty, $prod->id);
								if (empty($tva_tx)) $tva_npr=0;*/


								$result=$ligne->insert($user,1); // insertion de la nouvelle ligne dans la base, SANS TRIGGER !!

								// $result = $object->addline($desc, $pu_ht, $qty, $tva_tx, $localtax1_tx, $localtax2_tx, $idprod, $remise_percent, $info_bits, 0, $price_base_type, $pu_ttc, $date_start, $date_end, $type, - 1, 0, GETPOST('fk_parent_line'), $fournprice, $buyingprice, $label, $array_options, $fk_unit, '', 0, $pu_ht_devise);

								if( $result < 0 ){ //
									dol_syslog(__FILE__."::insertion OrderLine consigne:: erreur :".$result, LOG_ERR);
									return -1;
								}

								// récupération de l'objet produit
								$object->fetch_optionals($object->rowid,$extralabelsCmdeLigne);

								// et update de l'objet consigne
								$commande->lines[$nbLigne]->fetch_optionals($commande->lines[$nbLigne]->rowid,$extralabelsCmdeLigne);

								// ajout du lien
								$object->array_options['fk_ligneLiee']=$commande->lines[$nbLigne]->rowid;
								$object->insertExtraFields();

								$commande->lines[$nbLigne]->array_options['fk_ligneLiee']=$object->rowid;
								$commande->lines[$nbLigne]->insertExtraFields();

								return 1;
							}
						}


					}
					else if ($consigneProduct->est_emballage_consigne == true ){ // le produit est un emballage consigné!
						// A FAIRE, pour l'instant on ne fait rien... on verra plus tard!
						// il faudrait bloquer l'ajout de consigne "autonome"...., non ?

					} else if( $consigneProduct->est_emballage_consigne_vendu == true){
						// A FAIRE, pour l'instant on ne fait rien... on verra plus tard!
						// A voir !
					}

					return 0;
				case 'LINEORDER_MODIFY':
		    case 'LINEORDER_UPDATE':
					$error=0;
					$extralabelsCmdeLigne=$extrafields->fetch_name_optionals_label($object->table_element);

					$object->fetch_optionals($object->rowid,$extralabelsCmdeLigne);

					$cmde=new Commande($db);
					$extralabelsCmde=$extrafields->fetch_name_optionals_label($cmde->table_element);

					$sql="SELECT * FROM ". MAIN_DB_PREFIX.$object->table_element."_extrafields as t WHERE fk_ligneLiee = ".$object->rowid;
					dol_syslog(__FILE__."::".$this->name, LOG_DEBUG);
					$resql = $db->query($sql);
					$nb = $db->num_rows($resql);

					if ($nb != 0){ // il existe un (des) liens
						$cmdeLigneLiee=new OrderLine($db);
						$i=0;
						$nbAction=0;
						while( $i < $nb){
							$i++;
							$obj=$db->fetch_object($resql);

							if (empty($obj)) {		// Should not happen
								return -1;
							}


							$cmdeLigneLiee->fetch($obj->fk_object);
							$cmdeLigneLiee->fetch_optionals($obj->fk_object,$extralabelsCmdeLigne);

							// actualisation du lien
							$object->array_options['fk_ligneLiee']=$cmdeLigneLiee->rowid;
							$object->insertExtraFields();

							if( $object->qty == $cmdeLigneLiee->qty) continue; // pas de changement de quantité=>rien à faire

							$multi=$object->qty / $cmdeLigneLiee->qty;
							$cmdeLigneLiee->qty=$object->qty;


							$cmdeLigneLiee->total_ht = price2num($cmdeLigneLiee->subprice * $object->qty,'MT');
							$cmdeLigneLiee->total_tva = price2num($cmdeLigneLiee->total_ht * $cmdeLigneLiee->tva_tx / 100,'MT');
							$cmdeLigneLiee->total_ttc = price2num($cmdeLigneLiee->total_ht + $cmdeLigneLiee->total_tva,'MT');

							$cmdeLigneLiee->multicurrency_total_ttc=price2num($cmdeLigneLiee->multicurrency_total_ttc * $multi,'MT');
							$cmdeLigneLiee->multicurrency_total_ht=price2num($cmdeLigneLiee->multicurrency_total_ht * $multi,'MT');
							$cmdeLigneLiee->multicurrency_total_tva=price2num($cmdeLigneLiee->multicurrency_total_tva * $multi,'MT');
							$cmdeLigneLiee->multicurrency_subprice=price2num($cmdeLigneLiee->multicurrency_subprice * $multi,'MU');

							$result = $cmdeLigneLiee->update($user,1); // actualisation de la ligne consigne, SANS TRIGGER !!!

							If( $result >0 ) $nbAction++;
							else $error++;


							$cmde->fetch($cmdeLigneLiee->fk_commande);
							$cmde->fetch_optionals($cmdeLigneLiee->fk_commande,$extralabelsCmde);

						}
						if( $error !=0 ) return -$error;
						else return $nbAction;
					}

					return 0;
				case 'LINEORDER_DELETE':
					$error=0;
					$extralabelsCmdeLigne=$extrafields->fetch_name_optionals_label($object->table_element);

					$object->fetch_optionals($object->rowid,$extralabelsCmdeLigne);

					$cmde=new Commande($db);
					$extralabelsCmde=$extrafields->fetch_name_optionals_label($cmde->table_element);

					$sql="SELECT * FROM ". MAIN_DB_PREFIX.$object->table_element."_extrafields as t WHERE fk_ligneLiee = ".$object->rowid;
					$resql = $db->query($sql);
					$nb = $db->num_rows($resql);

					if ($nb != 0){ // il existe un (des) liens
						$cmdeLigneLiee=new OrderLine($db);
						$i=0;
						$nbAction=0;
						while( $i < $nb){
							$i++;
							$obj=$db->fetch_object($resql);

							if (empty($obj)) {		// Should not happen
								return -1;
							}

							$cmdeLigneLiee->fetch($obj->fk_object);
							$cmdeLigneLiee->fetch_optionals($obj->fk_object,$extralabelsCmdeLigne);

							$result=$cmdeLigneLiee->delete($user,1);

							if( $result>0) $nbAction++;
							else if( $result < 0 ) $error++;

						}
						if( $error !=0) return $error;
						else return $nbAction;
					}
					return 0;

				case 'LINESHIPPING_INSERT':
					break;
				case 'LINESHIPPING_DELETE':
					break;
				case 'LINESHIPPING_MODIFY':
					break;
					/*
		    case 'ORDER_SUPPLIER_CREATE':
		    case 'ORDER_SUPPLIER_CLONE':
		    case 'ORDER_SUPPLIER_VALIDATE':
		    case 'ORDER_SUPPLIER_DELETE':
		    case 'ORDER_SUPPLIER_APPROVE':
		    case 'ORDER_SUPPLIER_REFUSE':
		    case 'ORDER_SUPPLIER_CANCEL':
		    case 'ORDER_SUPPLIER_SENTBYMAIL':
		    case 'ORDER_SUPPLIER_DISPATCH':
		    case 'LINEORDER_SUPPLIER_DISPATCH':
		    case 'LINEORDER_SUPPLIER_CREATE':
		    case 'LINEORDER_SUPPLIER_UPDATE':

		        // Proposals
		    case 'PROPAL_CREATE':
		    case 'PROPAL_CLONE':
		    case 'PROPAL_MODIFY':
		    case 'PROPAL_VALIDATE':
		    case 'PROPAL_SENTBYMAIL':
		    case 'PROPAL_CLOSE_SIGNED':
		    case 'PROPAL_CLOSE_REFUSED':
		    case 'PROPAL_DELETE':
		    case 'LINEPROPAL_INSERT':
		    case 'LINEPROPAL_UPDATE':
		    case 'LINEPROPAL_DELETE':

		        // SupplierProposal
		    case 'SUPPLIER_PROPOSAL_CREATE':
		    case 'SUPPLIER_PROPOSAL_CLONE':
		    case 'SUPPLIER_PROPOSAL_MODIFY':
		    case 'SUPPLIER_PROPOSAL_VALIDATE':
		    case 'SUPPLIER_PROPOSAL_SENTBYMAIL':
		    case 'SUPPLIER_PROPOSAL_CLOSE_SIGNED':
		    case 'SUPPLIER_PROPOSAL_CLOSE_REFUSED':
		    case 'SUPPLIER_PROPOSAL_DELETE':
		    case 'LINESUPPLIER_PROPOSAL_INSERT':
		    case 'LINESUPPLIER_PROPOSAL_UPDATE':
		    case 'LINESUPPLIER_PROPOSAL_DELETE':

		        // Contracts
		    case 'CONTRACT_CREATE':
		    case 'CONTRACT_ACTIVATE':
		    case 'CONTRACT_MODIFY':
		    case 'CONTRACT_CANCEL':
		    case 'CONTRACT_CLOSE':
		    case 'CONTRACT_DELETE':
		    case 'LINECONTRACT_INSERT':
		    case 'LINECONTRACT_UPDATE':
		    case 'LINECONTRACT_DELETE':

		        // Bills
		    case 'BILL_CREATE':
		    case 'BILL_CLONE':
		    case 'BILL_MODIFY':
		    case 'BILL_VALIDATE':
		    case 'BILL_UNVALIDATE':
		    case 'BILL_SENTBYMAIL':
		    case 'BILL_CANCEL':
		    case 'BILL_DELETE':
		    case 'BILL_PAYED':
				*/

		    case 'LINEBILL_INSERT':
				
				$id_produit=$object->fk_product;

				//$product=new Product($db);
				$consigneProduct=new ConsigneProduct($db);
				$consigneProduct->fetch($id_produit);



				if( $consigneProduct->fk_product_emballage_consigne != null ){ // le produit a un emballage consigné!

					$consigneProduct_consigne = new ConsigneProduct($db);
					$consigneProduct_consigne->fetch($consigneProduct->fk_product_emballage_consigne);

					if( $consigneProduct_consigne->ajout_a == ConsigneProduct::AJOUT_A_LA_FACTURE && 1==0){	// cet emballage consigné doit être ajouté à la facture !
						$id_facture=$object->fk_facture;

						$facture=new Facture($db);
						$result=$facture->fetch($object->fk_facture);

						if( $result < 0){ //erreur
							dol_syslog(__FILE__."::fetch(".$facture->element.$id_commande."):: erreur :".$result, LOG_ERR);
							return -1;
						} else if( $result == 0){ // erreur objet $id_commande non trouvé. Ne devrais pas arriver
							dol_syslog(__FILE__."::fetch(".$facture->element.$id_commande."):: non trouvé", LOG_ERR);
							return -1;
						}

						$nbLigne=count($facture->lines);

						$facture->thirdparty=new Societe($db);
						$result=$facture->thirdparty->fetch($facture->socid);

						$idprod=$consigneProduct->fk_product_emballage_consigne;
						$prod=new Product($db);
						$result=$prod->fetch($idprod);

						// $facture->add_product($consigneProduct->fk_product_emballage_consigne,$object->qty); // on ajoute la consigne !

						$datapriceofproduct = $prod->getSellPrice($mysoc, $object->thirdparty, '');

						$pu_ht = $datapriceofproduct['pu_ht'];
						$pu_ttc = $datapriceofproduct['pu_ttc'];
						$price_min = $datapriceofproduct['price_min'];
						$price_base_type = $datapriceofproduct['price_base_type'];
						$tva_tx = $datapriceofproduct['tva_tx'];
						$tva_npr = $datapriceofproduct['tva_npr'];

						$type = $prod->type;
						$fk_unit = $prod->fk_unit;

						$desc=$prod->description;
						$qty=$object->qty;
						
						// Local Taxes
						$localtax1_tx = get_localtax($tva_tx, 1, $object->thirdparty, $mysoc, $tva_npr);
						$localtax2_tx = get_localtax($tva_tx, 2, $object->thirdparty, $mysoc, $tva_npr);

						$info_bits = 0;
						if ($tva_npr) {
							$info_bits |= 0x01;
						}

						// Define special_code for special lines
						$special_code = 0;

						// A modifier ?
						$fk_parent_line = 0;
						$fk_fournprice = null;
						$pa_ht = 0;
						$label='';
						$array_options = 0;
						$situation_percent = 100;
						
						$price_ht_devise = '';
						$pu_ht_devise = price2num($price_ht_devise, 'MU');

						$result = $facture->addLine(
							$desc,
							$pu_ht,	// @param    	double		$pu_ht              Unit price without tax (> 0 even for credit note)
							$qty,	// @param    	double		$qty             	Quantity
							$tva_tx,	// $txtva           	Force Vat rate, -1 for auto (Can contain the vat_src_code too with syntax '9.9 (CODE)')
							$localtax1_tx,	// @param		double		$txlocaltax1		Local tax 1 rate (deprecated, use instead txtva with code inside)
							$localtax2_tx,	// @param		double		$txlocaltax2		Local tax 2 rate (deprecated, use instead txtva with code inside)
							$idprod, //  @param    	int			$fk_product      	Id of predefined product/service
							0, // $remise_percent  	Percent of discount on line
							'', // $date_start      	Date start of service
							'', // $date_end      	Date end of service
							0, // $ventil          	Code of dispatching into accountancy
							$info_bits, // 	$info_bits			Bits of type of lines
							'', // $fk_remise_except	Id discount used
							$price_base_type,	// $price_base_type	'HT' or 'TTC'
							$pu_ttc,	// $pu_ttc             Unit price with tax (> 0 even for credit note)
							$type,	// $type				Type of line (0=product, 1=service). Not used if fk_product is defined, the type of product is used.
							$object->rang+1,	// $rang               Position of line (-1 means last value + 1)
							$special_code, // $special_code		Special code (also used by externals modules!)
							'',	// $origin				Depend on global conf MAIN_CREATEFROM_KEEP_LINE_ORIGIN_INFORMATION can be 'orderdet', 'propaldet'..., else 'order','propal,'....
							0,	// $origin_id			Depend on global conf MAIN_CREATEFROM_KEEP_LINE_ORIGIN_INFORMATION can be Id of origin object (aka line id), else object id
							$fk_parent_line,	// int	$fk_parent_line	Id of parent line 
							$fk_fournprice,	// int	$fk_fournprice	Supplier price id (to calculate margin) or '' 
							$pa_ht,		// int	$pa_ht	Buying price of line (to calculate margin) or '' 
							$label, // $label				Label of the line (deprecated, do not use)
							$array_options,	// $array_options		extrafields array
							$situation_percent,	//  $situation_percent  Situation advance percentage
							'',	// int         $fk_prev_id         Previous situation line id reference
							$fk_unit, // 	string		$fk_unit 			Code of the unit to use. Null to use the default one
							$pu_ht_devise, // double		$pu_ht_devise		Unit price in foreign currency
							'', // string	$ref_ext	External reference of the line
		  					1 // int	$noupdateafterinsertline	No update after insert of line
						);

						if( $result < 0 ){ //
							dol_syslog(__FILE__." facture->addLine():: erreur :".$result, LOG_ERR);
							return -1;
						}
						if( $result > 0 ){ // insertion OK

							if($consigneProduct->prix_produit_inclu_consigne == 1 ){	// le prix du produit doit inclure le prix de la consigne
								// A FAIRE
								// 1ere version, ne fonctionne QUE si les taux de localtax1 et de localtax2 sont identiques
								// A MODIFIER
								if( $object->localtax1_tx == $localtax1_tx && $object->localtax2_tx == $localtax2_tx ){

									$new_pu_ht = abs($object->subprice) - abs($pu_ht);
									$remise_percent=$object->remise_percent * abs($object->subprice) / ( $new_pu_ht );

									$localtaxes_type = getLocalTaxesFromRate($object->tva_tx, 0, $facture->thirdparty, $mysoc);
									
									
									$tabprice = calcul_price_total($object->qty, 	// int	$qty	Quantity 
											$new_pu_ht, 	// float	$pu	Unit price (HT or TTC selon price_base_type) 
											$remise_percent, 		// float	$remise_percent_ligne	Discount for line 
											$object->tva_tx,	// 	0=do not apply VAT tax, VAT rate=apply (this is VAT rate only without text code, we don't need text code because we alreaydy have all tax info into $localtaxes_array) 
											-1, // float	$uselocaltax1_rate	0=do not use this localtax, >0=apply and get value from localtaxes_array (or database if empty), 
																		// -1=autodetect according to seller if we must apply, get value from localtaxes_array (or database if empty). Try to always use -1. 
											-1,	// float	$uselocaltax2_rate (idem)
											0, // float	$remise_percent_global	0 
											'HT',	// string	$price_base_type	HT=Unit price parameter is HT, TTC=Unit price parameter is TTC 
											$object->info_bits, // 	Miscellaneous informations on line 
											$object->type, // int	$type	0/1=Product/service 
											$mysoc, // 	Thirdparty seller (we need $seller->country_id property). Provided only if seller is the supplier, otherwise $seller will be $mysoc
											$localtaxes_type,	// Array with localtaxes info array('0'=>type1,'1'=>rate1,'2'=>type2,'3'=>rate2) (loaded by getLocalTaxesFromRate(vatrate, 0, ...) function). 
											100,	// integer	$progress	Situation invoices progress (value from 0 to 100, 100 by default) 
											$facture->multicurrency_tx,	// double	$multicurrency_tx	Currency rate (1 by default) 
											0);		// double	$pu_devise	Amount in currency 

									$total_ht  = $tabprice[0];
									$total_tva = $tabprice[1];
									$total_ttc = $tabprice[2];
									$total_localtax1 = $tabprice[9];
									$total_localtax2 = $tabprice[10];
									$pu_ht  = $tabprice[3];
									$pu_tva = $tabprice[4];
									$pu_ttc = $tabprice[5];
						
									// MultiCurrency
									$multicurrency_total_ht = $tabprice[16];
									$multicurrency_total_tva = $tabprice[17];
									$multicurrency_total_ttc = $tabprice[18];
									$pu_ht_devise = $tabprice[19];

									// ???

									$object->qty = ($facture->type == Facture::TYPE_CREDIT_NOTE ?abs($qty) : $qty); // For credit note, quantity is always positive and unit price negative
									/*
									$object->vat_src_code = $vat_src_code;
									$object->tva_tx = $txtva;
									$object->localtax1_tx		= $txlocaltax1;
									$object->localtax2_tx		= $txlocaltax2;
									$object->localtax1_type		= empty($localtaxes_type[0]) ? '' : $localtaxes_type[0];
									$object->localtax2_type		= empty($localtaxes_type[2]) ? '' : $localtaxes_type[2];
									*/
									$object->remise_percent		= $remise_percent;
									$object->subprice			= ($facture->type == Facture::TYPE_CREDIT_NOTE ?-abs($pu_ht) : $pu_ht); // For credit note, unit price always negative, always positive otherwise
									$object->date_start = $date_start;
									$object->date_end			= $date_end;
									$object->total_ht			= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($total_ht) : $total_ht); // For credit note and if qty is negative, total is negative
									$object->total_tva			= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($total_tva) : $total_tva);
									$object->total_localtax1	= $total_localtax1;
									$object->total_localtax2	= $total_localtax2;
									$object->total_ttc			= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($total_ttc) : $total_ttc);
									$object->info_bits			= $info_bits;
									$object->special_code		= $special_code;
									$object->product_type		= $type;
									$object->fk_parent_line = $fk_parent_line;
									$object->skip_update_total = $skip_update_total;
									$object->situation_percent = $situation_percent;
									$object->fk_unit = $fk_unit;

									$object->fk_fournprice = $fk_fournprice;
									$object->pa_ht = $pa_ht;

									// Multicurrency
									$object->multicurrency_subprice		= ($facture->type == Facture::TYPE_CREDIT_NOTE ?-abs($pu_ht_devise) : $pu_ht_devise); // For credit note, unit price always negative, always positive otherwise
									$object->multicurrency_total_ht 	= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($multicurrency_total_ht) : $multicurrency_total_ht); // For credit note and if qty is negative, total is negative
									$object->multicurrency_total_tva 	= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($multicurrency_total_tva) : $multicurrency_total_tva);
									$object->multicurrency_total_ttc 	= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($multicurrency_total_ttc) : $multicurrency_total_ttc);
									
									$res=$object->update($user, 1);
									if( $res < 0 ){
										$e++;
									}
								}


							}

							if( $consigneProduct_consigne->indissociable ){
								$res=ll_ajouteLiaison($object, $result);
								if( $res < 0 ) return -1;
							}

							return 1;
						}
					}


				}
				else if ($consigneProduct->est_emballage_consigne == true ){ // le produit est un emballage consigné!
					// A FAIRE, pour l'instant on ne fait rien... on verra plus tard!
					// il faudrait bloquer l'ajout de consigne "autonome"...., non ?

				} else if( $consigneProduct->est_emballage_consigne_vendu == true){
					// A FAIRE, pour l'instant on ne fait rien... on verra plus tard!
					// A voir !
				}

				return 0;
		    case 'LINEBILL_UPDATE':
				$e=count($object->errors);
				$liste=ll_getLiaisons($object);

				if( count($liste) > 0 ){
					
					$facture=new Facture($db);
					$result=$facture->fetch($object->fk_facture);

					$e=0;
					$i=0;
					while($i < count($liste)){

						$ligne=new FactureLigne($db);
						$ligne->fetch($liste[$i]);

						if( $ligne->fk_facture != $object->fk_facture) { // ligne n'appartenant pas à la même facture ???
							// erreur à gérer ....!!! // A FAIRE
							continue;
						}

						if( $object->qty != $ligne->qty ){
							$localtaxes_type = getLocalTaxesFromRate($ligne->tva_tx, 0, $facture->thirdparty, $mysoc);
									
									
							$tabprice = calcul_price_total($object->qty, 	// int	$qty	Quantity 
									$ligne->pu_ht, 	// float	$pu	Unit price (HT or TTC selon price_base_type) 
									$ligne->remise_percent, 		// float	$remise_percent_ligne	Discount for line 
									$ligne->tva_tx,	// 	0=do not apply VAT tax, VAT rate=apply (this is VAT rate only without text code, we don't need text code because we alreaydy have all tax info into $localtaxes_array) 
									-1, // float	$uselocaltax1_rate	0=do not use this localtax, >0=apply and get value from localtaxes_array (or database if empty), 
																// -1=autodetect according to seller if we must apply, get value from localtaxes_array (or database if empty). Try to always use -1. 
									-1,	// float	$uselocaltax2_rate (idem)
									0, // float	$remise_percent_global	0 
									'HT',	// string	$price_base_type	HT=Unit price parameter is HT, TTC=Unit price parameter is TTC 
									$ligne->info_bits, // 	Miscellaneous informations on line 
									$ligne->type, // int	$type	0/1=Product/service 
									$mysoc, // 	Thirdparty seller (we need $seller->country_id property). Provided only if seller is the supplier, otherwise $seller will be $mysoc
									$localtaxes_type,	// Array with localtaxes info array('0'=>type1,'1'=>rate1,'2'=>type2,'3'=>rate2) (loaded by getLocalTaxesFromRate(vatrate, 0, ...) function). 
									100,	// integer	$progress	Situation invoices progress (value from 0 to 100, 100 by default) 
									$facture->multicurrency_tx,	// double	$multicurrency_tx	Currency rate (1 by default) 
									0);		// double	$pu_devise	Amount in currency 

							$total_ht  = $tabprice[0];
							$total_tva = $tabprice[1];
							$total_ttc = $tabprice[2];
							$total_localtax1 = $tabprice[9];
							$total_localtax2 = $tabprice[10];
							$pu_ht  = $tabprice[3];
							$pu_tva = $tabprice[4];
							$pu_ttc = $tabprice[5];
				
							// MultiCurrency
							$multicurrency_total_ht = $tabprice[16];
							$multicurrency_total_tva = $tabprice[17];
							$multicurrency_total_ttc = $tabprice[18];
							$pu_ht_devise = $tabprice[19];

							// ???

							$ligne->qty = ($facture->type == Facture::TYPE_CREDIT_NOTE ?abs($qty) : $qty); // For credit note, quantity is always positive and unit price negative
							/*
							$ligne->vat_src_code = $vat_src_code;
							$ligne->tva_tx = $txtva;
							$ligne->localtax1_tx		= $txlocaltax1;
							$ligne->localtax2_tx		= $txlocaltax2;
							$ligne->localtax1_type		= empty($localtaxes_type[0]) ? '' : $localtaxes_type[0];
							$ligne->localtax2_type		= empty($localtaxes_type[2]) ? '' : $localtaxes_type[2];
							*/
							$ligne->remise_percent		= $remise_percent;
							$ligne->subprice			= ($facture->type == Facture::TYPE_CREDIT_NOTE ?-abs($pu_ht) : $pu_ht); // For credit note, unit price always negative, always positive otherwise
							$ligne->date_start = $date_start;
							$ligne->date_end			= $date_end;
							$ligne->total_ht			= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($total_ht) : $total_ht); // For credit note and if qty is negative, total is negative
							$ligne->total_tva			= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($total_tva) : $total_tva);
							$ligne->total_localtax1	= $total_localtax1;
							$ligne->total_localtax2	= $total_localtax2;
							$ligne->total_ttc			= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($total_ttc) : $total_ttc);
							$ligne->info_bits			= $info_bits;
							$ligne->special_code		= $special_code;
							$ligne->product_type		= $type;
							$ligne->fk_parent_line = $fk_parent_line;
							$ligne->skip_update_total = $skip_update_total;
							$ligne->situation_percent = $situation_percent;
							$ligne->fk_unit = $fk_unit;

							$ligne->fk_fournprice = $fk_fournprice;
							$ligne->pa_ht = $pa_ht;

							// Multicurrency
							$ligne->multicurrency_subprice		= ($facture->type == Facture::TYPE_CREDIT_NOTE ?-abs($pu_ht_devise) : $pu_ht_devise); // For credit note, unit price always negative, always positive otherwise
							$ligne->multicurrency_total_ht 	= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($multicurrency_total_ht) : $multicurrency_total_ht); // For credit note and if qty is negative, total is negative
							$ligne->multicurrency_total_tva 	= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($multicurrency_total_tva) : $multicurrency_total_tva);
							$ligne->multicurrency_total_ttc 	= (($facture->type == Facture::TYPE_CREDIT_NOTE || $qty < 0) ?-abs($multicurrency_total_ttc) : $multicurrency_total_ttc);

							$res=$ligne->update($user, 1);
							if( $res < 0 ){
								$e++;
							}
						}
						
					}
					if( $e > 0){
						if( $e != count($liste)){
							return -3; // KO update
						} else {
							return -2;	// OK partiel
						} 
					}

					return count($liste); // OK
				} else {
					// <0 si ko, 0 si aucune action faite, >0 si ok 
					if( $e != count($object->errors) ){
						return -1; // KO
					} else return 0; // OK, aucune action faite
				}
				break;
		    case 'LINEBILL_DELETE':
				return ll_supprimerLiaison($user, $object, true);
				/*

		        //Supplier Bill
		    case 'BILL_SUPPLIER_CREATE':
		    case 'BILL_SUPPLIER_UPDATE':
		    case 'BILL_SUPPLIER_DELETE':
		    case 'BILL_SUPPLIER_PAYED':
		    case 'BILL_SUPPLIER_UNPAYED':
		    case 'BILL_SUPPLIER_VALIDATE':
		    case 'BILL_SUPPLIER_UNVALIDATE':
		    case 'LINEBILL_SUPPLIER_CREATE':
		    case 'LINEBILL_SUPPLIER_UPDATE':
		    case 'LINEBILL_SUPPLIER_DELETE':

		        // Payments
		    case 'PAYMENT_CUSTOMER_CREATE':
		    case 'PAYMENT_SUPPLIER_CREATE':
		    case 'PAYMENT_ADD_TO_BANK':
		    case 'PAYMENT_DELETE':

		        // Online
		    case 'PAYMENT_PAYBOX_OK':
		    case 'PAYMENT_PAYPAL_OK':
		    case 'PAYMENT_STRIPE_OK':

		        // Donation
		    case 'DON_CREATE':
		    case 'DON_UPDATE':
		    case 'DON_DELETE':

		        // Interventions
		    case 'FICHINTER_CREATE':
		    case 'FICHINTER_MODIFY':
		    case 'FICHINTER_VALIDATE':
		    case 'FICHINTER_DELETE':
		    case 'LINEFICHINTER_CREATE':
		    case 'LINEFICHINTER_UPDATE':
		    case 'LINEFICHINTER_DELETE':

		        // Members
		    case 'MEMBER_CREATE':
		    case 'MEMBER_VALIDATE':
		    case 'MEMBER_SUBSCRIPTION':
		    case 'MEMBER_MODIFY':
		    case 'MEMBER_NEW_PASSWORD':
		    case 'MEMBER_RESILIATE':
		    case 'MEMBER_DELETE':

		        // Categories
		    case 'CATEGORY_CREATE':
		    case 'CATEGORY_MODIFY':
		    case 'CATEGORY_DELETE':
		    case 'CATEGORY_SET_MULTILANGS':

		        // Projects
		    case 'PROJECT_CREATE':
		    case 'PROJECT_MODIFY':
		    case 'PROJECT_DELETE':

		        // Project tasks
		    case 'TASK_CREATE':
		    case 'TASK_MODIFY':
		    case 'TASK_DELETE':

		        // Task time spent
		    case 'TASK_TIMESPENT_CREATE':
		    case 'TASK_TIMESPENT_MODIFY':
		    case 'TASK_TIMESPENT_DELETE':

		        // Shipping
		    case 'SHIPPING_CREATE':
		    case 'SHIPPING_MODIFY':
		    case 'SHIPPING_VALIDATE':
		    case 'SHIPPING_SENTBYMAIL':
		    case 'SHIPPING_BILLED':
		    case 'SHIPPING_CLOSED':
		    case 'SHIPPING_REOPEN':
		    case 'SHIPPING_DELETE':
		        break;
*/
		    }

		return 0;
	}
}
