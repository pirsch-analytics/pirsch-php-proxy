# Pirsch PHP Proxy

A self-hosted proxy for the Pirsch Analytics JavaScript snippets.

## Why should I use a proxy?

The benefit of using a proxy is that your website will only make first-party requests. The JavaScript snippets are hosted on your own server. Requests to pirsch.io will be proxyed through your server, preventing them from being blocked by ad blockers.

Additionally, you can create rollup views and send data to multiple dashboards with a single request from the client.

## Installation

Download the latest release archive from the releases section on GitHub and extract it to your server. Create an API client (or several) on the Pirsch dashboard and edit the config.php file to suit your needs.

```php
return (object) array(
    'ipHeader' => array('X-Forwarded-For', 'CF-Connecting-IP', 'TRUE-CLIENT-IP', 'X-REAL-IP'), // optional lists of header to parse the visitor's IP address
    'clients' => array(
        (object) array(
            //'id' => 'your-client-id',
            'secret' => 'your-client-secret'
        )
        // add more clients here
    )
);
```

`clients` takes a list of API clients. The ID is optional if you are using an access key instead of an oAuth client.

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

Here is an example with the default configuration.

```JavaScript
<script defer type="text/javascript"
    src="/p/p.js.php"
    id="pianjs"
    data-hit-endpoint="/p/pv.php"
    data-event-endpoint="/p/e.php"
    data-session-endpoint="/p/s.php"></script>
```

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
