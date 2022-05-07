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

if($client->isConnected){
  $amount = $_REQUEST['amount'];
  $description = $_REQUEST['description'];

  $finance = new Finance($client->client);
  $finance->append($amount, $description);
}else{
  $response->add("auth_url", $client->getAuthURL());
  $response->status(401)->sendJson();
}

