CREATE TABLE proxies(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `added_by_user_id` INT,
    `link` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,
    `status` TINYINT DEFAULT 0 COMMENT '0 => not used , 1 => used',
    `port` VARCHAR(64) NULL,
    `secret` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
    `server` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
    `used_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,

    FOREIGN KEY (added_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);