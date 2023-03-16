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
	onChange("est_emballage_consigne");
	onChange("est_emballage_consigne_vendu");
/*
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
	}*/
}
document.body.onload="onLoad();";
</script>
<?php

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
