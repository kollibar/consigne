<?php
/* Copyright (C) 2015   Jean-François Ferry     <jfefe@aternatik.fr>
 * Copyright (C) 2018 Thomas Kolli <thomas@brasserieteddybeer.fr>
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

use Luracast\Restler\RestException;

/**
 * \file    consigne/class/api_consigneProduct.class.php
 * \ingroup consigne
 * \brief   File for API management of consigneProduct.
 */

/**
 * API class for consigne consigneProduct
 *
 * @smart-auto-routing false
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class consigneProductApi extends DolibarrApi
{
    /**
     * @var array   $FIELDS     Mandatory fields, checked when create and update object
     */
    static $FIELDS = array(
        'name'
    );

    /**
     * @var consigneProduct $consigneProduct {@type consigneProduct}
     */
    public $consigneProduct;

    /**
     * Constructor
     *
     * @url     GET consigneProduct/
     *
     */
    function __construct()
    {
		global $db, $conf;
		$this->db = $db;
        $this->consigneProduct = new consigneProduct($this->db);
    }

    /**
     * Get properties of a consigneProduct object
     *
     * Return an array with consigneProduct informations
     *
     * @param 	int 	$id ID of consigneProduct
     * @return 	array|mixed data without useless information
	 *
     * @url	GET consigneProduct/{id}
     * @throws 	RestException
     */
    function get($id)
    {
		if(! DolibarrApiAccess::$user->rights->consigneProduct->read) {
			throw new RestException(401);
		}

        $result = $this->consigneProduct->fetch($id);
        if( ! $result ) {
            throw new RestException(404, 'consigneProduct not found');
        }

		if( ! DolibarrApi::_checkAccessToResource('consigneProduct',$this->consigneProduct->id)) {
			throw new RestException(401, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		return $this->_cleanObjectDatas($this->consigneProduct);
    }

    /**
     * List consigneProducts
     *
     * Get a list of consigneProducts
     *
     * @param int		$mode		Use this param to filter list
     * @param string	$sortfield	Sort field
     * @param string	$sortorder	Sort order
     * @param int		$limit		Limit for list
     * @param int		$page		Page number
     * @param string    $sqlfilters Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101') or (t.import_key:=:'20160101')"
     * @return array Array of consigneProduct objects
     *
     * @url	GET /consigneProducts/
     */
    function index($mode, $sortfield = "t.rowid", $sortorder = 'ASC', $limit = 0, $page = 0, $sqlfilters = '') {
        global $db, $conf;

        $obj_ret = array();

        $socid = DolibarrApiAccess::$user->societe_id ? DolibarrApiAccess::$user->societe_id : '';

        // If the internal user must only see his customers, force searching by him
        if (! DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) $search_sale = DolibarrApiAccess::$user->id;

        $sql = "SELECT s.rowid";
        if ((!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql .= ", sc.fk_soc, sc.fk_user"; // We need these fields in order to filter by sale (including the case where the user can only see his prospects)
        $sql.= " FROM ".MAIN_DB_PREFIX.$consigneProduct->table_element." as s";

        if ((!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql.= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc"; // We need this table joined to the select in order to filter by sale
        $sql.= ", ".MAIN_DB_PREFIX."c_stcomm as st";
        $sql.= " WHERE s.fk_stcomm = st.id";

		// Example of use $mode
        //if ($mode == 1) $sql.= " AND s.client IN (1, 3)";
        //if ($mode == 2) $sql.= " AND s.client IN (2, 3)";

        $sql.= ' AND s.entity IN ('.getEntity('consigneProduct').')';
        if ((!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) $sql.= " AND s.fk_soc = sc.fk_soc";
        if ($socid) $sql.= " AND s.fk_soc = ".$socid;
        if ($search_sale > 0) $sql.= " AND s.rowid = sc.fk_soc";		// Join for the needed table to filter by sale
        // Insert sale filter
        if ($search_sale > 0)
        {
            $sql .= " AND sc.fk_user = ".$search_sale;
        }
        if ($sqlfilters)
        {
            if (! DolibarrApi::_checkFilters($sqlfilters))
            {
                throw new RestException(503, 'Error when validating parameter sqlfilters '.$sqlfilters);
            }
	        $regexstring='\(([^:\'\(\)]+:[^:\'\(\)]+:[^:\(\)]+)\)';
            $sql.=" AND (".preg_replace_callback('/'.$regexstring.'/', 'DolibarrApi::_forge_criteria_callback', $sqlfilters).")";
        }

        $sql.= $db->order($sortfield, $sortorder);
        if ($limit)	{
            if ($page < 0)
            {
                $page = 0;
            }
            $offset = $limit * $page;

            $sql.= $db->plimit($limit + 1, $offset);
        }

        $result = $db->query($sql);
        if ($result)
        {
            $num = $db->num_rows($result);
            while ($i < $num)
            {
                $obj = $db->fetch_object($result);
                $consigneProduct_static = new consigneProduct($db);
                if($consigneProduct_static->fetch($obj->rowid)) {
                    $obj_ret[] = parent::_cleanObjectDatas($consigneProduct_static);
                }
                $i++;
            }
        }
        else {
            throw new RestException(503, 'Error when retrieve consigneProduct list');
        }
        if( ! count($obj_ret)) {
            throw new RestException(404, 'No consigneProduct found');
        }
		return $obj_ret;
    }

    /**
     * Create consigneProduct object
     *
     * @param array $request_data   Request datas
     * @return int  ID of consigneProduct
     *
     * @url	POST consigneProduct/
     */
    function post($request_data = NULL)
    {
        if(! DolibarrApiAccess::$user->rights->consigneProduct->create) {
			throw new RestException(401);
		}
        // Check mandatory fields
        $result = $this->_validate($request_data);

        foreach($request_data as $field => $value) {
            $this->consigneProduct->$field = $value;
        }
        if( ! $this->consigneProduct->create(DolibarrApiAccess::$user)) {
            throw new RestException(500);
        }
        return $this->consigneProduct->id;
    }

    /**
     * Update consigneProduct
     *
     * @param int   $id             Id of consigneProduct to update
     * @param array $request_data   Datas
     * @return int
     *
     * @url	PUT consigneProduct/{id}
     */
    function put($id, $request_data = NULL)
    {
        if(! DolibarrApiAccess::$user->rights->consigneProduct->create) {
			throw new RestException(401);
		}

        $result = $this->consigneProduct->fetch($id);
        if( ! $result ) {
            throw new RestException(404, 'consigneProduct not found');
        }

		if( ! DolibarrApi::_checkAccessToResource('consigneProduct',$this->consigneProduct->id)) {
			throw new RestException(401, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

        foreach($request_data as $field => $value) {
            $this->consigneProduct->$field = $value;
        }

        if($this->consigneProduct->update($id, DolibarrApiAccess::$user))
            return $this->get ($id);

        return false;
    }

    /**
     * Delete consigneProduct
     *
     * @param   int     $id   consigneProduct ID
     * @return  array
     *
     * @url	DELETE consigneProduct/{id}
     */
    function delete($id)
    {
        if(! DolibarrApiAccess::$user->rights->consigneProduct->supprimer) {
			throw new RestException(401);
		}
        $result = $this->consigneProduct->fetch($id);
        if( ! $result ) {
            throw new RestException(404, 'consigneProduct not found');
        }

		if( ! DolibarrApi::_checkAccessToResource('consigneProduct',$this->consigneProduct->id)) {
			throw new RestException(401, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

        if( !$this->consigneProduct->delete($id))
        {
            throw new RestException(500);
        }

         return array(
            'success' => array(
                'code' => 200,
                'message' => 'consigneProduct deleted'
            )
        );

    }

    /**
     * Validate fields before create or update object
     *
     * @param array $data   Data to validate
     * @return array
     *
     * @throws RestException
     */
    function _validate($data)
    {
        $consigneProduct = array();
        foreach (consigneProductApi::$FIELDS as $field) {
            if (!isset($data[$field]))
                throw new RestException(400, "$field field missing");
            $consigneProduct[$field] = $data[$field];
        }
        return $consigneProduct;
    }
    
    /**
     * test si l'objet est un object consigne
     *
     * @param int   $idproduct             Id of consigneProduct to update
     * @return boolean
     *
     */
    
    function isConsigne($idproduct){
	global $db, $conf;
	
	$sql="SELECT * FROM ".MAIN_DB_PREFIX.$consigneProduct->table_element." as t";
	$sql.=" WHERE t.fk_product = $id";
	
	$resql = $db->query($sql);
	$nb = $db->num_rows($resql);

	if ($nb === 0){ // aucun enregistrement
		dol_syslog("api_consigneProduct.php::Erreur aucun consigneProduct lié à l'id produit $idproduct", LOG_DEBUG);
		return false;
	} else if ($nb === 1 ){
		// ok 1 seul enregistrement pour le produit idproduct
		$obj= $db->fetch_object($resql);
		if ( empty($obj)) {
			dol_syslog("api_consigneProduct.php::objet null -- ne devrait pas arriver !!!", LOG_ERR);
			return false;
		}		
		consigneProduct->fetch($obj->rowid);
		return ( consigneProduct->est_emballage_consigne == "1" );
		
	} else { // erreurs
		if( $nb > 1 ){ // plusieurs consigneProduct pour le même produit
			dol_syslog("api_ProductConsigne.php::Erreur plusieurs consigneProduct pour le même produit", LOG_ERR);
			exit;
		}
	}
    }
    
    /**
     * test si l'objet est un object retour de consigne
     *
     * @param int   $idproduct             Id of consigneProduct to update
     * @return boolean
     *
     */
    
    function isConsigneRetour($idproduct){
	global $db, $conf;
	
	$sql="SELECT * FROM ".MAIN_DB_PREFIX.$consigneProduct->table_element." as t";
	$sql.=" WHERE t.fk_product = $id";
	
	$resql = $db->query($sql);
	$nb = $db->num_rows($resql);

	if ($nb === 0){ // aucun enregistrement
		dol_syslog("api_consigneProduct.php::Erreur aucun consigneProduct lié à l'id produit $idproduct", LOG_DEBUG);
		return false;
	} else if ($nb === 1 ){
		// ok 1 seul enregistrement pour le produit idproduct
		$obj= $db->fetch_object($resql);
		if ( empty($obj)) {
			dol_syslog("api_consigneProduct.php::objet null -- ne devrait pas arriver !!!", LOG_ERR);
			return false;
		}		
		consigneProduct->fetch($obj->rowid);
		return ( consigneProduct->est_emballage_consigne_retour == "1" );
		
	} else { // erreurs
		if( $nb > 1 ){ // plusieurs consigneProduct pour le même produit
			dol_syslog("api_ProductConsigne.php::Erreur plusieurs consigneProduct pour le même produit", LOG_ERR);
			exit;
		}
	}
    }
}
