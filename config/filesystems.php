<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('uploads'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'audios' => [
            'driver' => 'local',
            'root' => storage_path('app/audios'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'wordcloud' => [
            'driver' => 'local',
            'root' => storage_path('app/wordcloud/files'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'hashtag' => [
            'driver' => 'local',
            'root' => storage_path('app/hashtag/files'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'planilhas' => [
            'driver' => 'local',
            'root' => public_path('planilhas'),
            'url' => env('APP_URL').'/planilhas',
            'visibility' => 'public',
        ],

        'impresso-img' => [
            'driver' => 'local',
            'root' => public_path('img/noticia-impressa'),
            'url' => env('APP_URL').'/impressos',
            'visibility' => 'public',
        ],

        'web-img' => [
            'driver' => 'local',
            'root' => public_path('img/noticia-web'),
            'url' => env('APP_URL').'/web',
            'visibility' => 'public',
        ],

        'radio-audio' => [
            'driver' => 'local',
            'root' => public_path('audio/noticia-radio'),
            'url' => env('APP_URL').'/radios',
            'visibility' => 'public',
        ],

        'tv-video' => [
            'driver' => 'local',
            'root' => public_path('video/noticia-tv'),
            'url' => env('APP_URL').'/tvs',
            'visibility' => 'public',
        ],

        'impresso-img-original' => [
            'driver' => 'local',
            'root' => public_path('img/impresso-img'),
            'url' => env('APP_URL').'/impressos',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL',''),
            'endpoint' => env('AWS_ENDPOINT',''),
            'suppress_php_deprecation_warning' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
