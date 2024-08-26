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

function getIP($config) {
    $ip = cleanIP($_SERVER['REMOTE_ADDR']);

    // TODO
    /*if (isset($config->allowedSubnets) && !validProxySource($ip, $config->allowedSubnets)) {
        return $ip;
    }

    if (isset($config->ipHeader)) {
        foreach ($config->ipHeader as $header) {
            $parsedIP = '';

            switch ($header) {
                case 'CF-Connecting-IP':
                    $parsedIP = parseXForwardedForHeader($_SERVER['HTTP_CF_CONNECTING_IP']);
                    break;
                case 'True-Client-IP':
                    $parsedIP = parseXForwardedForHeader($_SERVER['HTTP_TRUE_CLIENT_IP']);
                    break;
                case 'X-Forwarded-For':
                    $parsedIP = parseXForwardedForHeader($_SERVER['HTTP_X_FORWARDED_FOR']);
                    break;
                case 'Forwarded':
                    $parsedIP = parseForwardedHeader($_SERVER['HTTP_FORWARDED']);
                    break;
                case 'X-Real-IP':
                    $parsedIP = parseXRealIPHeader($_SERVER['HTTP_X_REAL_IP']);
                    break;
            }

            if (!empty($parsedIP)) {
                return $parsedIP;
            }
        }
    }*/

    return $ip;
}

function cleanIP($ip) {
    if str_contains($ip, ':') {
        $parts = explode(':', $ip, 1);
        return $parts[0];
    }

    return $ip;
}

// TODO
/*function validProxySource($ip, $allowedSubnets) {
    ip := net.ParseIP(address)

    if ip == nil {
        return false
    }

    for _, from := range allowed {
        if from.Contains(ip) {
            return true
        }
    }

    return false;
}

function parseForwardedHeader($value) {
    $parts = explode(',', $value);

    if count($parts) > 0 {
        $parts = explode(';', substr($parts[count($parts)-1]));

        foreach ($parts as $part) {
            $kv = explode('=', $part);

            if count($kv) == 2 && trim($kv[0]) == 'for' {
                $ip = cleanIP($kv[1]);

                if (isValidIP($ip)) {
                    return $ip;
                }
            }
        }
    }

    return '';
}

function parseXForwardedForHeader($value) {
    $parts = explode(',', $value);

    if count($parts) > 0 {
        $ip = cleanIP(trim($parts[count($parts)-1]));

        if (isValidIP($ip)) {
            return $ip;
        }
    }

    return '';
}

func parseXForwardedForHeaderFirst(value string) string {
	parts := strings.Split(value, ",")

	if len(parts) > 1 {
		ip := parts[0]

		if strings.Contains(ip, ":") {
			host, _, err := net.SplitHostPort(ip)

			if err != nil {
				return ip
			}

			return strings.TrimSpace(host)
		}

		return strings.TrimSpace(ip)
	}

	return ""
}

function parseXRealIPHeader($value) {
    $ip = cleanIP(trim($value));

    if (isValidIP($ip)) {
        return $ip;
    }

    return '';
}

function isValidIP($value) {
	ip := net.ParseIP(value)
	return ip != nil &&
		!ip.IsPrivate() &&
		!ip.IsLoopback() &&
		!ip.IsUnspecified()
}*/
