<?php
require_once __DIR__ . '/config.php';
echo "Starting URL test.";
$r3 = new \Respect\Rest\Router('/urltest.php?url=/');
$r3->get('/', function() {
        return 'Hello World';
    });
$r3->get('/hello', function() {
        return 'Hello from Path';
    });
?>