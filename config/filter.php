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
            'entities',
            'types',
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

        'reports.clients' => [
            'daterange',
            'created_by',
            'client',
        ],

        'reports.objectives' => [
            'daterange',
            'created_by',
            'client',
            'objective',
        ],

        'reports.consultings' => [
            'daterange',
            'client',
            'objective',
            'expertise',
        ],

        'reports.leads' => [
            'daterange',
            'client',
            'created_by',
        ],
    ],

];
