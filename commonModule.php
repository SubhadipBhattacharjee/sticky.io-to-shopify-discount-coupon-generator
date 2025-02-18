<?php 

class cURLWebhook{
    
    public $bodyData;
    public $accessURL;
    public $accessToken;
    
    public function __construct($accessURL,$accessToken,$bodyData){
        
        $this->accessURL = $accessURL;
        $this->accessToken = $accessToken;
        $this->bodyData =  $bodyData;
    }
    
    
    public function cURL_POST(){
        
      $curl = curl_init();
        curl_setopt_array($curl, array(
         CURLOPT_URL => $this->accessURL,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS =>json_encode($this->bodyData),
         CURLOPT_HTTPHEADER => array(
           'X-Shopify-Access-Token: '.$this->accessToken,
           'Content-Type: application/json'
         ),
        
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      
      $value = json_decode($response,true);
      return $value;
      
    }
}

?>