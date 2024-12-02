CREATE TABLE files(
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_id VARCHAR(255) NOT NULL,
    caption TEXT NULL,
    proxy_count VARCHAR(64) DEFAULT 0,
    file_type VARCHAR(255) NOT NULL,
    file_size VARCHAR(255) NOT NULL,
    `status` TINYINT DEFAULT 1 COMMENT '0 means deActive , 1 means active',
    has_spoiler TINYINT DEFAULT 0 COMMENT '0 means not have , 1 means have spoiler',
    media_group_id VARCHAR(255) NULL,


        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL,
        published_at TIMESTAMP NULL
        );