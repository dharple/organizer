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
                'format' => 'yaml',
                'type'   => 'simple',
            ],
            [
                'format' => 'yaml',
                'type'   => 'full',
            ],
        ],
    ],

];
