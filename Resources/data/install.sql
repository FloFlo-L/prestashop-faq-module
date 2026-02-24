-- FAQ category
CREATE TABLE IF NOT EXISTS `PREFIX_faq_category` (
    `id_faq_category` INT AUTO_INCREMENT NOT NULL,
    `icon` VARCHAR(255) NOT NULL DEFAULT '',
    `position` INT NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_faq_category`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

-- FAQ category translations
CREATE TABLE IF NOT EXISTS `PREFIX_faq_category_lang` (
    `id_faq_category` INT NOT NULL,
    `id_lang` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    INDEX IDX_FAQ_CATEGORY (`id_faq_category`),
    INDEX IDX_FAQ_CATEGORY_LANG (`id_lang`),
    PRIMARY KEY (`id_faq_category`, `id_lang`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

-- FAQ questions and answers
CREATE TABLE IF NOT EXISTS `PREFIX_faq` (
    `id_faq` INT AUTO_INCREMENT NOT NULL,
    `id_faq_category` INT NOT NULL,
    `position` INT NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    INDEX IDX_FAQ_CATEGORY (`id_faq_category`),
    PRIMARY KEY (`id_faq`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

-- FAQ questions and answers translations
CREATE TABLE IF NOT EXISTS `PREFIX_faq_lang` (
    `id_faq` INT NOT NULL,
    `id_lang` INT NOT NULL,
    `question` VARCHAR(512) NOT NULL DEFAULT '',
    `answer` LONGTEXT NULL,
    INDEX IDX_FAQ (`id_faq`),
    INDEX IDX_FAQ_LANG (`id_lang`),
    PRIMARY KEY (`id_faq`, `id_lang`)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
