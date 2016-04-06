CREATE TABLE `user`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `salt` VARCHAR(255) NOT NULL,
    `saltedHash` VARCHAR(255) NOT NULL COMMENT 'sha1(salt+password)'
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;;

CREATE TABLE `text`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `dictionary` TEXT NOT NULL COMMENT 'dictionary serialized in json',
    `user_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `word`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `eng` VARCHAR(50) NOT NULL,
    `rus` VARCHAR(50) NOT NULL,
    UNIQUE (`eng`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
