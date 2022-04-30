# Pirsch PHP Proxy

A self-hosted proxy for the Pirsch Analytics JavaScript snippet.

## Why should I use a proxy?

The benefit of using a proxy is that your website will only make first-party requests. The JavaScript snippets are hosted on your own server. Requests to pirsch.io will be proxied through your server, preventing them from being blocked by ad blockers.

Additionally, you can create rollup views and send data to multiple dashboards with a single request on the client.

## Installation

Download the latest release archive from the release section on GitHub and extract it onto your server. Adjust the `pirsch/config.php` file to your needs.

```php
<?php

return (object) array(
    'clients' => array(
        (object) array(
            'id' => 'your-client-id',
            'secret' => 'your-client-secret',
            'hostname' => 'example.com'
        )
        // add more clients here
    )
);
```

`clients` takes a list of API clients. You can create a new client ID and secret on the Pirsch dashboard on the developer settings page. The hostname needs to match the hostname you have configured on the dashboard.

The proxy will send all page views and events to all clients configured. So, if you would like to send the statistics to two dashboards, you can add another client by appending it to the list.

```php
<?php

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

Clients can also be used across websites to create rollup views. To do this, deploy as many proxies as you need and configure them using the same client ID and secret.

## Usage

After you have installed the proxy on your server, you can add the Pirsch JavaScript snippet to your website. In the examples below, the proxy is placed on your server in the `pirsch` directory. If you have chosen a different directory, or you don't use Apache, you might need to adjust the scripts with custom paths.

**pirsch.min.js**

This will track page views.

```JavaScript
<script defer type="text/javascript" src="/pirsch/pirsch.min.js" id="pirschjs"></script>
```

**pirsch-events.min.js**

This will make the `pirsch` event function available on your site.

```JavaScript
<script defer type="text/javascript" src="/pirsch/pirsch-events.min.js" id="pirscheventsjs"></script>
```

If you have placed the proxy in a different directory, adjust the `src`, `hit.php`, and `event.php` locations using the `data-endpoint` parameters.

```JavaScript
<script defer type="text/javascript"
    src="/custom/path/pirsch.min.js"
    id="pirschjs"
    data-endpoint="/custom/path/hit.php"></script>

<script defer type="text/javascript"
    src="/custom/path/pirsch-events.min.js"
    id="pirscheventsjs"
    data-endpoint="/custom/path/event.php"></script>
```

A demo can be found in the [demo](demo) directory.

## Local development

The `config.php` takes a `baseURL` parameter to configure a local Pirsch mock implementation.

```php
<?php

return (object) array(
    'baseURL' => 'http://localhost:8080',
    // ...
);
```

## License

MIT
