<?php

return [
    'simple' => 'ok',
    'array' => [
        'array_key' => 'array_value',
    ],
    'enum' => \IBroStudio\NeonConfig\Tests\Support\FakeEnum::TEST,
    'collection' => [
        \IBroStudio\NeonConfig\Tests\Support\FakeEnum::TEST,
        \IBroStudio\NeonConfig\Tests\Support\FakeEnum::TEST2,
        \IBroStudio\NeonConfig\Tests\Support\FakeEnum::TEST3,
    ],
    'name' => config('app.name'),
    'url' => config('unknow', 'https://laravel.com'),
    'environment' => env('APP_ENV'),
    'key' => env('UNKNOWN', default: 1234567890),
];
