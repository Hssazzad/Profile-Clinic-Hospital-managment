<?php

return [
    'show_warnings' => false,
    'public_path'   => public_path(),

    // load fonts from public/fonts
    'font_dir'   => public_path('fonts'),
    'font_cache' => storage_path('fonts'),

    // default font family name (MUST match key below)
    'default_font' => 'notobengali',

    'options' => [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled'      => true,
        'dpi'                  => 96,
        'defaultPaperSize'     => 'a4',
    ],

    // register Bangla font
    'font_data' => [
        'notobengali' => [
            'R'  => 'NotoSansBengali_Condensed-Bold.ttf',
            'B'  => 'NotoSansBengali_Condensed-Bold.ttf',
            'I'  => 'NotoSansBengali_Condensed-Bold.ttf',
            'BI' => 'NotoSansBengali_Condensed-Bold.ttf',
        ],
    ],
];
