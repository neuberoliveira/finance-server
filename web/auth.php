<?php
include_once('../core/Client.php');
include_once('../core/Finance.php');

use Finance\Client;

if($_SERVER['QUERY_STRING']){
  $code = substr($_SERVER['QUERY_STRING'], 5);
  $client = new Client();
  
  $client->authWithCode($code);
  echo "Authenticated";
}else{
  echo "No query string available";
}



