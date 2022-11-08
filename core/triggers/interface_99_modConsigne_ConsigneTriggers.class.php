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
	include_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
	//include_once DOL_DOCUMENT_ROOT.'/commande/class/api_orders.class.php';

	dol_include_once('/consigne/class/consigneproduct.class.php');
	dol_include_once('/consigne/class/facturationconsigne.class.php');
	dol_include_once('/consigne/class/mouvementconsigne.class.php');
	dol_include_once('/consigne/class/retourconsigne.class.php');

	$db=$object->db;

	// chargement des champs extra
	$extrafields = new ExtraFields($db);


	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

        switch ($action) {

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

			break;
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
			break;
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
		    case 'LINEBILL_INSERT':
		    case 'LINEBILL_UPDATE':
		    case 'LINEBILL_DELETE':

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

		    }

		return 0;
	}
}
