<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Data Dump
    |--------------------------------------------------------------------------
    |
    | Configures how data:dump works.
    |
    */

    'dump' => [
        'path' => storage_path('app/private/export'),
        'exports' => [
            [
                'format' => 'csv',
                'type'   => 'simple',
            ],
            [
                'format' => 'json',
                'type'   => 'full',
            ],
            [
                'format' => 'json',
                'type'   => 'simple',
            ],
            [
                'format' => 'yaml',
                'type'   => 'full',
            ],
            [
                'format' => 'yaml',
                'type'   => 'simple',
            ],
        ],
    ],

];
