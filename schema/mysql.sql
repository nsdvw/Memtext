CREATE TABLE `user`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `salt` VARCHAR(255) NOT NULL,
    `saltedHash` VARCHAR(255) NOT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `shortdict`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `keyword` VARCHAR(255) NOT NULL,
    `definition` TEXT NOT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `fulldict`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `keyword` VARCHAR(255) NOT NULL,
    `definition` TEXT NOT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_shortdict`
(
    `user_id` INT UNSIGNED NOT NULL,
    `shortdict_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `shortdict_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`shortdict_id`) REFERENCES `shortdict` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `text`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `author_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`author_id`) REFERENCES `user`(`id`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `text_shortdict`
(
    `text_id` INT UNSIGNED NOT NULL,
    `shortdict_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`text_id`, `shortdict_id`),
    FOREIGN KEY (`text_id`) REFERENCES `text` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`shortdict_id`) REFERENCES `shortdict` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `text_fulldict`
(
    `text_id` INT UNSIGNED NOT NULL,
    `fulldict_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`text_id`, `fulldict_id`),
    FOREIGN KEY (`text_id`) REFERENCES `text` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`fulldict_id`) REFERENCES `fulldict` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
