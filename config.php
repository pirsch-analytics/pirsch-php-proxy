<?php

return (object) array(
    //'ipHeader' => array(),
    'clients' => array(
        (object) array(
            //'id' => 'your-client-id',
            'secret' => 'your-client-secret or access-key'
        ),
        // (object) array(...)
    ),
    'allowedOrigins' => array(
        '*'
        // or 'https://your-domain.com' for example
    )
);
