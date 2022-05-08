<?php
require __DIR__ . '/../vendor/autoload.php';


use Finance\Client;
use Finance\Response;

$response = new Response();
$client = new Client();


$client->auth();

if(!$client->isConnected){
  $response->setErrors("No token available");
  $response->add("auth_url", $client->getAuthURL());
  $response->status(404)->sendJson();
  exit();
}
$token = $client->getAccessToken();
$response->add("token", $token["access_token"]);
$response->sendJson();



