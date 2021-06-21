<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       consigneproduct_card.php
 *		\ingroup    consigne
 *		\brief      Page to create/edit/view consigneproduct
 */

//if (! defined('NOREQUIREUSER'))          define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))            define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))           define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))          define('NOREQUIRETRAN','1');
//if (! defined('NOSCANGETFORINJECTION'))  define('NOSCANGETFORINJECTION','1');			// Do not check anti CSRF attack test
//if (! defined('NOSCANPOSTFORINJECTION')) define('NOSCANPOSTFORINJECTION','1');		// Do not check anti CSRF attack test
//if (! defined('NOCSRFCHECK'))            define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test done when option MAIN_SECURITY_CSRF_WITH_TOKEN is on.
//if (! defined('NOSTYLECHECK'))           define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL'))         define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))          define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))          define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))          define('NOREQUIREAJAX','1');         // Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php');
dol_include_once('/consigne/class/consigneproduct.class.php');
dol_include_once('/consigne/lib/consigneproduct.lib.php');
include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/lib/product.lib.php';

// Load traductions files requiredby by page
$langs->loadLangs(array("consigne@consigne","other"));
$langs->load("products");

// Get parameters
$idproduct			= GETPOST('id', 'int');
$ref        = GETPOST('ref', 'alpha');
$action		= GETPOST('action', 'alpha');
$cancel     = GETPOST('cancel', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

// vérifie l'existance d'un objet consigneProduct et le crée le cas échéant !
$product=new Product($db);
$consigneProduct=new ConsigneProduct($db);
$sql = "SELECT * FROM ".MAIN_DB_PREFIX.$consigneProduct->table_element." as t";
$sql.=" WHERE t.fk_product = $idproduct";
$resql = $db->query($sql);
$nb = $db->num_rows($resql);

if ($nb === 0){ // aucun enregistrement => il faut en crée 1
	$id=0;
	while( $id < $idproduct ){
		$consigneProduct->init();
		$consigneProduct->fk_product=$idproduct;
		$id=$consigneProduct->create($user);
		if( $id < 0) { // echec de la création
			dol_syslog("tabProductConsigne.php::echec de la création de l'objet consigneProduct", LOG_ERR);
			exit;
		}
		
		if( $id < $idproduct){
			$sqlP = "SELECT * FROM ".MAIN_DB_PREFIX.$product->table_element." as t WHERE t.rowid = $id";
			$resqlP = $db->query($sqlP);
			$nbP = $db->num_rows($resqlP);
			
			if( $nbP === 0 ) { // aucun produit avec cet id
				$consigneProduct->delete($user); // on supprime l'objet consigneProduct
			}
		}
	}
} else if ($nb === 1 ){
	// ok 1 seul enregistrement pour le produit idproduct
	$obj= $db->fetch_object($resql);
	if ( empty($obj)) {}		// Should not happen
	else { $id=$obj->rowid;}
} else { // erreurs
	if( $nb > 1 ){ // plusieurs consigneProduct pour le même produit
		dol_syslog("tabProductConsigne.php::Erreur plusieurs consigneProduct pour le même produit", LOG_ERR);
		exit;
	}
}

// Initialize technical objects
$object=new ConsigneProduct($db);
$extrafields = new ExtraFields($db);
$result = $product->fetch($id) ;
if( !$result ){
	exit;
}

$diroutputmassaction=$conf->consigne->dir_output . '/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('consigneproductcard'));     // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('consigneproduct');
$search_array_options=$extrafields->getOptionalsFromPost($extralabels,'','search_');

// Initialize array of search criterias
$search_all=trim(GETPOST("search_all",'alpha'));
$search=array();
foreach($object->fields as $key => $val)
{
    if (GETPOST('search_'.$key,'alpha')) $search[$key]=GETPOST('search_'.$key,'alpha');
}

if (empty($action) && empty($id) && empty($ref)) $action='view';

// Security check - Protection if external user
//if ($user->societe_id > 0) access_forbidden();
//if ($user->societe_id > 0) $socid = $user->societe_id;
//$result = restrictedArea($user, 'consigne', $id);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// permissions
// $usercanread = (($object->type == Product::TYPE_PRODUCT && $user->rights->produit->lire) || ($object->type == Product::TYPE_SERVICE && $user->rights->service->lire));
// $usercancreate = (($object->type == Product::TYPE_PRODUCT && $user->rights->produit->creer) || ($object->type == Product::TYPE_SERVICE && $user->rights->service->creer));
// $usercandelete = (($object->type == Product::TYPE_PRODUCT && $user->rights->produit->supprimer) || ($object->type == Product::TYPE_SERVICE && $user->rights->service->supprimer));

$usercancreate = 1;

/*
 * Actions
 *
 * Put here all code to do according to value of "action" parameter
 */

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	$error=0;

	$permissiontoadd = $user->rights->consigne->create;
	$permissiontodelete = $user->rights->consigne->delete;
	$backurlforlist = dol_buildpath('/consigne/consigneproduct_list.php',1);

	// Actions cancel, add, update or delete
	include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';

	// Actions when printing a doc from card
	include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

	// Actions to send emails
	$trigger_name='MYOBJECT_SENTBYMAIL';
	$autocopy='MAIN_MAIL_AUTOCOPY_MYOBJECT_TO';
	$trackid='consigneproduct'.$object->id;
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';
}




/*
 * View
 *
 * Put here all code to build page
 */
 
$shortlabel = dol_trunc($object->label,16);
$helpurl ='';
$title = $langs->trans('Product')." ". $shortlabel ." - ".$langs->trans('Consigne');
// $helpurl='EN:Module_Products|FR:Module_Produits|ES:M&oacute;dulo_Productos'; // A FAIRE

$form=new Form($db);
$formfile=new FormFile($db);

llxHeader('', $title, $helpurl);

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';

// Update a product or service
    if ($action == 'update' && $usercancreate)
    {
    	if (GETPOST('cancel','alpha')) {
            $action = '';
        } else {
            if ($object->id > 0) {
		$error=0;
		
//		$object->oldcopy= clone $object;

                $object->entity                    = $entity;
                $object->description            = dol_htmlcleanlastbr(GETPOST('desc','none'));
                $object->status            = GETPOST('status');
                $object->fk_product             = $idproduct;
                $object->fk_product_emballage_vendu         = GETPOST('fk_product_emballage_vendu');
                $object->fk_product_emballage_retour          = GETPOST('fk_product_emballage_retour');
		$object->fk_product_emballage_consigne          = GETPOST('fk_product_emballage_consigne');

                $object->est_emballage_consigne_vendu                 = GETPOST('est_emballage_consigne_vendu');
                $object->est_emballage_consigne                 = GETPOST('est_emballage_consigne');
                $object->suivi_emballage           = GETPOST('suivi_emballage');
		
		$object->est_cache_bordereau_livraison           = GETPOST('est_cache_bordereau_livraison');
		$object->colisage           = GETPOST('colisage');
		
		if( $object->fk_product_emballage_vendu == "-1" ) $object->fk_product_emballage_vendu=null;
		if( $object->fk_product_emballage_retour == "-1" ) $object->fk_product_emballage_retour=null;
		if( $object->fk_product_emballage_consigne == "-1" ) $object->fk_product_emballage_consigne=null;
		if( $object->colisage== "-1" ) $object->colisage=null;
		
		if( $object->est_emballage_consigne != "1") $object->est_emballage_consigne ="0";
		if( $object->est_emballage_consigne_vendu != "1") $object->est_emballage_consigne_vendu ="0";
		if( $object->suivi_emballage != "1") $object->suivi_emballage ="0";
		
		if( $object->est_cache_bordereau_livraison != "1") $object->est_cache_bordereau_livraison ="0";

                if (! $error && $object->check())
                {
			//var_dump($object);
                    if ($object->update($user) > 0) {
                        $action = 'view';
                    } else {
			if (count($object->errors)) setEventMessages($object->error, $object->errors, 'errors');
                    	else setEventMessages($langs->trans($object->error), null, 'errors');
                        $action = 'edit';
                    }
                }
                else {
			if (count($object->errors)) setEventMessages($object->error, $object->errors, 'errors');
                	else setEventMessages($langs->trans("ErrorProductBadRefOrLabel"), null, 'errors');
			$action = 'edit';
                }
            }
	}
}

// Part to create
if ($action == 'create' and 0)
{
	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("ConsigneProduct")));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	$head = product_prepare_head($product, $user);
	dol_fiche_head($head, 'tabConsigneProduct');

	print '<table class="border centpercent">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_add.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_add.tpl.php';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" name="add" value="'.dol_escape_htmltag($langs->trans("Create")).'">';
	print '&nbsp; ';
	print '<input type="'.($backtopage?"submit":"button").'" class="button" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'"'.($backtopage?'':' onclick="javascript:history.go(-1)"').'>';	// Cancel for create does not post form if we don't know the backtopage
	print '</div>';

	print '</form>';
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("ConsigneProduct"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';

	$head = product_prepare_head($product, $user);
	dol_fiche_head($head, 'tabConsigneProduct');

	print '<table class="border centpercent">'."\n";

	// Common attributes
	include_once 'core/tpl/tabProduct_edit.tpl.php';
	//include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_edit.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_edit.tpl.php';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
    $res = $object->fetch_optionals($object->id, $extralabels);

	//$head = consigneproductPrepareHead($object);
	//dol_fiche_head($head, 'card', $langs->trans("ConsigneProduct"), -1, 'consigneproduct@consigne');
	
	$head = product_prepare_head($product, $user);
	dol_fiche_head($head, 'tabConsigneProduct');


	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="' .dol_buildpath('/consigne/consigneproduct_list.php',1) . '?restore_lastsearch_values=1' . (! empty($socid) ? '&socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';

	$morehtmlref='<div class="refidno">';
	/*
	// Ref bis
	$morehtmlref.=$form->editfieldkey("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->consigne->creer, 'string', '', 0, 1);
	$morehtmlref.=$form->editfieldval("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->consigne->creer, 'string', '', null, null, '', 1);
	// Thirdparty
	$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $soc->getNomUrl(1);
	// Project
	if (! empty($conf->projet->enabled))
	{
	    $langs->load("projects");
	    $morehtmlref.='<br>'.$langs->trans('Project') . ' ';
	    if ($user->rights->consigne->creer)
	    {
	        if ($action != 'classify')
	        {
	            $morehtmlref.='<a href="' . $_SERVER['PHP_SELF'] . '?action=classify&amp;id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> : ';
	            if ($action == 'classify') {
	                //$morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 1, 1);
	                $morehtmlref.='<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
	                $morehtmlref.='<input type="hidden" name="action" value="classin">';
	                $morehtmlref.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	                $morehtmlref.=$formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
	                $morehtmlref.='<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
	                $morehtmlref.='</form>';
	            } else {
	                $morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1);
	            }
	        }
	    } else {
	        if (! empty($object->fk_project)) {
	            $proj = new Project($db);
	            $proj->fetch($object->fk_project);
	            $morehtmlref.='<a href="'.DOL_URL_ROOT.'/projet/card.php?id=' . $object->fk_project . '" title="' . $langs->trans('ShowProject') . '">';
	            $morehtmlref.=$proj->ref;
	            $morehtmlref.='</a>';
	        } else {
	            $morehtmlref.='';
	        }
	    }
	}
	*/
	$morehtmlref.='</div>';
	
	$linkback = '<a href="'.DOL_URL_ROOT.'/product/list.php?restore_lastsearch_values=1&type='.$object->type.'">'.$langs->trans("BackToList").'</a>';
            $object->next_prev_filter=" fk_product_type = ".$object->type;

            $shownav = 1;
            if ($user->societe_id && ! in_array('product', explode(',',$conf->global->MAIN_MODULES_FOR_EXTERNAL))) $shownav=0;

            dol_banner_tab($product, 'ref', $linkback, $shownav, 'ref');


	//dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);


	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent">'."\n";

	// Common attributes
	//$keyforbreak='fieldkeytoswithonsecondcolumn';
	include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_view.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

	print '</table>';
	print '</div>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div><br>';

	dol_fiche_end();


	// Buttons for actions
	if ($action != 'presend' && $action != 'editline') {
    	print '<div class="tabsAction">'."\n";
    	$parameters=array();
    	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

    	if (empty($reshook))
    	{
    	    
		// bouton MODIFIER
    		if ($user->rights->produit->creer)
    		{
    			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a>'."\n";
    		}
    		else
    		{
    			print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans('Modify').'</a>'."\n";
    		}

    	}
    	print '</div>'."\n";
	}
}


// End of page
llxFooter();
$db->close();
