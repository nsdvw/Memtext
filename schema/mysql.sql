CREATE TABLE `user`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `salt` VARCHAR(255) NOT NULL,
    `salted_hash` VARCHAR(255) NOT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `dictionary`
(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `keyword` VARCHAR(255) NOT NULL,
    `definition` TEXT NOT NULL,
    `type` ENUM('short', 'full') NOT NULL DEFAULT 'full',
    KEY (`keyword`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_dictionary`
(
    `user_id` INT UNSIGNED NOT NULL,
    `word_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `word_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`word_id`) REFERENCES `dictionary` (`id`)
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

CREATE TABLE `text_dictionary`
(
    `text_id` INT UNSIGNED NOT NULL,
    `word_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`text_id`, `word_id`),
    FOREIGN KEY (`text_id`) REFERENCES `text` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (`word_id`) REFERENCES `dictionary` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
