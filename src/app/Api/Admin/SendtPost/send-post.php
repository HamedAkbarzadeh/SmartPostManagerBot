<?php
if (strpos($telegramApi->getText(), 'ارسال پست') === 0) {
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["send-post-normal"]);

    $postsCount = explode(' ', $telegramApi->getText())[2];
    if (!is_numeric($postsCount)) {
        exit();
    }
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

// send custom message section //

//1) send message
if (strpos($telegramApi->getText(), "send custom post") === 0) {
    $proxyCount = explode(' ', $userStep)[3];

    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["send-post-custom-$proxyCount"]);
    $text = "پست خود را ارسال نمایید (شامل عکس یا ویدیو و...)";
    $telegramApi->sendMessage($text);
}

//2) send message to channel
if (strpos($user['step'], "send-post-custom-") === 0) {

    //get proxy from db
    $proxyCount = explode('-', $userStep)[3];
    $proxies = $sql->table('proxies')->select()->where('status', 0)->orderBy('id')->limit($proxyCount)->get();
    if (count($proxies) > 0) {
        $strProxies = "";
        foreach ($proxies as $key => $value) {
            $link = $value['link'];
            $linkNumber = $key + 1;
            $strProxies .= "<a href='$link'>پروکسی $linkNumber </a>";
        }
    }

    if ($telegramApi->getCaption() != null) {
        $text = $telegramApi->getCaption();
    } elseif ($telegramApi->getText() != null) {
        $text = $telegramApi->getText();
    }

    $media_group_id = $telegramApi->getMedia_group_id();
    if (isset($media_group_id)) {

    } else {
        $file_type = $telegramApi->getFile_type();
        $file_id = $telegramApi->getFile_id();

        $channelLink = "🆔 @PHarseProxy 🫧";
        $textMessage = "$text\n\n$strProxies\n\n$channelLink";

        switch ($file_type) {
            case 'photo':
                $response = $telegramApi->sendPhoto($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            case 'video':
                $response = $telegramApi->sendVideo($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            case 'message':
                $response = $telegramApi->sendMessage($textMessage, null, CHANNEL_ID, null, "HTML");
                break;
            case 'animation':
                $response = $telegramApi->sendAnimation($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            default:
                $response = $telegramApi->sendMessage($textMessage, null, CHANNEL_ID, null, "HTML");
                break;
        }
    }
    $txt = "پست شما با موفقیت با تعداد $proxyCount پروکسی ارسال شد .";
    $telegramApi->sendMessage($textMessage);

}
