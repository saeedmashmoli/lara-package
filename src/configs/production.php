<?php

return [
    /*
    |-----------------------------------------------------------------------------------------------
    | destination server
    |-----------------------------------------------------------------------------------------------
    |
    | Production server name and path of project
    |
    */
    'destination' => [
        'user' => 'oxylopsa',
        'name' => 'server2.filecadeh.com',
        'path' => '/home/oxylopsa/domains/my.oxyplug.com',
    ],

    /*
    |-----------------------------------------------------------------------------------------------
    | encoder server
    |-----------------------------------------------------------------------------------------------
    |
    | Encode server name and path.
    | Driver could be: ioncube, sourceguardian
    |
    */
    'encoder' => [
        'driver' => 'ioncube',
        'name' => 'gitserver.itbazar.com',
        'path' => '/opt/ioncube-encoder5-basic-11.0',
    ],

    /*
    |-----------------------------------------------------------------------------------------------
    | intermediate server
    |-----------------------------------------------------------------------------------------------
    |
    | Intermediate server name and path. Build and all jobs will do by this server
    |
    */
    'intermediate' => [
        'name' => 'server2.filecadeh.com',
        'path' => '',
    ],

    /*
    |-----------------------------------------------------------------------------------------------
    | backup server
    |-----------------------------------------------------------------------------------------------
    |
    | Backup server name and path.
    |
    */
    'backup' => [
        'name' => 'server2.filecadeh.com',
        'path' => '/home/admin/admin_backups/oxy-server-versions',
    ],

    /*
    |-----------------------------------------------------------------------------------------------
    | timeout
    |-----------------------------------------------------------------------------------------------
    |
    | Timeout in seconds.
    |
    */
    'timeout' => 600,

    /*
    |-----------------------------------------------------------------------------------------------
    | front
    |-----------------------------------------------------------------------------------------------
    |
    | front: mix, quasar
    |
    */
    'front' => 'quasar',

];
