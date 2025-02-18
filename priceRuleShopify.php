<?php
require_once('dbConnection.php');
require_once('commonModule.php');

class PriceRules{
    
    
    public function __construct(){
        
        $database = new MySQL();
        $this->db = $database->connection();
        //--Shopify Credentials--//
        $this->accessToken = '';
        $this->accessURL = '';
    }
    
    //---Creating price rules in Shopify---//
    public function create_price_rule(){
        
        $start = $this->dateTime_ISO(); //fetching datetime in ISO format
        $users = $this->fetch_created_users();
        if(empty($users)){
           return "No user found to create price_rule.";
       }
        
        foreach($users as $user){
            
            $email = $user['email'];
            $cust_id = $user['customer_id'] ;
            
            $title = $this->random_coupon($email);
            
            $data = [
              price_rule =>[
                 "title"=> $title ,
                 "target_type"=>"line_item",
                 "target_selection"=>"all",
                 "allocation_method"=>"across",
                 "value_type"=>"fixed_amount",
                 "value"=>"-100.0",
                 "customer_selection"=>"prerequisite",
                 "prerequisite_customer_ids"=> [ $cust_id ],
                 "starts_at"=> $start  
              ]
            ];
            
           
            //----creating price_rules in Shopify----//
            $response= $this->POST($this->accessURL,$this->accessToken,$data);
            if($response['errors']){
               $resp = json_encode($response);
               $this->failedAPIResponse($resp,$email);
               continue;
            }
            //---inserting price_rule records in DB ---//
            $output = $this->insert_price_rules($response);
            echo $output;
            
        }
        
    }
    
    
    //---Hitting cURL to create price rule in Shopify---//
    public function POST($url,$token,$data){
       $cURL = new cURLWebhook($url,$token,$data); // commonModule.php file
       return $cURL->cURL_POST();
    }
    
    
    //---Saving Failed API response to 'shopify_created_user'---//
    public function failedAPIResponse($resp,$email){
        
        $priceRule_status = 2; // set for invalid API response.
        
        $sql = "UPDATE shopify_created_user SET price_rule_status = ?, price_rule_api_resp = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iss', $priceRule_status, $resp, $email);
       
       // Execute the query
       if ($stmt->execute()) {
          echo "Falied API response saved." ;
       } else {
          echo $stmt->error;
       }
        
        
    }
    
    
    //---getting users list created in Shopify---//
    public function fetch_created_users(){
        $sql = "SELECT a.user_email as email, a.customer_id, b.f_name from shopify_created_user a left join rebuild_orders_details b on a.user_email=b.email WHERE a.price_rule_status=0";
        $users = $this->db->query($sql);
        return $users->fetch_all(MYSQLI_ASSOC);
    }
    
    
    //---inserting records into 'shopify_price_rules' TABLE---//
    public function insert_price_rules($data){
        
       $cust_id = $data['price_rule']['prerequisite_customer_ids'][0];   
       $status = 1;
       $resp = json_encode($data);
       
       $sql = "INSERT INTO shopify_price_rules (shopify_cust_id, price_rule_id, price, discount_code) VALUES (?, ?, ?, ?)";
       $stmt = $this->db->prepare($sql);
       $stmt->bind_param('ssss',$cust_id, $data['price_rule']['id'], $data['price_rule']['value'], $data['price_rule']['title']);
       
       
       // Execute the query
       if ($stmt->execute()) {
          $this->update_price_rule_status($status,$cust_id,$resp);
          return $stmt->insert_id;
       } else {
          return $stmt->error;
       }
        
    }
    
    
    //---updating 'price_rule_status' of 'shopify_created_user' table---//
    function update_price_rule_status($status,$cust_id,$resp){
        
       $sql = "UPDATE shopify_created_user SET price_rule_status = ?, price_rule_api_resp = ? WHERE customer_id = ?";
       $stmt = $this->db->prepare($sql);
       $stmt->bind_param('iss', $status,$resp,$cust_id);
       
       // Execute the query
       if ($stmt->execute()) {
          return "Record updated successfully" ;
       } else {
          return $stmt->error;
       }
    }
    
    
    //---Converting dateTime in ISO format---//
    public function dateTime_ISO(){
        
      date_default_timezone_set('America/New_York'); 
      $created_at = date('Y-m-d H:i:s');
    
      $dateTime = new DateTime($created_at);
      $isoFormat = $dateTime->format(DateTime::ATOM);//ATOM is an alias for ISO
      return $isoFormat;
        
    }
    
    public function random_coupon($email){
        
       $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
       
       $beforeAt = explode('@', $email)[0];
       $randomString = substr(str_shuffle($char), 0, 4);
        
       return $beforeAt.$randomString."100off";
    }
    
    
}

     $priceRules = new PriceRules();
     $data = $priceRules->create_price_rule();
     echo $data;
     
     
     
     //$pass = 'Subhadip2024#';
     
     //$enc = base64_encode($pass);
     //$dec = base64_decode($enc);
     //echo $enc ;
?>

