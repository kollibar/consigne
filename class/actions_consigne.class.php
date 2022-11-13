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
 * \file    consigne/class/actions_consigne.class.php
 * \ingroup consigne
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */


 dol_include_once('/product/class/product.class.php');
 dol_include_once('/consigne/class/consigneproduct.class.php');

/**
 * Class ActionsConsigne
 */
class ActionsConsigne
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;
    /**
     * @var string Error
     */
    public $error = '';
    /**
     * @var array Errors
     */
    public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
	    $this->db = $db;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

        /* print_r($parameters); print_r($object); echo "action: " . $action; */
	    if (in_array($parameters['currentcontext'], array('expeditioncard')))	    // do something only for the context 'somecontext1' or 'somecontext2'
	    {
			// Do what you want here...
			// You can for example call global vars like $fieldstosearchall to overwrite them, or update database depending on $action and $_POST values.
		  }

		if (! $error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0;                                    // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}




  function  printObjectLine(&$parameters, &$object, &$action){
    global $langs,$conf;
		global $hookmanager;
    global $db;

    $error = 0; // Error counter

    // contexte d'expedition !
    if (in_array($parameters['currentcontext'], array('expeditioncard')))
    {


      // cas 1: création de l'expedition
      // $parameters = array('i' => $indiceAsked, 'line' => $line, 'num' => $numAsked);

      // cas 2: affichage/édition de l'expédition
      // $parameters = array('i' => $i, 'line' => $lines[$i], 'line_id' => $line_id,
        //    'num' => $num_prod, 'alreadysent' => $alreadysent,
        //    'editColspan' => !empty($editColspan) ? $editColspan : 0, 'outputlangs' => $outputlangs);



      $product = new Product($db);
      $consigneProduct = new ConsigneProduct($db);

      if( $line->fk_product ){
        $consigneProduct->fetch($line->fk_product);
        // $product->fetch($line->fk_product);
      } else {

      }

      if( ! array_key_exists( 'editColspan', $parameters)){ // cas 1 création de l'éxpédition


        if( $parameters['i'] == 0 ){ // 1ere ligne
          $fk_commandedet_list = array();
          $fk_product_list = array();

          foreach ($object->lines as $line) {
            $fk_commandedet_list[]=$line->id;
            $fk_product_list[]=$line->fk_product;
          }

          $product_to_check=array();

          $sql = "SELECT rowid, fk_product_emballage_consigne, fk_product";
          $sql .= " FROM ".MAIN_DB_PREFIX."consigne_consigneproduct";
          $sql .= " WHERE fk_product IN (" . implode(',',$fk_product_list) . ")";
          $sql .= " AND fk_product_emballage_consigne != -1 ";

          dol_syslog("consigne/class/actions_consigne.class.php::printObjectLine", LOG_DEBUG);
          $resql = $db->query($sql);
          if ($resql) {
            $num = $db->num_rows($resql);
            $i = 0;

            while ($i < $num) {
              $i++;
              $obj = $db->fetch_object($resql);
              if( empty($obj->fk_product_emballage_consigne) || $obj->fk_product_emballage_consigne == -1) continue;
              $product_to_check[]=$obj->fk_product;
            }

          }
          $db->free();

          $ligne_to_check=array();
          foreach ($object->lines as $line) {
            if( in_array($line->fk_product,$product_to_check)) $ligne_to_check[]=$line->id;
          }


          $liens=array();

          $sql = "SELECT fk_ligneLiee, fk_object";
          $sql .= " FROM ".MAIN_DB_PREFIX."commandedet_extrafields";
          $sql .= " WHERE fk_object IN (" . implode(',',$fk_commandedet_list) . ")";

          dol_syslog("consigne/class/actions_consigne.class.php::printObjectLine", LOG_DEBUG);
          $resql = $db->query($sql);
          if ($resql) {
            $num = $db->num_rows($resql);
            $i = 0;
            print '<script>
            let cs_liens={';

            while ($i < $num) {
              $obj = $db->fetch_object($resql);
              $liens[$obj->fk_ligneLiee]=$obj->fk_object;
              print $obj->fk_ligneLiee.':'.$obj->fk_object.',';

              $i++;
            }
            print '};
            let ligneToCheck=['.implode(',',$ligne_to_check).'];
            ';


            print 'jQuery(document).ready(function() {
                for(let i=0;i<ligneToCheck.length;i++){
                  i=cs_getIndex_commandedet(ligneToCheck[i]);
                  fk_liens=cs_liens[ligneToCheck[i]];
                  i_lie=cs_getIndex_commandedet(fk_liens);

                  cs_verifLiens(i,i_lie);
                }
                $(\'.qtyl\').change(function(){
                  console.log(\'onchange\');
                  name=$(this).attr(\'name\');
                  i=cs_getIndex(name);
                  j=cs_getSubIndex(name);

                  fk_commandedet=$(this).closest(\'table\').find(\'input[name=idl\'+i+\']\').attr(\'value\');

                  fk_liens=cs_liens[fk_commandedet];
                  i_lie=cs_getIndex_commandedet(fk_liens);

                  cs_verifLiens(i,i_lie);
                });
              });
            </script>';
          }
          $db->free();


        }

        // commandedet
        $fk_commandedet=$line->id;

      } else { // cas 2: affichage/édition de l'expédition

        // commandedet
        $fk_commandedet=$line->origin_line_id;

      }






      if( ! array_key_exists( 'editColspan', $parameters)){ // cas 1 création de l'éxpédition

        // masquage des cache sur les BL
        /*
        if( $consigneProduct->est_cache_bordereau_livraison == 1){
          print('<script>jQuery(document).ready(function() {
            $(\'a[name='.$parameters['line']->id']\').closest(\'td\').closest(\'tr\').attr(\'style\',\'display:none;\');
          }
          ');
        }
        */





      } else { // cas 2: affichage/édition de l'expédition

        // masquage des cache sur les BL
        /*
        if( $consigneProduct->est_cache_bordereau_livraison == 1){
          print('<script>jQuery(document).ready(function() {
            $(\'#row-'.$parameters['line']->id'\').attr(\'style\',\'display:none;\');
          }
          ');
        }
        */


      }

      if (! $error) {
  			$this->results = array('myreturn' => 999);
  			$this->resprints = 'A text to show';
  			return 0;                                    // or return 1 to replace standard code
  		} else {
  			$this->errors[] = 'Error message';
  			return -1;
  		}

    }
  }

  // autre hooks possibles !

  // executeHooks('formObjectOptions', $parameters, $expe, $action);
  // executeHooks('formConfirm', $parameters, $object, $action);
  // executeHooks('formObjectOptions', $parameters, $object, $action);
  // executeHooks('printObjectLine', $parameters, $object, $action);
  // executeHooks('addMoreActionsButtons', $parameters, $object, $action);

}
