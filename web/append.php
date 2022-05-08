<?php
require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

use Finance\Finance;
use Finance\Client;
use Finance\Response;

$response = new Response();
$client = new Client();
$finance = new Finance($client->client);

$client->auth();

if(!$client->isConnected){
  $response->add("auth_url", $client->getAuthURL());
  $response->status(401)->sendJson();
  exit();
}

$amount = $_REQUEST['amount'];
$type = $_REQUEST['type'];
$description = $_REQUEST['description'];

$validateErrors = $finance->appendValidate($type, $amount, $description);
if($validateErrors){
  $response->status(400)->setError($validateErrors);
}else{
  $result = $finance->append($type, $amount, $description);
  $response->replace($result);
}

$response->sendJson();

