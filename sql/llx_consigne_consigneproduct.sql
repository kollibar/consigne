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
	description text, 
	date_creation datetime NOT NULL, 
	tms timestamp NOT NULL, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	status integer NOT NULL, 
	fk_product integer NOT NULL, 
	entity INTEGER DEFAULT 1 NOT NULL,
	est_emballage_consigne integer NOT NULL, 
	suivi_emballage integer NOT NULL, 
	fk_product_emballage_vendu integer, 
	fk_product_emballage_retour integer, 
	fk_product_emballage_consigne integer, 
	est_emballage_consigne_vendu integer NOT NULL,
	est_emballage_consigne_retour integer NOT NULL,
	est_cache_bordereau_livraison integer DEFAULT 0 NOT NULL, 
	colisage integer 
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
