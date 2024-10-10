<?php

namespace src\app\Trait;

use src\app\Classes\Guzzle;

require_once "../../core/initialize.php";

trait WebHook
{

    public function setWebHook($pathInHost)
    {
        $clinet = new Guzzle();
        $params = [
            'url' => DOMAIN . $pathInHost,
        ];
        $response = $clinet->request('setWebHook', $params);

        return $response;
    }
}
