<?php
require __DIR__ . '/../vendor/autoload.php';


use Finance\Client;
use Finance\Response;

$response = new Response();

if(isset($_GET['code']) && isset($_GET['scope'])){
  $code = substr($_SERVER['QUERY_STRING'], 5);
  $client = new Client();
  
  try {
    $client->authWithCode($code);
    $response->add("auth", "authenticated");
  }catch(Exception $ex){
    $response->setErrors("Invalid authentication code");
    $response->add("auth", "fail");
    $response->add("detail", $ex->getMessage());
    $response->status(400);
  }
}else{
  $response->setErrors("Authentication code not found");
  $response->add("auth", "fail");
  $response->status(400);
}
$response->sendJson();



