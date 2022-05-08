<?php
include_once('../core/Client.php');
include_once('../core/Finance.php');
include_once('../core/Response.php');

date_default_timezone_set('America/Sao_Paulo');

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
$type = $_REQUEST['type'];
$description = $_REQUEST['description'];

$isAmountValid = $amount && is_numeric($amount);
$isTypeValid = ($type=="debit" || $type=="credit");

if( !$isAmountValid || !$isTypeValid){
  $response->status(400);
  if(!$isAmountValid){
    $response->add("error", "Amount is required to be numeric");
  }else if(!$isTypeValid){
    $response->add("error", "Type is required and must be 'debit' or 'credit'");
  }
}else{
  $finance = new Finance($client->client);
  $result = $finance->append($type, $amount, $description);
  
  $response->replace($result);
}

$response->sendJson();

