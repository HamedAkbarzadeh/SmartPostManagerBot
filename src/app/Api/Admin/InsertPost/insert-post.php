<?php

preg_match_all("/https:\/\/t\.me\/[^\s)]+/", $text, $matches);

if ($matches[0][0]) {
    foreach ($matches[0] as $link) {
        $queries = $explodes = explode("&", explode("?", $link)[1]);
        $server = explode("=", $queries[0])[1] ?? null;
        $port = explode("=", $queries[1])[1] ?? null;
        $secret = explode("=", $queries[2])[1] ?? null;
        $sql->table('proxies')->insert(['link', 'status', 'port', 'server', 'secret', 'added_by_user_id'], [$link, 0, $port, $server, $secret, $user['id']]);
    }

    $telegramApi->sendMessage('با موفقیت اضافه شد .');
}
