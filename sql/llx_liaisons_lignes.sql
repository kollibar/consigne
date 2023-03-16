CREATE TABLE `llx2q_liaisons_lignes` (
  `rowid` integer NOT NULL,
  `type` VARCHAR(32) NOT NULL,
  `fk_source` integer NOT NULL,
  `fk_target` integer NOT NULL
) ENGINE=InnoDB;