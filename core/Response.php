<?php
namespace Finance;
use stdClass;

class Response {
  private $output;
  private $statusCode = 200;
  public function __construct(){
    $this->output = new stdClass();
  }

  public function add($key, $value){
    $this->output->$key = $value;
  }

  public function replace($output){
    $this->output = $output;
  }

  public function setResponseHeader($type){
    header("Content-Type: ".$type);
  }

  public function status($code){
    $this->statusCode = $code;
    
    return $this;
  }

  public function sendJson(){
    $this->setResponseHeader("application/json");
    http_response_code($this->statusCode);

    echo json_encode($this->output);
  }
}