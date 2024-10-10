<?php

if (strpos($telegramApi->getText(), '/start') === 0) {
    $sql->table('users')->where('user_id', $telegramApi->getUser_id())->update(['step'], ['home']);


    if (!$user) {

        $userID = $sql->table('users')->insert(
            [
                'user_id',
                'first_name',
                'last_name',
                'username',
                'is_bot',
                'is_permium',
                'step',
            ],
            [
                $telegramApi->getUser_id(),
                $telegramApi->getFirst_name(),
                $telegramApi->getLast_name(),
                $telegramApi->getUsername(),
                $telegramApi->getIs_bot(),
                $telegramApi->getIs_permium(),
                'home',

            ]
        );
        $user = $sql->table('users')->select()->where('id', $userID)->first();
    }
    $reply_markup = null;
    if ($user['is_admin'] == 1) {
        $text = "در این بخش هم میتوانید پستی ارسال نمایید و هم میتوانید پستی را برای ارسال به کانال انتخاب نمایید .";
        $reply_markup = [
            'inline_keyboard' => [
                [
                    [
                        'text' => 'ارسال 1 پست',
                        'callback_data' => 'sendPost_1',
                    ],
                    [
                        'text' => 'ارسال 2 پست',
                        'callback_data' => 'sendPost_2',
                    ]
                ],
                [
                    [
                        'text' => 'ارسال 3 پست',
                        'callback_data' => 'sendPost_3',
                    ],
                    [
                        'text' => 'ارسال 5 پست',
                        'callback_data' => 'sendPost_5',
                    ]
                ]
            ]
        ];
    } else {
        $text = "به ربات " . BOT_NAME . " خوش آمدید . جهت ورود به کانال به لینک زیر مراجعه نمایید" . PHP_EOL . CHANNEL_USERNAME;
    }

    $telegramApi->sendMessage($text, $reply_markup);
}
