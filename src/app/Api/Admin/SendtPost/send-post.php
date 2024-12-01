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
    $proxyCount = explode(' ', $telegramApi->getText())[3] ?? 0;

    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["send-post-custom-$proxyCount"]);
    $text = "پست خود را ارسال نمایید (شامل عکس یا ویدیو و...)";
    $reply_markup = [
        'inline_keyboard' => [
            [
                [
                    'text' => 'کنسل کردن',
                    'callback_data' => 'cancel_send_post'
                ]
            ]
        ]
    ];
    $telegramApi->sendMessage($text, $reply_markup);
}

//2) send message to channel
if (strpos($user['step'], "send-post-custom-") === 0) {
    checkCencel();
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["sended-post-custom-$proxyCount-to-channel"]);

    //get proxy from db
    $proxyCount = explode('-', $userStep)[3];
    $proxies = $sql->table('proxies')->select()->where('status', 0)->orderBy('id')->limit($proxyCount)->get();
    if (count($proxies) > 0) {
        $strProxies = "";
        foreach ($proxies as $key => $value) {
            $link = $value['link'];
            $linkNumber = $key + 1;
            $strProxies .= "<a href='$link'>پروکسی $linkNumber </a>";
            $sql->table('proxies')->where('id', $value['id'])->delete();
        }
    }
    //send media
    if ($telegramApi->getCaption() != null) {
        $text = $telegramApi->getCaption();
    } elseif ($telegramApi->getText() != null) {
        $text = $telegramApi->getText();
    }

    $media_group_id = $telegramApi->getMedia_group_id();
    if (isset($media_group_id)) {
    } else {

        $file_type = explode('/', $telegramApi->getFile_type())[0];
        $file_id = $telegramApi->getFile_id();

        $channelLink = "🆔 @PHarseProxy 🫧";
        $textMessage = "$text\n\n$strProxies\n\n$channelLink";
        $robotChatID = $telegramApi->getChat_id();
        switch ($file_type) {
            case 'photo':
                $response = $telegramApi->sendPhoto($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            case 'video':
                $response = $telegramApi->sendVideo($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            case 'audio':
                $response = $telegramApi->sendAudio($file_id, $textMessage, null, CHANNEL_ID, "HTML");
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
    $textForRobot = "پست شما با موفقیت با تعداد $proxyCount پروکسی ارسال شد ." . PHP_EOL . PHP_EOL . "تعداد پروکسی آماده ارسال : " . $sql->table('proxies')->select('COUNT(*)')->where('status', 0)->get()[0]['COUNT(*)'];
    $telegramApi->sendMessage($textForRobot, null, $robotChatID, null, null);
}

//* send forward media *//
if ($telegramApi->getText() == "send forward media") {
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["choose_proxy_count_send_forward_media"]);
    $reply_markup = [
        'inline_keyboard' => [
            [
                [
                    'text' => "0",
                    'callback_data' => "proxies_count_for_send_forward_media_0"
                ],
                [
                    'text' => "1",
                    'callback_data' => "proxies_count_for_send_forward_media_1"
                ],
            ],
            [
                [
                    'text' => "3",
                    'callback_data' => "proxies_count_for_send_forward_media_3"
                ],
                [
                    'text' => "6",
                    'callback_data' => "proxies_count_for_send_forward_media_6"
                ],
            ],
            [
                [
                    'text' => "کنسل کردن",
                    'callback_data' => 'cancel_send_post'
                ]
            ]
        ]
    ];

    $text = "تعداد پروکسی هایی که میخواهید همراه پست ارسال شود را ارسال نمایید .";
    $telegramApi->sendMessage($text, $reply_markup);
}
if (strpos($telegramApi->getText(), "proxies_count_for_send_forward_media_") === 0) {
    $proxyCount = end(explode("_", $telegramApi->getText()));
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["send_forward_media_with_proxies_$proxyCount"]);

    $text = "لطفا فایل خود را که میخواهید فوروارد کنید رو ارسال کنید .";
    $reply_markup = [
        'inline_keyboard' => [
            [
                [
                    'text' => "کنسل کردن",
                    'callback_data' => 'cancel_send_post'
                ]
            ]
        ]
    ];
    $telegramApi->sendMessage($text, $reply_markup);
}

if (strpos($userStep, "send_forward_media_with_proxies_") === 0) {
    checkCencel();
    $proxyCount = end(explode("_", $userStep));
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["choose_caption_or_no_for_send_forward_media_proxy_count_$proxyCount"]);

    $text = "اگر میخواهید کپشن اضافه کنید لطفا کپشن مورد نظر خودتون رو ارسال نمایید . در غیر این صورت یکی از دکمه های زیر را انتخاب نمایید .";
    $reply_markup = [
        "inline_keyboard" => [
            [
                [
                    'text' => 'بدون کپشن',
                    'callback_data' => "send_without_caption",
                ],
                [
                    'text' => 'cancel',
                    'callback_data' => 'cancel_send_post'
                ]
            ]
        ]
    ];
    $telegramApi->sendMessage($text, $reply_markup);
}

if (strpos($userStep, 'choose_caption_or_no_for_send_forward_media_proxy_count_') === 0) {
    checkCencel();
    $proxyCount = end(explode('_', $userStep));
    if ($telegramApi->getText() == "send_without_caption") {

        sendMediaWithProxies(null, $proxyCount);
    } else {

        sendMediaWithProxies($telegramApi->getText(), $proxyCount);
    }
}



function sendMediaWithProxies($caption = null, $proxyCount = 0)
{
    global $telegramApi;
    global $sql;




    //get proxy from db
    $proxies = $sql->table('proxies')->select()->where('status', 0)->orderBy('id')->limit($proxyCount)->get();
    if (count($proxies) > 0) {
        $strProxies = "";
        foreach ($proxies as $key => $value) {
            $link = $value['link'];
            $linkNumber = $key + 1;
            $strProxies .= "<a href='$link'>پروکسی $linkNumber </a>";
            $sql->table('proxies')->where('id', $value['id'])->delete();
        }
    }

    //send media
    if ($caption != null) {
        $text = $caption;
    } else {
        $text = "";
    }

    $media_group_id = $telegramApi->getMedia_group_id();
    if (isset($media_group_id)) {
    } else {

        $file_type = explode('/', $telegramApi->getFile_type())[0];
        $file_id = $telegramApi->getFile_id();

        $channelLink = "🆔 @PHarseProxy 🫧";
        $textMessage = "$text\n\n$strProxies\n\n$channelLink";
        $robotChatID = $telegramApi->getChat_id();
        switch ($file_type) {
            case 'photo':
                $response = $telegramApi->sendPhoto($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            case 'video':
                $response = $telegramApi->sendVideo($file_id, $textMessage, null, CHANNEL_ID, "HTML");
                break;
            case 'audio':
                $response = $telegramApi->sendAudio($file_id, $textMessage, null, CHANNEL_ID, "HTML");
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
    $textForRobot = "پست شما با موفقیت با تعداد $proxyCount پروکسی ارسال شد ." . PHP_EOL . PHP_EOL . "تعداد پروکسی آماده ارسال : " . $sql->table('proxies')->select('COUNT(*)')->where('status', 0)->get()[0]['COUNT(*)'];
    $telegramApi->sendMessage($textForRobot, null, $robotChatID, null, null);
}

function checkCencel()
{
    global $telegramApi;
    global $sql;
    if ($telegramApi->getText() == "cancel_send_post") {
        $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ["cancel_send_post"]);
        exit(22);
    }
}
