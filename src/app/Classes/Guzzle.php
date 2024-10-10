<?php

namespace src\app\Classes;

require __DIR__ . "../../../core/initialize.php";

use GuzzleHttp\Client;

class Guzzle
{
    public $client;
    public function __construct()
    {
        $this->client = new Client();
    }

    public function request($method, array $params = null, $requestMethod = 'get')
    {
        $response = $this->client->$requestMethod(API . $method, [
            'form_params' => $params
        ]);
        $callback = json_decode($response->getBody()->getContents() , true);
        if ($response->getStatusCode() == 200) {
            return [
                'callback' => $callback,
                'message' => "Webhook has been set successfully!",
                'status' => 200
            ];
        } else {
            return [
                'callback' => $callback,
                'message' => "Failed to set Webhook!",
                'status' => $response->getStatusCode()
            ];
        }
    }
}