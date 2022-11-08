<?php
/* Copyright (C) 2004-2018 Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**
 * 	\defgroup   consigne     Module Consigne
 *  \brief      Consigne module descriptor.
 *
 *  \file       htdocs/consigne/core/modules/modConsigne.class.php
 *  \ingroup    consigne
 *  \brief      Description and activation file for module Consigne
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


// The class name should start with a lower case mod for Dolibarr to pick it up
// so we ignore the Squiz.Classes.ValidClassName.NotCamelCaps rule.
// @codingStandardsIgnoreStart
/**
 *  Description and activation class for module Consigne
 */
class modConsigne extends DolibarrModules
{
	// @codingStandardsIgnoreEnd
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $this->db Database handler
	 */
	public function __construct($db)
	{
        global $langs,$conf;

        $this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 445401;		// TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve id number for your module
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'consigne';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','interface','other'
		// It is used to group modules by family in module setup page
		$this->family = "products";
		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';
		// Gives the possibility to the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));

		// Module label (no space allowed), used if translation string 'ModuleConsigneName' not found (MyModue is name of module).
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleConsigneDesc' not found (MyModue is name of module).
		$this->description = "Gestion de retour et facturation de consignes";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "ConsigneDescription (Long)";

		$this->editor_name = 'SCOP Au-delà des nuages';
		$this->editor_url = 'https://www.audeladesnuages.fr';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where CONSIGNE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='generic';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /consigne/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /consigne/core/modules/barcode)
		// for specific css file (eg: /consigne/css/consigne.css.php)
		$this->module_parts = array(
		                        	'triggers' => 1,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
									'login' => 0,                                    	// Set this to 1 if module has its own login method file (core/login)
									'substitutions' => 1,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
									'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
									'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
									'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
									'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
									'css' => array('/consigne/css/consigne.css.php'),	// Set this to relative path of css file if module has its own css file
	 								'js' => array('/consigne/js/consigne.js.php'),          // Set this to relative path of js file if module must load a js on all pages
									'hooks' => array('data'=>array('hookcontext1','hookcontext2'), 'entity'=>'0') 	// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context 'all'
		                        );

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/consigne/temp","/consigne/subdir");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into consigne/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@consigne");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of module class names as string that must be enabled if this module is enabled
		$this->requiredby = array();	// List of module ids to disable if this one is disabled
		$this->conflictwith = array();	// List of module class names as string this module is in conflict with
		$this->langfiles = array("consigne@consigne");
		$this->phpmin = array(5,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(6,0);	// Minimum version of Dolibarr required by module
		$this->warnings_activation = array();                     // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array();                 // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		//$this->automatic_activation = array('FR'=>'ConsigneWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('CONSIGNE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('CONSIGNE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(
			1=>array('CONSIGNE_MYCONSTANT', 'chaine', 'avalue', 'This is a constant to add', 1, 'allentities', 1)
		);


		if (! isset($conf->consigne) || ! isset($conf->consigne->enabled))
		{
			$conf->consigne=new stdClass();
			$conf->consigne->enabled=0;
		}


		// Array to add new pages in new tabs
        $this->tabs = array('data'=>'product:+tabConsigneProduct:Consigne:consigne@consigne:$user->rights->produit->lire:/consigne/tabProductConsigne.php?id=__ID__');
		// Example:
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@consigne:$user->rights->consigne->read:/consigne/mynewtab1.php?id=__ID__');  					// To add a new tab identified by code tabname1
        // $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@consigne:$user->rights->othermodule->read:/consigne/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
        // $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
        //
        // Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view


        // Dictionaries
		$this->dictionaries=array();
        /* Example:
        $this->dictionaries=array(
            'langs'=>'mylangfile@consigne',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->consigne->enabled,$conf->consigne->enabled,$conf->consigne->enabled)												// Condition to show each dictionary
        );
        */


        // Boxes/Widgets
		// Add here list of php file(s) stored in consigne/core/boxes that contains class to show a widget.
        $this->boxes = array(
        	//0=>array('file'=>'consignewidget1.php@consigne','note'=>'Widget provided by Consigne','enabledbydefaulton'=>'Home'),
        	//1=>array('file'=>'consignewidget2.php@consigne','note'=>'Widget provided by Consigne'),
        	//2=>array('file'=>'consignewidget3.php@consigne','note'=>'Widget provided by Consigne')
        );


		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
			0=>array('label'=>'MyJob label', 'jobtype'=>'method', 'class'=>'/consigne/class/retourconsigne.class.php', 'objectname'=>'RetourConsigne', 'method'=>'doScheduledJob', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true)
		);
		// Example: $this->cronjobs=array(0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true),
		//                                1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>true)
		// );


		// Permissions
		$this->rights = array();		// Permission array used by this module

		$r=0;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Read retourconsigne of Consigne';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'read';				// In php code, permission will be checked by test if ($user->rights->consigne->level1->level2)
		$this->rights[$r][5] = '';				    // In php code, permission will be checked by test if ($user->rights->consigne->level1->level2)

		$r++;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update retourconsigne of Consigne';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'write';				// In php code, permission will be checked by test if ($user->rights->consigne->level1->level2)
		$this->rights[$r][5] = '';				    // In php code, permission will be checked by test if ($user->rights->consigne->level1->level2)

		$r++;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete retourconsigne of Consigne';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'delete';				// In php code, permission will be checked by test if ($user->rights->consigne->level1->level2)
		$this->rights[$r][5] = '';				    // In php code, permission will be checked by test if ($user->rights->consigne->level1->level2)


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;

		// Add here entries to declare new menus

		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[$r++]=array('fk_menu'=>'',			                // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'top',			                // This is a Top menu entry
								'titre'=>'Consigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'',
								'url'=>'/consigne/consigneindex.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->consigne->enabled',	// Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		/* END MODULEBUILDER TOPMENU */

		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT
		$this->menu[$r++]=array(	'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List RetourConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_retourconsigne_list',
								'url'=>'/consigne/retourconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(	'fk_menu'=>'fk_mainmenu=consigne,fk_leftmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New RetourConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_retourconsigne_new',
								'url'=>'/consigne/retourconsigne_page.php?action=create',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		*/

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=products',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List RetourConsigne',
								'mainmenu'=>'products',
								'leftmenu'=>'consigne',
								'url'=>'/consigne/menuConsigne.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'RetourConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_retourconsigne',
								'url'=>'/consigne/retourconsigne.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=products,fk_leftmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'Suivi des consignes',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_suiviconsigne',
								'url'=>'/consigne/suiviconsigne.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both


		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List RetourConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_retourconsigne',
								'url'=>'/consigne/retourconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		/* */

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List FacturationConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_facturationconsigne',
								'url'=>'/consigne/facturationconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both


		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List MouvementConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_mouvementconsigne',
								'url'=>'/consigne/mouvementconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both


		/* */

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List ConsigneProduct',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_consigneproduct',
								'url'=>'/consigne/consigneproduct_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne,fk_leftmenu=consigne_consigneproduct',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New ConsigneProduct',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_consigneproduct',
								'url'=>'/consigne/consigneproduct_card.php?action=create',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		/* */

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List FacturationConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_facturationconsigne',
								'url'=>'/consigne/facturationconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne,fk_leftmenu=consigne_facturationconsigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New FacturationConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_facturationconsigne',
								'url'=>'/consigne/facturationconsigne_card.php?action=create',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		/* */

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List MouvementConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_mouvementconsigne',
								'url'=>'/consigne/mouvementconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne,fk_leftmenu=consigne_mouvementconsigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New MouvementConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_mouvementconsigne',
								'url'=>'/consigne/mouvementconsigne_card.php?action=create',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		/* */

		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List RetourConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_retourconsigne',
								'url'=>'/consigne/retourconsigne_list.php',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(
                				'fk_menu'=>'fk_mainmenu=consigne,fk_leftmenu=consigne_retourconsigne',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New RetourConsigne',
								'mainmenu'=>'consigne',
								'leftmenu'=>'consigne_retourconsigne',
								'url'=>'/consigne/retourconsigne_card.php?action=create',
								'langs'=>'consigne@consigne',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1100+$r,
								'enabled'=>'$conf->consigne->enabled',  // Define condition to show or hide menu entry. Use '$conf->consigne->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->consigne->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both

		/* END MODULEBUILDER LEFTMENU MYOBJECT */


		// Exports
		$r=1;

		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		$langs->load("consigne@consigne");
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='RetourConsigneLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r]='retourconsigne@consigne';
		$keyforclass = 'RetourConsigne'; $keyforclassfile='/mymobule/class/retourconsigne.class.php'; $keyforelement='retourconsigne';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='retourconsigne'; $keyforaliasextra='extra'; $keyforelement='retourconsigne';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r]=array('mysubobject'=>'ts.rowid', 't.myfield'=>array('t.myfield2','t.myfield3')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'retourconsigne as t';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('retourconsigne').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */
	}

	/**
	 *	Function called when module is enabled.
	 *	The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *	It also creates data directories
	 *
     *	@param      string	$options    Options when enabling module ('', 'noboxes')
	 *	@return     int             	1 if OK, 0 if KO
	 */
	public function init($options='')
	{
		$this->_load_tables('/consigne/sql/');
		global $user;

		// Create extrafields
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$resultAdd1=$extrafields->addExtraField('fk_ligneLiee', "ligne liée (Consigne)", 'select', 1,  3, 'commandedet',   0, 0, 'null', '', 1, '', 0, 1, '', '', 'consigne@consigne', '$conf->consigne->enabled');
		//$result1=$extrafields->addExtraField('myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', 0, 0, '', '', 'consigne@consigne', '$conf->consigne->enabled');
		//$result2=$extrafields->addExtraField('myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', 0, 0, '', '', 'consigne@consigne', '$conf->consigne->enabled');
		//$result3=$extrafields->addExtraField('myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', 0, 0, '', '', 'consigne@consigne', '$conf->consigne->enabled');
		//$result4=$extrafields->addExtraField('myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1 '', 0, 0, '', '', 'consigne@consigne', '$conf->consigne->enabled');
		//$result5=$extrafields->addExtraField('myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', 0, 0, '', '', 'consigne@consigne', '$conf->consigne->enabled');


		include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		dol_include_once('/consigne/class/consigneproduct.class.php');

		$product=new Product($this->db);
		$consigneProduct = new ConsigneProduct($this->db);

		// requête SQL sur toute la table produit
		$sql = 'SELECT ';
		//foreach($product->fields as $key => $val){$sql.='t.'.$key.', ';	}
		$sql.=" * ";
		$sql=preg_replace('/, $/','', $sql);
		$sql.= " FROM ".MAIN_DB_PREFIX.$product->table_element." as t";
		$sql.=" WHERE 1 = 1";
		//$sql.=$this->db->order($sortfield,$sortorder);
		// Count total nb of records
		$nbtotalofproduct = '';
		if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
		{
			$resql = $this->db->query($sql);
			$nbtotalofproduct = $this->db->num_rows($resql);
		}
		if (! $resql)
		{
			dol_print_error($this->db);
			exit;
		}

		// requête SQL sur toute la table consigneProduct
		$sqlCP = 'SELECT ';
		foreach($consigneProduct->fields as $key => $val){$sqlCP.='t.'.$key.', ';}
		$sqlCP=preg_replace('/, $/','', $sqlCP);
		$sqlCP.= " FROM ".MAIN_DB_PREFIX.$consigneProduct->table_element." as t";
		$sqlCP.=" WHERE 1 = 1";
		//$sql2.=$this->db->order($sortfield,$sortorder);
		// Count total nb of records
		$nbtotalofCP = '';
		if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
		{
			$resqlCP = $this->db->query($sqlCP);
			$nbtotalofCP = $this->db->num_rows($resqlCP);
		}
		if (! $resqlCP)
		{
			dol_print_error($this->db);
			exit;
		}

		$lastID=0;

		// parcours de tous les objets CONSIGNEPRODUCT afin de supprimer ceux dont l'objet n'existerais plus
		if( $nbtotalofCP != 0) {
			$i=0;
			while( $i < $nbtotalofCP ){
				$i++;
				$obj= $this->db->fetch_object($resqlCP);
				if (empty($obj)) break;		// Should not happen
				$id=$obj->rowid;
				$lastID=$id;

				// recherche si il existe bien un obet PRODUCT avec le même id
				$sqlID='SELECT ';
				$sqlID.='t.rowid';
				//foreach($product->fields as $key => $val){$sqlID.='t.'.$key.', ';}
				//$sqlID=preg_replace('/, $/','', $sqlID);
				$sqlID.=' FROM '.MAIN_DB_PREFIX.$product->table_element." as t WHERE t.rowid = $id";

				$resqlID = $this->db->query($sqlID);
				$nb=$this->db->num_rows($resqlID);
				if( $nb == 0) { // aucun objet PRODUCT avec l'id de l'objet CONSIGNEPRODUCT ==> Suppression de l'objet CONSIGNEPRODUCT
					$consigneProduct->id = $obj->rowid; // affectation de l'objet consigneProduct
					foreach($consigneProduct->fields as $key => $val) {if (isset($obj->$key)) $consigneProduct->$key = $obj->$key;}

					$consigneProduct->delete($user); // puis suppression
				}
			}
		}


		// parcours de tous les objets product
		$i=0;
		while ($i < $nbtotalofproduct){
			$i++;
			$obj = $this->db->fetch_object($resql);
			if (empty($obj)) break;		// Should not happen

			if( $obj->rowid > $lastID ){ // si l'id objet est supérieur à la dernière id consigneproduct (pas besoin de regarder avant, elles y sont forcément !)
				// Store properties in $object
				$product->id = $obj->rowid;
				//foreach($product->fields as $key => $val) {if (isset($obj->$key)) $product->$key = $obj->$key;}

				$sqlCP = 'SELECT ';
				$sqlCP.='* ';
				//foreach($consigneProduct->fields as $key => $val){$sql.='t.'.$key.', ';}
				$sqlCP=preg_replace('/, $/','', $sqlCP);
				$sqlCP.= " FROM ".MAIN_DB_PREFIX.$consigneProduct->table_element." as t";
				$sqlCP.=" WHERE t.rowid = ".$product->id;
				$resqlCP = $this->db->query($sqlCP);
				$nbtotalofrecords = $this->db->num_rows($resqlCP);

				if ($nbtotalofrecords == 0){ // aucun enregistrement => il faut en crée 1
					$id=0;
					while( $id < $obj->rowid){
						$consigneProduct->init();
						$consigneProduct->fk_product=$obj->rowid;
						$id=$consigneProduct->create($user);
						if( $id < 0) {
							dol_syslog("Activation module consigne: Erreur de création d'un consigneProduct !", LOG_ERR);
							exit;
						}

						if( $id < $obj->rowid){
							$consigneProduct->delete($user);
						}
					}
				}
			}
		}

		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *	Function called when module is disabled.
	 *	Remove from database constants, boxes and permissions from Dolibarr database.
	 *	Data directories are not deleted
	 *
	 *	@param      string	$options    Options when enabling module ('', 'noboxes')
	 *	@return     int             	1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

}
