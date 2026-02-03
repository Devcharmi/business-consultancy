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
            'client',
            'objective',
            'expertise',
            'created_by',
            'status',
        ],

        'consulting.index' => [
            'daterange',
            'client',
            'objective',
            'expertise',
            'focus_area',
        ],

    ],

];
