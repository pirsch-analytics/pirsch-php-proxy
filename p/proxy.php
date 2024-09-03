<?php
require_once __DIR__.'/../vendor/autoload.php';

function sendHit() {
    try {
        $config = include('../config.php');
        $data = new Pirsch\HitOptions;
        $data->ip = getIP($config);
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
        $data->ip = getIP($config);
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
        $data = new Pirsch\HitOptions;
        $data->ip = getIP($config);
        $baseURL = property_exists($config, 'baseURL') ? $config->baseURL : Pirsch\Client::DEFAULT_BASE_URL;

        foreach ($config->clients as $client) {
            $client = new Pirsch\Client($client->id, $client->secret, $baseURL);
            $client->session($data);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log($e->getMessage());
    }
}

function getIP($config) {
    if (isset($config->ipHeader)) {
        foreach ($config->ipHeader as $header) {
            $parsedIP = '';

            switch (strtolower($header)) {
                case 'cf-connecting-ip':
                    $parsedIP = parseXForwardedForHeader($_SERVER['HTTP_CF_CONNECTING_IP']);
                    break;
                case 'true-client-ip':
                    $parsedIP = parseXForwardedForHeader($_SERVER['HTTP_TRUE_CLIENT_IP']);
                    break;
                case 'x-forwarded-for':
                    $parsedIP = parseXForwardedForHeader($_SERVER['HTTP_X_FORWARDED_FOR']);
                    break;
                case 'x-real-ip':
                    $parsedIP = parseXRealIPHeader($_SERVER['HTTP_X_REAL_IP']);
                    break;
            }

            if (!empty($parsedIP)) {
                return $parsedIP;
            }
        }
    }

    return cleanIP($_SERVER['REMOTE_ADDR']);
}

function parseXForwardedForHeader($value) {
    if (!isset($value)) {
        return '';
    }

    $parts = explode(',', $value);

    if (count($parts) > 0) {
        return cleanIP(trim($parts[0]));
    }

    return '';
}

function parseXRealIPHeader($value) {
    if (!isset($value)) {
        return '';
    }

    return cleanIP(trim($value));
}

function cleanIP($ip) {
    if (str_contains($ip, ':')) {
        $parts = explode(':', $ip, 1);
        return $parts[0];
    }

    return $ip;
}
