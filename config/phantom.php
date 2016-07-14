<?php
return [
    'defaultOptions' => [
        'debug' => false,
        'ignore-ssl-errors' => true,
        'load-images' => true,
        'ssl-protocol' => 'any'
    ],

    // URL: http://phantomjs.org/api/command-line.html
    'validOptions' => [
        'debug' => 'bool',
        'cookies-file' => 'string',
        'disk-cache' => 'bool',
        'load-images' => 'bool',
        'local-storage-path' => 'string',
        'local-storage-quota' => 'integer',
        'local-to-remote-url-access' => 'bool',
        'max-disk-cache-size' => 'integer', //in KB
        'output-encoding' => 'string',
        'proxy' => 'string', //192.168.1.42:8080
        'proxy-type' => ['http', 'socks5', 'none'],
        'proxy-auth' => 'string', //username:password
        'script-encoding' => 'script',
        'ssl-protocol' => [ 'sslv3', 'sslv2', 'tlsv1', 'any'],
        'ssl-certificates-path' => 'string',
        'web-security' => 'bool',
        'webdriver' => 'string',
        'webdriver-selenium-grid-hub' => 'string'
    ]
];