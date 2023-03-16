-- Copyright (C) 2018 Thomas Kolli <thomas@brasserieteddybeer.fr>
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
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.


CREATE TABLE llx_myobject(
	rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
	-- BEGIN MODULEBUILDER FIELDS
	entity INTEGER DEFAULT 1 NOT NULL,
	label VARCHAR(255),
	qty INTEGER,
	status INTEGER,
	date_creation DATETIME NOT NULL,
	tms TIMESTAMP NOT NULL,
	import_key VARCHAR(14)
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;