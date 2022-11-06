-- Húzás eljárása. Felülírja az előző húzás eredményét!

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `draw` ()  COMMENT 'Húzás eljárása. Felülírja az előző húzás eredményét!' BEGIN
  DECLARE i INT DEFAULT 0;

  TRUNCATE `link`;

  WHILE i < (SELECT COUNT(`id`) FROM `users`) DO
    SET @blacklist = (SELECT GROUP_CONCAT(`recipient`) FROM `link`);
    SET @id = (SELECT `id` FROM `users` ORDER BY `id` LIMIT 1 OFFSET i);
  	INSERT INTO link(secret_santa, recipient) VALUES (
      @id, 
      (SELECT `id` FROM `users` WHERE 
        (FIND_IN_SET(`id`, @blacklist) IS NULL OR 
        FIND_IN_SET(`id`, @blacklist) = 0) AND
        `id` <> @id
      ORDER BY RAND() LIMIT 1));
    SET i = i + 1;
  END WHILE;
END$$