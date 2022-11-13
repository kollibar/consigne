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
 *
 * Library javascript to enable Browser notifications
 */

if (!defined('NOREQUIREUSER'))  define('NOREQUIREUSER', '1');
if (!defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
if (!defined('NOREQUIRESOC'))   define('NOREQUIRESOC', '1');
if (!defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
if (!defined('NOCSRFCHECK'))    define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1);
if (!defined('NOLOGIN'))        define('NOLOGIN', 1);
if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', 1);
if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', 1);
if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');


/**
 * \file    consigne/js/consigne.js.php
 * \ingroup consigne
 * \brief   JavaScript file for module Consigne.
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/../main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/../main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

// Define js type
header('Content-Type: application/javascript');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');
?>

/* Javascript library of module Consigne */

function cs_getIndex(name){
  if( name.indexOf('_') < 0 ){
    return name.slice(4);
  } else {
    return name.slice(4,name.indexOf('_'));
  }
}
function cs_getSubIndex(name){
  if( name.indexOf('_') < 0 ){
    return 0;
  } else {
    return name.slice(name.indexOf('_')+1);
  }
}
function cs_getIndex_commandedet(fk_commandedet){
  return $('a[name='+fk_commandedet+']').closest('tr').find('input')[0].attributes[0].value.slice(8);
}
function cs_getListeInputName_Index(index){
  let listeName=[];

  var l=$('.qtyl');
  for(let i=0;i < l.length;i++){
    name=l[i].attributes[1].value;
    idx=cs_getIndex(name);
    if( idx == index ){
      listeName.push(name);
    }
  }
  return listeName;
}
function cs_getQty_Index(index){
  var qty=0;
  listeName=cs_getListeInputName_Index(index);
  for(let i=0;i < listeName.length;i++){
    qty += parseInt($('input[name='+listeName[i]+']').val());
  }
  return qty;
}
function cs_getMaxQty(index, subindex){
  if( subindex == 0 && $('#qtyl'+index+'_'+subindex).length == 0 ){
    // A FAIRE
    if( $('#qtyl'+index).closest('tr').find('td.left').find('select') != 0){
      data=$('#qtyl'+index).closest('tr').find('td.left').find('select').find('option[value='+$('#qtyl'+index).closest('tr').find('td.left').find('select').val()+']').html()
      if( data.lastIndexOf(')') != -1 ){
        data=data.slice(0, data.lastIndexOf(')'));
        return parseInt(data.slice(data.lastIndexOf(':')+1));
      }
    } else {
      // A FAIRE, cas où pas de select ??
    }
  } else {
    // a corriger si entrepot multiple
    if( $('#qtyl'+index+'_'+subindex).closest('tr').find('td.left').find('select') != 0){
      // A FAIRE au cas où un select ??
    } else {
      var data=$('#qtyl'+index+'_'+subindex).closest('tr').find('td.left').html();
      if( data.indexOf('<br>') != -1){
        data=data.slice(0,data.lastIndexOf('<br>'));
        return parseInt(data.slice(data.lastIndexOf(' ')+1));
      }
    }
  }
  return -1;
}
function cs_setQty(index, subindex, value){
    return $('#qtyl'+index).val(value);
    if( subindex == 0 && $('#qtyl'+index+'_'+subindex).length == 0 ){
  } else {
    return $('#qtyl'+index+'_'+subindex).val(value);
  }
}
function cs_getQty(index, subindex){
  if( subindex == 0 && $('#qtyl'+index+'_'+subindex).length == 0 ){
    return parseInt($('#qtyl'+index).val());
  } else {
    return parseInt($('#qtyl'+index+'_'+subindex).val());
  }
}
function getName(index, subindex){
  if( subindex == 0 && $('#qtyl'+index).length == 1){
    return '#qtyl'+index;
  } else if($('#qtyl'+index+'_'+subindex).length == 1){
    return '#qtyl'+index+'_'+subindex;
  }
}
function getNbInput(index){
  var l=cs_getListeInputName_Index(index);
  return l.length;
}
function verifLiens(i,i_lie){
  var qty=cs_getQty_Index(i);
  var qty_lie=cs_getQty_Index(i_lie);

  console.log('qty:' + qty + ' qty_lie:' + qty_lie);


  var nb_lie=getNbInput(i_lie);

  var j=0;
  while( qty != qty_lie && j < nb_lie){

    if( qty < qty_lie ){
      var dif=qty_lie-qty;
      if( cs_getQty(i_lie, j) > dif ) {
        cs_setQty(i_lie,j, cs_getQty(i_lie, j) - dif);
        break;
      } else {
        qty_lie -=  cs_getQty(i_lie, j);
        cs_setQty(i_lie,j,0);
      }
    } else {
      var dif=qty-qty_lie;

      if( cs_getMaxQty(i_lie, j) == -1 || cs_getMaxQty(i_lie, j) - cs_getQty(i_lie, j) > dif){
        cs_setQty(i_lie,j, cs_getQty(i_lie, j) + dif);
        break;
      } else if( cs_getQty(i_lie, j) < cs_getMaxQty(i_lie, j) ){
        qty_lie +=  cs_getMaxQty(i_lie, j) - cs_getQty(i_lie, j);
        cs_setQty(i_lie, j, cs_getMaxQty(i_lie, j));
      }
    }
    j++;
  }
}
