<?php
include_once('../core/Client.php');
include_once('../core/Response.php');

use Finance\Client;
use Finance\Response;

$response = new Response();

if(isset($_GET['code']) && isset($_GET['scope'])){
  $code = substr($_SERVER['QUERY_STRING'], 5);
  $client = new Client();
  
  try {
    $client->authWithCode($code);
    $response->add("auth", "authenticated");
    $response->add("token", json_encode($client->getAccessToken()));
  }catch(Exception $ex){
    $response->add("auth", "fail");
    $response->add("error", "Invalid authentication code");
    $response->add("detail", $ex->getMessage());
    $response->status(400);
  }
}else{
  $response->add("auth", "fail");
  $response->add("error", "Authentication code not found");
  $response->status(400);
}
$response->sendJson();



