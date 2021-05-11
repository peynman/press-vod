<?php

return [
    'product_typenames' => [
        'hls' => 'vod_hls',
        'link' => 'vod_link',
    ],

    'queue' => 'jobs',
    'hls_variants' => [
        264 => [426, 240],
        878 => [640, 360],
        1128 => [854, 480],
        2628 => [1280, 720],
    ],
];
