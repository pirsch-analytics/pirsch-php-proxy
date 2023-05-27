<?php

return (object) array(
    'clients' => array(
        (object) array(
            //'id' => 'your-client-id',
            'secret' => 'your-client-secret or access-key'
        ),
        // (object) array(...)
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
    )*/
);
