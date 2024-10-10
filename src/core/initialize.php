<?php

namespace src\core;

if (!defined("TOKEN")) {
    define("TOKEN", '7775020157:AAF4xz9z8fDqCDPyrGPiGOt7XhZ-4dZhH9c');
}
if (!defined("DOMAIN")) {
    define('DOMAIN', '');
}
if (!defined('API')) {
    define('API', "https://api.telegram.org/bot" . TOKEN . "/");
}
if (!defined('BOT_USERNAME')) {
    define('BOT_USERNAME', 'https://t.me/pharseProxyBot');
}
//DB Config
if (!defined('DB_NAME')) {
    define('DB_NAME', 'moblekho_proxydb');
}
if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'moblekho_hmd');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', 'W!(ji}H(V$!e');
}
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}