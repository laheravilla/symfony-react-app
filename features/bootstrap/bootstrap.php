<?php

use Symfony\Component\Dotenv\Dotenv;

// The check is to ensure we don't use .env in production
if (!isset($_SERVER["APP_ENV"])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException("APP_ENV environment variable is not defined. You need to define it");
    }
    (new Dotenv())->load(__DIR__."/../../.env.test");
}