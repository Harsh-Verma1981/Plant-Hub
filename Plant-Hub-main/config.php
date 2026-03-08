<?php
// config.php

// This must be the first thing (or very early) in files that need env vars
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Create the Dotenv instance
$dotenv = Dotenv::createImmutable(__DIR__);   // ← correct: Dotenv:: not Dotenv\Dotenv::

// Load the .env file
$dotenv->load();          // throws exception if .env is missing → good for dev
// OR use this safer version in production:
// $dotenv->safeLoad();   // silent fail if .env missing