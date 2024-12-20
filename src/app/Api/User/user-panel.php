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
        $text = "در این بخش هم می  نمایید و هم می را برای  به کانال انتخاب نمایید .";
        $reply_markup = [
            'keyboard' => [
                [
                    [
                        'text' => 'send forward media',
                    ]  
                ],
                [
                    [
                        'text' => 'send custom post 1',
                    ],
                    [
                        'text' => 'send custom post 3',
                    ],
                ],
                [
                    [
                        'text' => 'send custom post 6',
                    ],
                    [
                        'text' => 'send custom post 8',
                    ],
                ],
                [
                    ['text' => 'ارسال پست 1'],
                    ['text' => 'ارسال پست 2'],
                ],
                [
                    ['text' => 'ارسال پست 3'],
                    ['text' => 'ارسال پست 5'],
                ],
            ],
        ];
    } else {
        $text = "به ربات " . BOT_NAME . " خوش آمدید . جهت ورود به کانال به لینک زیر مراجعه نمایید" . PHP_EOL . CHANNEL_USERNAME;
    }

    $telegramApi->sendMessage($text, $reply_markup);
}