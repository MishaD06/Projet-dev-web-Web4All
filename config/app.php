<?php

return [
    'name'         => 'StageLab',
    'url'          => $_ENV['APP_URL']  ?? 'http://stagelab.local',
    'env'          => $_ENV['APP_ENV']  ?? 'production',
    'debug'        => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',

    // Uploads (hors public/)
    'upload_dir'   => dirname(__DIR__) . '/storage/uploads/',
    'upload_url'   => '/uploads/',        // route vers download sécurisé
    'upload_max_mb'=> 5,

    // Session
    'session_name' => 'stagelab_sess',

    // Pagination
    'per_page'     => 9,
];
