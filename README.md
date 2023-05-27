# Pirsch PHP Proxy

A self-hosted proxy for the Pirsch Analytics JavaScript snippets.

## Why should I use a proxy?

The benefit of using a proxy is that your website will only make first-party requests. The JavaScript snippets are hosted on your own server. Requests to pirsch.io will be proxyed through your server, preventing them from being blocked by ad blockers.

Additionally, you can create rollup views and send data to multiple dashboards with a single request from the client.

## Installation

Download the latest release archive from the releases section on GitHub and extract it to your server. Create an API client (or several) on the Pirsch dashboard and edit the config.php file to suit your needs.

```php
return (object) array(
    'clients' => array(
        (object) array(
            //'id' => 'your-client-id',
            'secret' => 'your-client-secret'
        )
        // add more clients here
    )
    // Optional list of allowed subnets (CIRD).
    /*'allowedSubnets' => array(
        '10.0.0.0/8'
    ),*/
    // Optional list of allowed headers to read the IP address.
    /*'ipHeader' => array(
        'CF-Connecting-IP',
        'True-Client-IP',
        'X-Forwarded-For',
        'Forwarded',
        'X-Real-IP'
    ),*/
);
```

`clients` takes a list of API clients. The ID is optional if you are using an access key instead of an oAuth client.

`ipHeader` is an optional list of headers to parse the IP address. Make sure you set the correct header if your proxy is behind a load balancer. Otherwise the remote address (`$_SERVER['REMOTE_ADDR']`) will be used.

`allowedSubnets` is an optional list of allowed subnets to parse the IP address.

The proxy will send all page views and events to all configured clients. So if you want to send statistics to two dashboards, you can add another client by appending it to the list.

```php
return (object) array(
    'clients' => array(
        (object) array(
            // client 1
        ),
        (object) array(
            // client 2
        )
    )
);
```

## Usage

Once you have installed the proxy on your server, you can add the Pirsch JavaScript snippet to your website.

Here is an example for the `pirsch.js` script with the default configuration.

```JavaScript
<script defer type="text/javascript"
        src="/p/p.js.php"
        id="pirschjs"
        data-endpoint="/p/pv.php"></script>
```

There are three other scripts available:

* `pirsch-events.js` as `e.js.php` using the endpoint `/p/e.php`
* `pirsch-sessions.js` as `s.js.php` using the endpoint `/p/s.php`
* `pirsch-extended.js` as `ext.js.php` using all of the other endpoints

Note that the extended scripts use different endpoint parameters. Namely `data-hit-endpoint`, `data-event-endpoint` and `data-session-endpoint`.

## Local development

The `config.php` takes a `baseURL` parameter to configure a local Pirsch mock implementation.

```php
return (object) array(
    'baseURL' => 'http://localhost:8080',
    // ...
);
```

## License

MIT
