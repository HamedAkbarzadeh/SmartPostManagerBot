<?php

preg_match_all("/https:\/\/t\.me\/proxy[^\s)]+/", $telegramApi->getText(), $matches);

if (isset($telegramApi->getCaption_entities()[0])) {
    foreach ($telegramApi->getCaption_entities() as $entitie) {
        if ($entitie['type'] == 'text_link') {
            $matches[0][] = $entitie['url'];
        }
    }
}

if (isset($telegramApi->getEntities()[0])) {
    foreach ($telegramApi->getEntities() as $entitie) {
        if ($entitie['type'] == 'text_link') {
            $matches[0][] = $entitie['url'];
        }
    }
}
if ($matches[0]) {
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["insert-post"]);

    foreach ($matches[0] as $link) {
        $queries = $explodes = explode("&", explode("?", $link)[1]);
        $server = explode("=", $queries[0])[1] ?? null;
        $port = explode("=", $queries[1])[1] ?? null;
        $secret = explode("=", $queries[2])[1] ?? null;
        $sql->table('proxies')->insert(['link', 'status', 'port', 'server', 'secret', 'added_by_user_id'], [$link, 0, $port, $server, $secret, $user['id']]);
    }
    $text = "به تعداد " . count($matches[0]) . "  عدد پست ثبت شد .";
    $telegramApi->sendMessage($text);
}
