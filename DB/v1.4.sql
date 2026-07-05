/* =========================
   Invoice Update
========================= */

ALTER TABLE `invoices`
    ADD COLUMN `type` VARCHAR(255) NOT NULL DEFAULT 'invoice' AFTER `user_id`,
    MODIFY `issue_date` DATE NULL,
    MODIFY `currency` VARCHAR(255) NULL,
    MODIFY `to` VARCHAR(255) NULL,
    MODIFY `address` TEXT NULL,
    MODIFY `email` TEXT NULL,
    MODIFY `items` JSON NULL,
    MODIFY `charge` DECIMAL(28,8) NULL,
    MODIFY `amount` DECIMAL(28,8) NULL,
    MODIFY `total_amount` DECIMAL(28,8) NULL;


/* =========================
   Megamenu Tables
========================= */

CREATE TABLE `megamenu_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `navigation_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(255) NULL,
    `url` VARCHAR(255) NULL,
    `page_id` BIGINT UNSIGNED NULL,
    `featured_image` VARCHAR(255) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` TINYINT NOT NULL DEFAULT 1,
    `translate` LONGTEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
);


/* =========================
   Navigation Updates
========================= */

ALTER TABLE `navigations`
    ADD COLUMN `has_megamenu` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

ALTER TABLE `navigations`
    ADD COLUMN `megamenu_type` TINYINT NOT NULL DEFAULT 1 AFTER `has_megamenu`;

ALTER TABLE `navigations`
    ADD COLUMN `megamenu_name` VARCHAR(255) NULL AFTER `name`;


/* =========================
   Megamenu Item Updates
========================= */

ALTER TABLE `megamenu_items`
    RENAME COLUMN `featured_image` TO `preview_image`;

ALTER TABLE `megamenu_items`
    ADD COLUMN `preview_title` VARCHAR(255) NULL AFTER `url`,
    ADD COLUMN `preview_description` TEXT NULL AFTER `preview_title`;


/* =========================
   Pages Update
========================= */

ALTER TABLE `pages`
    MODIFY COLUMN `type` ENUM('static', 'dynamic', 'service') NULL;
