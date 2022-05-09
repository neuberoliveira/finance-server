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
        $credentials = $this->getCredentials();
        
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
        $this->client->setAuthConfig($credentials);
        $this->client->setRedirectUri($this->getURLMatchServer($credentials));
    }

    private function getURLMatchServer($config){
        $parsed;
        $currentHost = $_SERVER["SERVER_NAME"];
        $uris = $config["web"]["redirect_uris"];

        foreach($uris as $uri){
            $parsed = parse_url($uri);
            if($parsed["host"]==$currentHost){
                return $uri;
            }
        }

        throw new Exception("Server $currentHost not match any uri");
    }

    private function getCredentials(){
        $configFile = "../credentials.json";
        $credentialContent;
        if(isset($_ENV["sheet_credential"])){
            $credentialContent = $_ENV["sheet_credential"];
        }else if(file_exists($configFile)){
            $credentialContent = file_get_contents($configFile);
        }else {
            throw new Exception("No Credentials available, either in file or in env");
        }

        $credentials = json_decode($credentialContent, true);
        return $credentials;
    }

    public function loadToken(){
        $tokenStr;
        $accessToken;
        if(isset($_GET["token"])){
            $tokenStr = $_GET["token"];
        }else if (file_exists($this->tokenPath)) {
            $tokenStr = file_get_contents($this->tokenPath);
        }
        
        if($tokenStr){
            $accessToken = json_decode($tokenStr, true);
            return $accessToken;
        }
    }
    
    private function saveToken($token){
        // Save the token to a file.
        if (!file_exists(dirname($this->tokenPath))) {
            mkdir(dirname($this->tokenPath), 0700, true);
        }
        file_put_contents($this->tokenPath, json_encode($token));
    }

    public function auth(){
        $loadedToken = $this->loadToken();
        if(!$loadedToken){
            return null;
        }

        
        $this->client->setAccessToken($loadedToken);
        // If there is no previous token or it's expired.
        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                
                $this->saveToken($this->client->getAccessToken());
            }
        }
        $this->isConnected = true;
    }
    
    public function authWithCode($authCode){
        // Exchange authorization code for an access token.
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
        
        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }
        
        $this->client->setAccessToken($accessToken);
        $this->saveToken($accessToken);
        $this->isConnected = true;
    }

    public function getAccessToken(){
        return $this->client->getAccessToken();
    }

    public function getAuthURL(){
        return $this->client->createAuthUrl();
    }
}