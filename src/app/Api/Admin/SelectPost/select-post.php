<?php

if (strpos($telegramApi->getText(), 'sendPost_') === 0) {
    $postsCount = explode('_', $telegramApi->getText())[1];
    $proxies = $sql->table('proxies')->select()->where('status', 1)->orderBy('id', 'desc')->limit($postsCount)->get();
    $text = "با موفقیت ارسال شد";
    foreach ($proxies as $proxy) {
        $telegramApi->sendMessage($proxy['link'], null, CHANNEL_ID);
        $telegramApi->sendMessage($text);
    }
}
