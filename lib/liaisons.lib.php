<?php


/**
 * Ajoute une liaison entre 2 lignes
 * @param   Object  $object     objet pour lequel ajouter une liaison. doit être un FactureLigne, CommandeLigne ou PropalLigne (aucune vérification)
 * @param   int     $fk_target  id de l'autre objet à lié. Doit être du même type (aucune vérification)
 * @return	int					<0 if KO, >0 if OK
 */

function ll_ajouteLiaison(&$object, $fk_target){
    global $db, $error;
    // création de la liaison entre les lignes
    $sql = "INSERT INTO " . MAIN_DB_PREFIX. "liaisons_lignes ";
    $sql .= "(type, fk_source, fk_target) VALUES (" . $object->element . "," . $object->id .",". $fk_target . ")";

    dol_syslog(__FILE__."::ajouteLiaison ".$object->element, LOG_DEBUG);

    $res = $db->query($sql);
    if ($res===false)
    {
        $error++;
        $object->errors[] = $db->lasterror();
        return -1;
    }
    return 1;
}

/**
 * Ajoute une liaison entre 2 lignes
 * @param   User    $user       utilisateur
 * @param   Object  $object     objet pour lequel supprimer une liaison. doit être un FactureLigne, CommandeLigne ou PropalLigne (aucune vérification)
 * @param   bool    $supprObjetLie  si true, supprime aussi l'(les) objet(s) lié(s).
 * @param   int     $fk_target  id de l'autre objet pour lequel supprimer la liaison. Si non fourni, supprime toute les liaisons de $object. Doit être du même type (aucune vérification)
 * @return	int					<0 if KO, >0 if OK, 0 si aucune action faite
 */

function ll_supprimerLiaison(User &$user, &$object, $supprObjetLie=false, $fk_target=-1){
    global $db, $error;

    $sql = "SELECT * FROM ". MAIN_DB_PREFIX . "liaisons_lignes ";
    if( $fk_target > 0){
        $sql .= " WHERE type = " . $object->element ." AND ( ( fk_target = " . $object->id . " AND fk_source = " . $fk_target . ") OR ( fk_source = " . $object->id . " AND fk_target = ". $fk_target . ") )";
    } else {
        $sql .= " WHERE type = " . $object->element ." AND ( fk_target = " . $object->id . " OR fk_source = " . $object->id . " )";
    }

    dol_syslog(__FILE__."::supprimerLiaison ".$object->element, LOG_DEBUG);
    $result = $db->query($sql);
    if ($result)
    {
        $num = $db->num_rows($result);

        if( $supprObjetLie ){   // si suppression des éléments
            $i = 0;
            $class  = get_class($object);
            while ($i < $num)
            {
                $objp = $db->fetch_object($result);
                $ligneLiee = ($objp->fk_source != $object->id?$objp->fk_source:$objp->fk_target);

                // Ajouter changement d'objet
                $ligne = new $class; // le fichier est il chargé ??
                $ligne->fetch($ligneLiee);
                $ligne->delete($user, true);

                $i++;
            }
        }

        $db->free($result);

        if( $num > 0 ){ // il y a des liaison à supprimer

            $sql = "DELETE FROM ". MAIN_DB_PREFIX . $object->table_element . "_liaisons ";
            if( $fk_target > 0){
                $sql .= " WHERE type = " . $object->element ." AND ( ( fk_target = " . $object->id . " AND fk_source = " . $fk_target . ") OR ( fk_source = " . $object->id . " AND fk_target = ". $fk_target . ") )";
            } else {
                $sql .= " WHERE type = " . $object->element ." AND ( fk_target = " . $object->id . " OR fk_source = " . $object->id . " )";
            }

            $result = $db->query($sql);
            if( $result < 0 ){ //
                dol_syslog(__FILE__." SupprimerLiaison ".$object->element . ":: erreur :".$result, LOG_ERR);
                return -1;
            }
            return 1;
        }
        return 0;
    }
    return -2;
}

/**
 * Retourne la liste des objets liées à une ligne.
 * @param   Object  $object     objet pour lequel récuperer la liste des liaisons. doit être un FactureLigne, CommandeLigne ou PropalLigne (aucune vérification)
 * @return	Array				tableau des id d'objets liés. vide si KO ou aucun
 */

function ll_getLiaisons(&$object){
    global $db, $error;

    $sql = "SELECT * FROM ". MAIN_DB_PREFIX . $object->table_element . "_liaisons ";
	$sql .= " WHERE type = ".$object->element . "AND ( fk_target = " . $object->id . " OR fk_source = " . $object->id . " )";

    $liste=array();

    dol_syslog(__FILE__."::getLiaisons ".$object->element, LOG_DEBUG);

    $result = $db->query($sql);
    if ($result)
    {
        $num = $db->num_rows($result);

        $i = 0;
        while ($i < $num)
        {
            $objp = $db->fetch_object($result);
            $liste[] = ($objp->fk_source != $object->id?$objp->fk_source:$objp->fk_target);

            $i++;
        }

        $db->free($result);
    } else {
        $error++;
        $object->errors[] = $db->lasterror();
    }
    return $liste;
}