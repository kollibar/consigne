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


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_consigne_consigneproduct ADD INDEX idx_consigne_consigneproduct_rowid (rowid);
ALTER TABLE llx_consigne_consigneproduct ADD INDEX idx_consigne_consigneproduct_status (status);
ALTER TABLE llx_consigne_consigneproduct ADD INDEX idx_consigne_consigneproduct_fk_product (fk_product);
-- END MODULEBUILDER INDEXES

--ALTER TABLE llx_consigne_consigneproduct ADD UNIQUE INDEX uk_consigne_consigneproduct_fieldxyz(fieldx, fieldy);

--ALTER TABLE llx_consigne_consigneproduct ADD CONSTRAINT llx_consigne_consigneproduct_field_id FOREIGN KEY (fk_field) REFERENCES llx_myotherobject(rowid);

