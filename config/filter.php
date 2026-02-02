<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route-wise Filter Visibility
    |--------------------------------------------------------------------------
    | Control which filters appear on which routes
    */

    'route_filters' => [
        'dashboard' => ['daterange'],
        'user-task.index' => [
            'daterange',
            'created_by',
            'staff',
            'client',
            'status',
            'priority',
        ],

        'task.index' => [
            'daterange',
            'created_by',
            'staff',
            'client',
            'status',
            'priority',
        ],

        'consulting.index' => [
            'daterange',
            'created_by',
            'staff',
            'client',
            'objective',
            'status',
            'expertise',
            'focus_area',
            // priority intentionally hidden
        ],

    ],

];
