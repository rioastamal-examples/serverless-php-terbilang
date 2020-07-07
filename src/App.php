<?php
namespace App;
use RioAstamal\AngkaTerbilang\Terbilang;

/**
 * Handler function which return a response and will be send back to Lambda
 *
 * @array $event Lambda Event
 * @return String
 */
function handler($event)
{
  $prettyPrint = array_key_exists('pretty', $event['queryStringParameters']) ? JSON_PRETTY_PRINT : 0;

  $response = [];
  $response['angka'] = $event['queryStringParameters']['angka'];
  $response['terbilang'] = Terbilang::create()->terbilang( strval($response['angka']) );

  return json_encode($response, $prettyPrint);
}

/**
 * Simple abstraction for HTTP GET Lambda invocation
 *
 * @param String $url
 * @return Array
 */
function fetch($url)
{
  $headers = [
    'Content-Type: application/json',
    'User-Agent: TeknoCerdas/1.0',
    'Accept: application/json'
  ];
  $options = [
    'http' => [
      'method' => 'GET',
      'header' => implode("\r\n", $headers)
    ]
  ];
  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);

  // Parse special variables called $http_response_header
  $requestId = null;

  foreach ($http_response_header as $value) {
    if (preg_match('/Lambda-Runtime-Aws-Request-Id/', $value)) {
      $requestId = trim(explode('Lambda-Runtime-Aws-Request-Id:', $value)[1]);
    }
  }

  return [
    'body' => json_decode($response, $toArray = true),
    'request_id' => $requestId
  ];
}

/**
 * Simple abstraction for HTTP POST
 *
 * @param String $url
 * @param String $data
 * @return String
 */
function post($url, $data)
{
  $headers = [
    'Content-Type: application/json',
    'User-Agent: TeknoCerdas/1.0',
    'Content-Length: ' . strlen($data)
  ];
  $options = [
    'http' => [
      'method' => 'POST',
      'header' => implode("\r\n", $headers),
      'content' => $data
    ]
  ];

  $context = stream_context_create($options);
  return file_get_contents($url, false, $context);
}

/**
 * Lambda main loop
 */
function main()
{
  $lambdaRuntimeApi = getenv('AWS_LAMBDA_RUNTIME_API');
  $lambdaBaseUrl = sprintf('http://%s/2018-06-01/runtime/invocation/', $lambdaRuntimeApi);
  $maxLoop = getenv('MAX_LOOP') ?: 10;
  $currentLoop = 0;

  while (true)
  {
    if (++$currentLoop > $maxLoop) {
      break;
    }

    $nextEvent = fetch($lambdaBaseUrl . 'next');
    $handlerResponse = handler($nextEvent['body']);

    $responseUrl = sprintf('%s%s/response', $lambdaBaseUrl, $nextEvent['request_id']);
    $lambdaResponse = post($responseUrl, $handlerResponse);
  }
}