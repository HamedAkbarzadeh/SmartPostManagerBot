<?php
if (strpos($telegramApi->getText(), 'sendPost_') === 0) {
    $postsCount = explode('_', $telegramApi->getText())[1];
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
        $text = "تعداد " . count($proxies) . " عدد پست ارسال شد";

        $telegramApi->sendMessage($text, null, null, null, "MarkdownV2");
    } else {
        $text = "هیچ پست فعالی برای ارسال موجود نیست .";
        $telegramApi->sendMessage($text);
    }
}