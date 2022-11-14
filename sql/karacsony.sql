SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DELIMITER $$
CREATE PROCEDURE `draw` ()  COMMENT 'Húzás eljárása. Felülírja az előző húzás eredményét!' BEGIN
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

CREATE PROCEDURE `participants` ()  READS SQL DATA
BEGIN

SELECT name, CHAR_LENGTH(wish) as `szöveghossz`, wish FROM `wishes` w JOIN users u ON w.user_id = u.id;

END$$

DELIMITER ;

CREATE TABLE `link` (
  `secret_santa` int(11) NOT NULL,
  `recipient` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(300) NOT NULL,
  `name` text NOT NULL,
  `nickname` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `wishes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wish` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `link`
  ADD UNIQUE KEY `secret_santa` (`secret_santa`),
  ADD UNIQUE KEY `recipient` (`recipient`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `wishes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wishes_ibfk_1` (`user_id`);


ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `wishes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`recipient`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `link_ibfk_2` FOREIGN KEY (`secret_santa`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `wishes`
  ADD CONSTRAINT `wishes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
