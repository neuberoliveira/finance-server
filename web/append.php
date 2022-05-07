<?php
include_once('../core/Client.php');
include_once('../core/Finance.php');

use Finance\Finance;
use Finance\Client;

$client = new Client();
$client->auth();

if($client->isConnected){
  $finance = new Finance($client->client);
  $finance->append(-1, "Integer");
}else{
  echo $client->getAuthURL();
}

