<?php
require_once __DIR__.'/../vendor/autoload.php';

function sendHit() {
    try {
        $config = include('../config.php');
        $data = new Pirsch\HitOptions;
        $data->url = $_GET['url'];
        $data->title = $_GET['t'];
        $data->referrer = $_GET['ref'];
        $data->screen_width = $_GET['w'];
        $data->screen_height = $_GET['h'];
        $baseURL = property_exists($config, 'baseURL') ? $config->baseURL : Pirsch\Client::DEFAULT_BASE_URL;

        foreach ($config->clients as $client) {
            $client = new Pirsch\Client($client->id, $client->secret, $baseURL);
            $client->pageview($data);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log($e->getMessage());
    }
}

function sendEvent() {
    try {
        $config = include('../config.php');
        $body = json_decode(file_get_contents('php://input'), true);
        $data = new Pirsch\HitOptions;
        $data->url = $body['url'];
        $data->title = $body['title'];
        $data->referrer = $body['referrer'];
        $data->screen_width = $body['screen_width'];
        $data->screen_height = $body['screen_height'];
        $eventName = $body['event_name'];
        $eventDuration = $body['event_duration'];
        $eventMeta = $body['event_meta'];
        $baseURL = property_exists($config, 'baseURL') ? $config->baseURL : Pirsch\Client::DEFAULT_BASE_URL;

        foreach ($config->clients as $client) {
            $client = new Pirsch\Client($client->id, $client->secret, $baseURL);
            $client->event($eventName, $eventDuration, $eventMeta, $data);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log($e->getMessage());
    }
}

function extendSession() {
    try {
        $config = include('../config.php');
        $baseURL = property_exists($config, 'baseURL') ? $config->baseURL : Pirsch\Client::DEFAULT_BASE_URL;

        foreach ($config->clients as $client) {
            $client = new Pirsch\Client($client->id, $client->secret, $baseURL);
            $client->session();
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log($e->getMessage());
    }
}
