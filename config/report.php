<?php

return [
    'pdf' => [
        'phantom' => [
            'debug' => false,
            'ignore-ssl-errors' => true,
            'load-images' => true,
            'ssl-protocol' => 'any'
        ],

        // 72 dpi (web) 595 X 842 pixels
        // 300 dpi (print) = 2480 X 3508 pixels
        // 600 dpi (print) = 4960 X 7016 pixels
        'viewport' => [
            'larger' => '3508',
            'smaller' => '2480'
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
    'image' => [
        'phantom' => [
            'debug' => false,
            'ignore-ssl-errors' => true,
            'load-images' => true,
            'ssl-protocol' => 'any'
        ],

        // 72 dpi (web) 595 X 842 pixels
        // 300 dpi (print) = 2480 X 3508 pixels
        // 600 dpi (print) = 4960 X 7016 pixels
        'viewport' => [
            'larger' => '842',
            'smaller' => '595'
        ],

        'page' => [
            'margin' => '{top: "20px", right: "20px", bottom: "20px", left: "20px"}',

            //Options = ['landscape', 'portrait']
            'orientation' => 'portrait',

            //Options = ['BMP', 'JPG', 'JPEG', 'PNG']
            'format' => 'JPG'
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
