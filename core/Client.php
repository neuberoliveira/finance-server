<?php
namespace Finance;
use Google_Client;
use Google_Service_Sheets;
use Exception;

require __DIR__ . '/../vendor/autoload.php';

class Client {
    public $isConnected = false;
    public $client;
    private $tokenPath = '../token.json';
    
    public function __construct(){
        $credential = $_ENV["sheet_credential"];
        $this->client = new Google_Client();
        $this->client->setScopes(
            Google_Service_Sheets::DRIVE,
            Google_Service_Sheets::DRIVE_FILE,
            Google_Service_Sheets::DRIVE_READONLY,
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Sheets::SPREADSHEETS_READONLY
        );
        $this->client->setApplicationName('Finance App');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setAuthConfig(json_decode($credential));

        $currentToken = $this->loadToken();
        if($currentToken){
            $this->client->setAccessToken($currentToken);
        }

    }

    private function loadToken(){
        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        
        if (file_exists($this->tokenPath)) {
            $accessToken = json_decode(file_get_contents($this->tokenPath), true);
            return $accessToken;
        }

        return null;
    }
    
    private function saveToken($token){
        // Save the token to a file.
        if (!file_exists(dirname($this->tokenPath))) {
            mkdir(dirname($this->tokenPath), 0700, true);
        }
        file_put_contents($this->tokenPath, json_encode($token));
    }

    public function auth(){
        // If there is no previous token or it's expired.
        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            }
        }
    }
    
    public function authWithCode($authCode){
        // Exchange authorization code for an access token.
        $accessToken = $this->client->fetchAccessTokenWithAuthCode("4/0AX4XfWjo1qrkYK-X7vy-EwoU0egGNSUhACooLem52M8-7EGIOBnonkeCOLp7WNSrbjUNhg&scope=https://www.googleapis.com/auth/drive");
        
        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }
        
        $this->client->setAccessToken($accessToken);
        $this->saveToken($accessToken);
        $this->isConnected = true;
    }

    public function getAuthURL(){
        return $this->client->createAuthUrl();
    }
}