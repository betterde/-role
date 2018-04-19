<?php

return [
    'model' => Betterde\Role\Models\Role::class,
    'table' => 'roles',
    'cache' => [
        'enable' => true,
        'prefix' => 'betterde',
        'ttl' => 60,
        'database' => 'cache'
    ]
];