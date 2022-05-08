<?php
include_once('../core/Client.php');
include_once('../core/Finance.php');
include_once('../core/Response.php');

use Finance\Finance;
use Finance\Client;
use Finance\Response;

$response = new Response();
$client = new Client();
$client->auth();

if(!$client->isConnected){
  $response->add("auth_url", $client->getAuthURL());
  $response->status(401)->sendJson();
  exit();
}


$amount = $_REQUEST['amount'];
$description = $_REQUEST['description'];

if(!$amount || !is_numeric($amount) ){
  $response->status(400);
  $response->add("error", "Amount is required to be numeric");
}else{
  $finance = new Finance($client->client);
  $result = $finance->append($amount, $description);
  
  $response->replace($result);
}

$response->sendJson();

