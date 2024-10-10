<?php
if (strpos($telegramApi->getText(), 'sendPost_') === 0) {
    $postsCount = explode('_', $telegramApi->getText())[1];
    $proxies = $sql->table('proxies')->select()->where('status', 0)->orderBy('id')->limit($postsCount)->get();

    if (count($proxies) > 0) {

        foreach ($proxies as $proxy) {

            $text = "💯  پروکسی پر سرعت و ضد فیلتر  ♨️
            " . $proxy["link"] . "
            
            ✅  لطفا پروکسی هارو برای دوستاتون ارسال کنید تا از پروکسی های رایگان و پرسرعت ما استفاده کنند.
            ✳️  ری اکشن یادتون نره
            
            🆔 @PHarseProxy 🫧";
            $telegramApi->sendMessage($text, null, CHANNEL_ID, null);
           
            $timestmp = time();
            $sql->table('proxies')->where('id', $proxy['id'])->update(['status', 'used_at'], [1, date("Y-m-d H:i:s", $timestmp)]);
        }
        $text = "تعداد ". count($proxies) ." ارسال شد";

        $telegramApi->sendMessage($text, null, null, null, "MarkdownV2");

    } else {
        $text = "هیچ پست فعالی برای ارسال موجود نیست .";
        $telegramApi->sendMessage($text);
    }
}