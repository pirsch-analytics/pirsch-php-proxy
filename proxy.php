<?php
require_once __DIR__.'/vendor/autoload.php';

function sendHit() {
    try {
        $config = include('config.php');
        $data = new Pirsch\HitOptions;
        $data->url = $_GET['url'];
        $data->title = $_GET['t'];
        $data->referrer = $_GET['ref'];
        $data->screen_width = $_GET['w'];
        $data->screen_height = $_GET['h'];
        $baseURL = property_exists($config, 'baseURL') ? $config->baseURL : Pirsch\Client::DEFAULT_BASE_URL;

        error_log($baseURL);

        foreach($config->clients as $client) {
            $client = new Pirsch\Client($client->id, $client->secret, $client->hostname, $baseURL);
            $client->pageview($data);
        }
    } catch(Exception $e) {
        http_response_code(500);
        error_log($e->getMessage());
    }
}

function sendEvent() {
    // TODO
}
