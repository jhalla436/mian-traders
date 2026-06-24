<?php

return [
    'mode'                  => env('DOMPDF_MODE', 'utf-8'),
    'defines'               => [
        'DOMPDF_ENABLE_PHP'        => env('DOMPDF_ENABLE_PHP', false),
        'DOMPDF_ENABLE_JAVASCRIPT' => env('DOMPDF_ENABLE_JAVASCRIPT', true),
        'DOMPDF_ENABLE_REMOTE'     => env('DOMPDF_ENABLE_REMOTE', true),
        'DOMPDF_ENABLE_CSS_FLOAT'  => env('DOMPDF_ENABLE_CSS_FLOAT', true),
        'DOMPDF_DEFAULT_MEDIA_TYPE' => env('DOMPDF_DEFAULT_MEDIA_TYPE', 'screen'),
        'DOMPDF_DEFAULT_PAPER_SIZE' => env('DOMPDF_DEFAULT_PAPER_SIZE', 'a4'),
        'DOMPDF_DEFAULT_PAPER_ORIENTATION' => env('DOMPDF_DEFAULT_PAPER_ORIENTATION', 'portrait'),
        'DOMPDF_MARGIN_TOP'        => env('DOMPDF_MARGIN_TOP', 0),
        'DOMPDF_MARGIN_RIGHT'      => env('DOMPDF_MARGIN_RIGHT', 0),
        'DOMPDF_MARGIN_BOTTOM'     => env('DOMPDF_MARGIN_BOTTOM', 0),
        'DOMPDF_MARGIN_LEFT'       => env('DOMPDF_MARGIN_LEFT', 0),
        'DOMPDF_FONT_DIR'          => storage_path('fonts/'),
        'DOMPDF_FONT_CACHE'        => storage_path('fonts/'),
        'DOMPDF_TEMP_DIR'          => sys_get_temp_dir(),
        'DOMPDF_UNICODE_ENABLED'   => true,
    ],
    'convert_entities'      => true,
    'options'               => [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
        ],
    ],
];
