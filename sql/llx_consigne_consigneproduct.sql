-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see http://www.gnu.org/licenses/.


CREATE TABLE llx_consigne_consigneproduct(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
	entity INTEGER DEFAULT 1 NOT NULL,
	description text,
	date_creation datetime NOT NULL,
	tms timestamp NOT NULL,
	fk_user_creat integer NOT NULL,
	fk_user_modif integer,
	status integer NOT NULL,
	fk_product integer NOT NULL,
	est_emballage_consigne integer NOT NULL DEFAULT 0,
	est_emballage_consigne_vendu integer NOT NULL DEFAULT 0,
	est_emballage_consigne_retour integer NOT NULL DEFAULT 0,

	fk_product_emballage_consigne integer,

	fk_product_emballage_vendu integer DEFAULT NULL,
	fk_product_emballage_retour integer DEFAULT NULL,
	suivi_emballage integer DEFAULT 0,
	ajout_a integer DEFAULT 0,
	indissociable integer DEFAULT 0,
	prix_produit_inclu_consigne integer DEFAULT 0,

	est_cache_bordereau_livraison integer DEFAULT 0,
	
	colisage integer,
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

/*
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
	*/