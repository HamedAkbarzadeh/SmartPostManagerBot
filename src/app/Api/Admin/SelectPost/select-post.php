<?php
if (strpos($telegramApi->getText(), 'ارسال پست') === 0) {
    $postsCount = explode(' ', $telegramApi->getText())[2];
    $proxies = $sql->table('proxies')->select()->where('status', 0)->orderBy('id')->limit($postsCount)->get();

    if (count($proxies) > 0) {

        foreach ($proxies as $proxy) {
            $title = "💯  پروکسی پر سرعت و ضد فیلتر  ♨️";
            $footer = "✅  لطفا پروکسی هارو برای دوستاتون ارسال کنید تا از پروکسی های رایگان و پرسرعت ما استفاده کنند .";
            $footer2 = "✳️  ری اکشن یادتون نره ";
            $channelLink = "🆔 @PHarseProxy 🫧";
            $link = $proxy["link"];
            $text = "<b>$title</b>\n\n$link\n\n$footer\n$footer2\n\n$channelLink";
            $telegramApi->sendMessage($text, null, CHANNEL_ID, null, "HTML");

            $timestmp = time();
            $sql->table('proxies')->where('id', $proxy['id'])->delete();
        }
        $text = "تعداد " . count($proxies) . " عدد پست ارسال شد \n تعداد پست های آماده ارسال   : " . $sql->table('proxies')->select('COUNT(*)')->where('status', 0)->get()[0]['COUNT(*)'];

        $telegramApi->sendMessage($text, null, null, null, "MarkdownV2");
    } else {
        $text = "هیچ پست فعالی برای ارسال موجود نیست .";
        $telegramApi->sendMessage($text);
    }
}
