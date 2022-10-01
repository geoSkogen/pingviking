<?php

require(__DIR__ . '/vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$sitemap_urls = [];
$request_url = "https://pdx.edu/socdial-work/sitemap.xml";
$client = new GuzzleHttp\Client();
$response = '';
try {
  $http_response = $client->request(
    'GET',
    $request_url,
    ["headers" => [], "body" => '']
  );
  $response = $http_response->getBody();
  print($response);
  try {
    $test_dom = new SimpleXMLElement($response);
  } catch (Exception $e) {
    error_log( $e->getMessage() );
  }
  print_r($test_dom);
  foreach ($test_dom->url as $map_url ) {
    $sitemap_urls[] = $map_url->loc->__toString();
  }
} catch (RequestException $e) {
  if ($e->hasResponse()) {
    $err_response = $e->hasResponse();
    error_log($err_response);
  }
}
print_r($sitemap_urls);
