<?php

namespace src\core;

if (!defined("TOKEN")) {
    define("TOKEN", '');
}
if (!defined("DOMAIN")) {
    define('DOMAIN', '');
}
if (!defined("BOT_NAME")) {
    define('BOT_NAME', '');
}
if (!defined('CHANNEL_ID')) {
    define('CHANNEL_ID', "");
}
if (!defined("CHANNEL_USERNAME")) {
    define('CHANNEL_USERNAME', "");
}
if (!defined('API')) {
    define('API', "https://api.telegram.org/bot" . TOKEN . "/");
}
if (!defined('BOT_USERNAME')) {
    define('BOT_USERNAME', '');
}
//DB Config
if (!defined('DB_NAME')) {
    define('DB_NAME', '');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', '');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
