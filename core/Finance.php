<?php
namespace Finance;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use DateTime;
use stdClass;

class Finance {
  private $client;
  private $service;
  private $spreadsheetId = "1k29GW98EmRiH7N1GkO13TxEoMMLJjl2ovXt4MEQzcwI";
  private static $HEADERS_ROWS_SIZE = 2;

  public function __construct($client){
    $this->client = $client;
    $this->service = new Google_Service_Sheets($this->client);
  }

  public function getNextEmptyLine(){
    $nextEmptyLine = null;
    $range = 'Gastos!A3:A';
    $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
    $results = $response->getValues();
    
    if (empty($results)) {
        print "No data found.\n";
    } else {
        $totalSize = count($results)+Finance::HEADERS_ROWS_SIZE;
        $nextEmptyLine = $totalSize+1;
        
    }
    return $nextEmptyLine;
  }

  public function append($amount, $description){
    $date = new DateTime();
    $range = 'Gastos!A3:A';
    $requestBody = new Google_Service_Sheets_ValueRange();

    $options = [
      "valueInputOption"=>"USER_ENTERED", 
      "includeValuesInResponse"=>true
    ];

    $params = [
      $date->format('d/m/Y'),
      $amount,
      $description,
    ];
    
    $requestBody->values = [$params];
    $response = $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $requestBody, $options);
    $result = new stdClass();

    $result->range = $response->updates->updatedRange;
    $result->cells = $response->updates->updatedCells;
    $result->columns = $response->updates->updatedColumns;
    $result->rows = $response->updates->updatedRows;
    $result->values = $response->updates->updatedData->values;

    return $result;
  }
}