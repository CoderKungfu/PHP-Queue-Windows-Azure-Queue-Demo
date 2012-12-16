<?php
require_once __DIR__ . '/config.php';
PHPQueue\REST::$rest_server = new \Respect\Rest\Router('/index.php?url=');
PHPQueue\REST::defaultRoutes();