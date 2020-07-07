<?php
require __DIR__ . '/vendor/autoload.php';

// Run for Lambda
if (getenv('AWS_LAMBDA_RUNTIME_API')) {
  App\main();
  exit(0);
}

// Should be run from CLI
$payload = file_get_contents(__DIR__ . '/src/event.json');
echo App\handler( json_decode($payload, $toArray = true));