<?php

return [
    'notifications' => [
        'to' => env('UPTIME_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS')),
    ],
];
