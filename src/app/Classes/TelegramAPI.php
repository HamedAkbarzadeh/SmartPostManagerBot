<?php

namespace src\app\Classes;

use src\app\Trait\WebHook;

// use src\app\Trait\Log;

class TelegramAPI
{
    // use Log;
    use WebHook;
    public Guzzle $client;
    public $response;
    public $chat_id;
    public $callback_id;
    public $user_id;
    public $message_id;
    public $first_name;
    public $last_name;
    public $username;
    public $text;
    //media
    public $file_id;
    public $audio_id;
    public $video_id;
    public $animation_id;
    public $media_group_id;
    public $caption;
    public $file_size;
    public $file_type;

    public $is_bot;
    public $is_permium;
    public $entities;
    public $invite_link;
    public $caption_entities;

    public function __construct()
    {
        $this->client = new Guzzle;
        $this->response = json_decode(file_get_contents('php://input'), true);
        //$this->setLog($this->response, '../Log/Response.log', 'INFO');
        setManualLog(json_encode($this->response));

        if (array_key_exists('message', $this->response)) {
            $this->user_id = $this->response['message']['from']['id'] ?? null;
            $this->chat_id = $this->response['message']['chat']['id'] ?? null;
            $this->message_id = $this->response['message']['message_id'] ?? null;
            $this->first_name = $this->response['message']['from']['first_name'] ?? null;
            $this->last_name = $this->response['message']['from']['last_name'] ?? null;
            $this->username = $this->response['message']['from']['username'] ?? null;
            $this->text = $this->response['message']['text'] ?? null;
            $this->is_bot = $this->response['message']['from']['is_bot'] == true ? 1 : 0;
            $this->is_permium = $this->response['message']['from']['is_permium'] == true ? 1 : 0;
            $this->media_group_id = $this->response['message']['media_group_id'] ?? null;
            $this->entities = $this->response['message']['entities'] ?? null;
            //photo
            if (isset($this->response['message']['photo'])) {
                $this->file_id = end($this->response['message']['photo'])['file_id'] ?? null;
                $this->file_size = end($this->response['message']['photo'])['file_size'] ?? null;
                $this->file_type = "photo";
            } else {
                $this->file_id = null;
            }

            $this->file_id = $this->response['message']['audio']['file_id'] ?? null;
            $this->file_id = $this->response['message']['video']['file_id'] ?? null;
            $this->file_size = $this->response['message']['vdieo']['file_size'] ?? null;
            $this->file_type = $this->response['message']['vdieo']['mime_type'] ?? null;
            $this->file_id = $this->response['message']['animation']['file_id'] ?? null;
            $this->caption = $this->response['message']['caption'] ?? null;
            $this->caption_entities = $this->response['message']['caption_entities'] ?? null;
        } elseif (array_key_exists('callback_query', $this->response)) {
            $this->callback_id = $this->response['callback_query']['id'] ?? null;
            $this->user_id = $this->response['callback_query']['from']['id'] ?? null;
            $this->chat_id = $this->response['callback_query']['message']['chat']['id'] ?? null;
            $this->message_id = $this->response['callback_query']['message']['message_id'] ?? null;
            $this->first_name = $this->response['callback_query']['from']['first_name'] ?? null;
            $this->last_name = $this->response['callback_query']['from']['last_name'] ?? null;
            $this->username = $this->response['callback_query']['from']['username'] ?? null;
            $this->text = $this->response['callback_query']['data'] ?? null;
        }
        $this->invite_link = BOT_USERNAME . "?start=" . hash('md2', $this->getUser_id());
    }

    //////////////////////// GET FUNCTIONS //////////////////////////
    public function getUserID()
    {
        $sql = new DB;
        return $sql->table('users')->select(['id', 'user_id'])->where('user_id', $this->getUser_id())->first()['id'];
    }
    public function getUser_id()
    {
        return $this->user_id;
    }

    public function getEntities()
    {
        return $this->entities;
    }
    public function getChat_id()
    {
        return $this->chat_id;
    }
    public function getMessage_id()
    {
        return $this->message_id;
    }
    public function getCallback_id()
    {
        return $this->callback_id;
    }
    public function getFirst_name()
    {
        return $this->first_name;
    }
    public function getLast_name()
    {
        return $this->last_name;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getText()
    {
        return $this->text;
    }
    //media
    public function getFile_id()
    {
        return $this->file_id;
    }
    public function getAudio_id()
    {
        return $this->audio_id;
    }
    public function getVideo_id()
    {
        return $this->video_id;
    }
    public function getFile_type()
    {
        return $this->file_type;
    }
    public function getAnimation_id()
    {
        return $this->animation_id;
    }
    public function getMedia_group_id()
    {
        return $this->media_group_id;
    }
    public function getFile_size()
    {
        return $this->file_size;
    }
    public function getCaption()
    {
        return $this->caption;
    }
    public function getCaption_entities()
    {
        return $this->caption_entities;
    }

    public function getIs_bot()
    {
        return $this->is_bot;
    }
    public function getIs_permium()
    {
        return $this->is_permium;
    }
    public function getInvite_link()
    {
        return $this->invite_link;
    }
    /////////////////////// API METHODS FUNCTION ///////////////////////
    public function getUpdates()
    {
        return $this->response;
    }
    public function sendMessage($text, $reply_markup = null, $chat_id = null, $reply_to_message_id = null, $parse_mode = null)
    {
        if (!$chat_id) {
            $chat_id = $this->chat_id;
        }
        $params = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];

        if ($reply_to_message_id) {
            $params['reply_to_message_id'] = $reply_to_message_id;
        }
        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }
        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        $response = $this->client->request('sendMessage', $params);

        return $response;
    }
    public function editMessageReplyMarkup($reply_markup = null)
    {
        $params = [
            'chat_id' => $this->chat_id,
            'message_id' => $this->message_id,
        ];

        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }

        $response = $this->client->request('editMessageReplyMarkup', $params);

        return $response;
    }
    public function editMessageText($text, $reply_markup = null, $parse_mode = null)
    {
        $params = [
            'chat_id' => $this->chat_id,
            'text' => $text,
            'message_id' => $this->message_id,
        ];

        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }
        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        $response = $this->client->request('editMessageText', $params);

        return $response;
    }
    public function sendPhoto($photo, $caption = null, $reply_markup = null, $chat_id = null, $parse_mode = null)
    {
        if (isset($chat_id)) {
            $this->chat_id = $chat_id;
        }
        $params = [
            'chat_id' => $this->chat_id,
            'photo' => $photo,
        ];
        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        if ($caption) {
            $params['caption'] = $caption;
        }
        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }

        $response = $this->client->request('sendPhoto', $params);

        return $response;
    }
    public function sendVideo($video, $caption = null, $reply_markup = null, $chat_id = null, $parse_mode = null)
    {
        if (isset($chat_id)) {
            $this->chat_id = $chat_id;
        }
        $params = [
            'chat_id' => $this->chat_id,
            'video' => $video,
        ];
        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }
        if ($caption) {
            $params['caption'] = $caption;
        }
        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }

        $response = $this->client->request('sendVideo', $params);

        return $response;
    }
    public function sendMediaGroup($media, $reply_markup = null, $chat_id = null, $parse_mode = null)
    {
        if (isset($chat_id)) {
            $this->chat_id = $chat_id;
        }
        $params = [
            'chat_id' => $this->chat_id,
            'media' => json_encode($media),
        ];
        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }
        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }

        $response = $this->client->request('sendMediaGroup', $params);

        return $response;
    }
    public function sendChatAction($action)
    {
        $params = [
            'chat_id' => $this->chat_id,
            'action' => $action, // typing, upload_photo, record_video, upload_video, record_voice, find_location
        ]; // upload_voice, upload_document, upload_video_note, record_video_note,

        $response = $this->client->request('sendChatAction', $params);

        return $response;
    }
    public function sendAnimation($animation, $caption = null, $reply_markup = null, $chat_id = null, $parse_mode = null)
    {
        if (isset($chat_id)) {
            $this->chat_id = $chat_id;
        }
        $params = [
            'chat_id' => $this->chat_id,
            'animation' => $animation,
        ];
        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }
        if ($caption) {
            $params['caption'] = $caption;
        }
        if ($reply_markup) {
            $params['reply_markup'] = json_encode($reply_markup);
        }

        $response = $this->client->request('sendAnimation', $params);

        return $response;
    }
    public function getChatMember($chat_id, $user_id)
    {

        $params = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ];

        $response = $this->client->request('getChatMember', $params);

        if ($response['ok']) {
            return $response['result'];
        }

        return null; // اگر خطایی وجود داشته باشد، مقدار null بازگردانده می‌شود
    }
    public function setDefault()
    {
        $this->chat_id = null;
        $this->callback_id = null;
        $this->user_id = null;
        $this->message_id = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->username = null;
        $this->text = null;
        $this->file_id = null;
        $this->audio_id = null;
        $this->video_id = null;
        $this->animation_id = null;
        $this->caption = null;
    }
    public function __destruct()
    {
        $this->setDefault();
        unset($this->client);
    }
}
