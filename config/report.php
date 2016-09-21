<?php

return [
    'pdf' => [
        'phantom' => [
            'debug' => false,
            'ignore-ssl-errors' => true,
            'load-images' => true,
            'ssl-protocol' => 'any'
        ],

        'page' => [
            'margin' => '{top: "20px", right: "20px", bottom: "20px", left: "20px"}',

            //Options = ['landscape', 'portrait']
            'orientation' => 'portrait',

            //Options = ['A4', 'A3', 'Letter']
            'format' => 'A4'
        ],

        'footer' => [
            'height' => '25px',
        ],

        'header' => [
            'height' => '45px',
        ]
    ],
    'html' => [
    ]
];