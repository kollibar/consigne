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
 * \file    core/tpl/mytemplate.tpl.php
 * \ingroup consigne
 * \brief   Example template.
 *
 * Put detailed description here.
 */

// Protection to avoid direct call of template
if (empty($conf) || ! is_object($conf))
{
	print "Error, template page can't be called as URL";
	exit;
}
?>
<!-- BEGIN PHP TEMPLATE tabProduct_edit.tpl.php -->

<script language="javascript">
function onChange(id){
/*
est_emballage_consigne
suivi_emballage
fk_product_emballage_vendu
fk_product_emballage_consigne
fk_product_emballage_retour
est_emballage_consigne_vendu
document.getElementById("est_emballage_consigne").checked = true*/
	if( id == "est_emballage_consigne" ){ // on change est_emballage_consigne
		if( document.getElementById("est_emballage_consigne").checked == true ){// on le coche
			document.getElementById("est_emballage_consigne_vendu").checked = false;
			document.getElementById("fk_product_emballage_consigne_tr").style.display="none";
			document.getElementById("fk_product_emballage_retour_tr").style.display="";
			document.getElementById("fk_product_emballage_vendu_tr").style.display="";
		} else { // on le décoche
			document.getElementById("fk_product_emballage_consigne_tr").style.display="";
			document.getElementById("fk_product_emballage_retour_tr").style.display="none";
			document.getElementById("fk_product_emballage_vendu_tr").style.display="none";
		}
	}
	if( id == "est_emballage_consigne_vendu"){
		if( document.getElementById("est_emballage_consigne_vendu").checked == true ){ // on viens de cocher est_emballage_consigne
			document.getElementById("est_emballage_consigne").checked = false;
			document.getElementById("fk_product_emballage_consigne_tr").style.display="none";
			document.getElementById("fk_product_emballage_retour_tr").style.display="none";
			document.getElementById("fk_product_emballage_vendu_tr").style.display="none";
		} else {
			document.getElementById("fk_product_emballage_consigne_tr").style.display="";
			document.getElementById("fk_product_emballage_retour_tr").style.display="";
			document.getElementById("fk_product_emballage_vendu_tr").style.display="";
		}
	}
}
function onLoad(){
	if( document.getElementById("est_emballage_consigne").checked == true ){// on le coche
		document.getElementById("est_emballage_consigne_vendu").checked = false;
		document.getElementById("fk_product_emballage_consigne_tr").style.display="none";
		document.getElementById("fk_product_emballage_retour_tr").style.display="";
		document.getElementById("fk_product_emballage_vendu_tr").style.display="";
	} else if( document.getElementById("est_emballage_consigne_vendu").checked == true ){ // on viens de cocher est_emballage_consigne
		document.getElementById("fk_product_emballage_consigne_tr").style.display="none";
		document.getElementById("fk_product_emballage_retour_tr").style.display="none";
		document.getElementById("fk_product_emballage_vendu_tr").style.display="none";
	} else {
		document.getElementById("fk_product_emballage_consigne_tr").style.display="";
		document.getElementById("fk_product_emballage_retour_tr").style.display="none";
		document.getElementById("fk_product_emballage_vendu_tr").style.display="none";
	}
}
document.body.onload="onLoad();";
</script>
<?php
/*
	'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'visible'=>-1, 'enabled'=>1, 'position'=>1, 'notnull'=>1, 'index'=>1, 'comment'=>"Id",),
	'entity' => array('type'=>'integer', 'label'=>'Entity', 'visible'=>-1, 'enabled'=>1, 'position'=>20, 'notnull'=>1, 'index'=>1,),
	'description' => array('type'=>'text', 'label'=>'Descrption', 'visible'=>-1, 'enabled'=>1, 'position'=>60, 'notnull'=>-1,),
	'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'visible'=>-2, 'enabled'=>1, 'position'=>500, 'notnull'=>1,),
	'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'visible'=>-2, 'enabled'=>1, 'position'=>501, 'notnull'=>1,),
	'fk_user_creat' => array('type'=>'integer', 'label'=>'UserAuthor', 'visible'=>-2, 'enabled'=>1, 'position'=>510, 'notnull'=>1,),
	'fk_user_modif' => array('type'=>'integer', 'label'=>'UserModif', 'visible'=>-2, 'enabled'=>1, 'position'=>511, 'notnull'=>-1,),
	'status' => array('type'=>'integer', 'label'=>'Status', 'visible'=>1, 'enabled'=>1, 'position'=>1000, 'notnull'=>1, 'index'=>1, 'arrayofkeyval'=>array('0'=>'Draft', '1'=>'Active', '-1'=>'Cancel')),
	'fk_product' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'Product', 'visible'=>-1, 'enabled'=>1, 'position'=>2, 'notnull'=>1, 'index'=>1, 'comment'=>"id produit associé",),
	'est_emballage_consigne' => array('type'=>'integer', 'label'=>'EstEmballageConsigne', 'visible'=>1, 'enabled'=>1, 'position'=>10, 'notnull'=>1, 'comment'=>"0: non, 1: oui",),
	'suivi_emballage' => array('type'=>'integer', 'label'=>'SuiviEmballahe', 'visible'=>1, 'enabled'=>1, 'position'=>15, 'notnull'=>1, 'comment'=>"0: non, 1: oui         indique si l'emballage doit être suivi par client",),
	'fk_product_emballage_vendu' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'EmballageVendu', 'visible'=>1, 'enabled'=>1, 'position'=>20, 'notnull'=>-1, 'comment'=>"si le produit vendu doit être différent du produit consigné (pour compta)",),
	'fk_product_emballage_retour' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'EmballageRetour', 'visible'=>1, 'enabled'=>1, 'position'=>25, 'notnull'=>-1, 'comment'=>"si le produit en retour doit être différent du produit consigné (pour compta)",),
	'fk_product_emballage_consigne' => array('type'=>'integer:Product:product/class/product.class.php', 'label'=>'ProduitEmballageConsigne', 'visible'=>1, 'enabled'=>1, 'position'=>40, 'notnull'=>-1, 'comment'=>"si est le produit vendu d'un emballage consigné non retourné sinon null",),
	'est_emballage_consigne_vendu' => array('type'=>'integer', 'label'=>'EstEmballageConsigneVendu', 'visible'=>1, 'enabled'=>1, 'position'=>45, 'notnull'=>1, 'comment'=>"0: non, 1: oui indique si ce produit correspond à un emballage consigne vendu",),
	*/

	
	// description

$object->fields = dol_sort_array($object->fields, 'position');

foreach($object->fields as $key => $val)
{
	// Discard if extrafield is a hidden field on form
	if (abs($val['visible']) != 1) continue;

	if (array_key_exists('enabled', $val) && isset($val['enabled']) && ! $val['enabled']) continue;	// We don't want this field
	
	if( $key == "fk_product") continue; // on n'affiche pas le champ fk_product, celui-ci est géré automatiquement
	
	if( $key == "status" ) continue; // champ pas encore utilisé !
	
	$moreparam='onchange="onChange(this.id);"';

	print '<tr id='.$key.'_tr><td';
	print ' class="titlefieldcreate';
	if ($val['notnull'] > 0) print ' fieldrequired';
	if ($val['type'] == 'text' || $val['type'] == 'html') print ' tdtop';
	print '"';
	print '>'.$langs->trans($val['label']).'</td>';
	print '<td>';
	if (in_array($val['type'], array('int', 'integer'))) $value = GETPOSTISSET($key)?GETPOST($key, 'int'):$object->$key;
	elseif ($val['type'] == 'text' || $val['type'] == 'html') $value = GETPOSTISSET($key)?GETPOST($key,'none'):$object->$key;
	else $value = GETPOSTISSET($key)?GETPOST($key, 'alpha'):$object->$key;
	//var_dump($val.' '.$key.' '.$value);
	print $object->showInputField($val, $key, $value, $moreparam, '', '', 0);
	print '</td>';
	print '</tr>';
}
?>
<!-- END PHP TEMPLATE tabProduct_edit.tpl.php -->