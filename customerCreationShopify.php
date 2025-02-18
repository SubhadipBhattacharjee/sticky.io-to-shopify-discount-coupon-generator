<?php
require_once('dbConnection.php');
require_once('commonModule.php');

class userCreation{

   private $db ;
   
   public function __construct(){
     
     $database = new MySQL();
     $this->db = $database->connection();
     //--Shopify Credentials--//
     $this->accessToken = '--access-token of shopify---' ;
     $this->accessURL = '---access URL of Shopify---';

   }
   
    //---creating customer into shopify---//
    public function create_customer(){
       
       $user_details = $this->subscribed_user();
       if(empty($user_details)){
           return "No user found to create in Shopify account.";
       }
       
       foreach($user_details as $det){
      
         $password = $det['f_name']."2024#";
         $email = $det['email'];
         
         $data=[
            "customer"=>[
               "first_name"=>$det['f_name'],
               "last_name"=>$det['l_name'],
               "email"=>$det['email'],
               "phone"=>$det['phone'],
               "verified_email"=>true,
               "addresses"=>[
                  [
                    "address1"=>$det['add1'],
                    "city"=>$det['city'],
                    //"province"=>"ON",
                    "phone"=>$det['phone'],
                    "zip"=>$det['zip'],
                    "last_name"=>$det['l_name'],
                    "first_name"=>$det['f_name'],
                    "country"=>$det['country']
                  ]
                ],
                "password"=>$password,
                "password_confirmation"=>$password,
                "send_email_welcome"=>false
            ]
         ];
         
         //----creating users in Shopify----//
         $response= $this->POST($this->accessURL,$this->accessToken,$data);
         if($response['errors']){
             //return json_encode($response);
             $resp = json_encode($response);
             $this->failedAPIResponse($resp,$email);
             continue;
         }
         
         //---inserting created_users details in DB---//
         $output = $this->insert_created_users($password,$response);
         echo $output ;
       }
       
    }
   
    //----Hitting cURL to create user in Shopify----//
    public function POST($url,$token,$data){
       $cURL = new cURLWebhook($url,$token,$data); // commonModule.php file
       return $cURL->cURL_POST();
    }
    
    //---Saving Failed API response to the TABLE---//
    public function failedAPIResponse($resp,$email){
        
        $user_status = 2; // set for invalid API response.
        
        $sql = "UPDATE rebuild_orders_details SET user_creation_resp = ?, user_creation = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sis', $resp, $user_status, $email);
       
       // Execute the query
       if ($stmt->execute()) {
          echo "Falied API response saved." ;
       } else {
          echo $stmt->error;
       }
        
        
    }
   
   
    //---Fetching subscribed users_details from DB---//
    public function subscribed_user(){
   
      $sql = "SELECT * FROM rebuild_orders_details WHERE user_creation = 0";
      $result = $this->db->query($sql);
      return $result->fetch_all(MYSQLI_ASSOC);
      //return $result->fetch_assoc();
    }
   
   
    //---inserting created users(in shopify) details in DB---//
    public function insert_created_users($pass,$data){
       
       $email = $data['customer']['email'];
       $user_creation = 1;  
       $resp = json_encode($data);
       
       $sql = "INSERT INTO shopify_created_user (user_email, phone, password, customer_id, adress_id) VALUES (?, ?, ?, ?, ?)";
       $stmt = $this->db->prepare($sql);
       $stmt->bind_param('sssss', $email, $data['customer']['phone'], base64_encode($pass), $data['customer']['id'], $data['customer']['addresses'][0]['id']);
       
       
       // Execute the query
       if ($stmt->execute()) {
          $this->update_field($user_creation,$resp,$email);
          return $stmt->insert_id;
       } else {
          return $stmt->error;
       }
        
    }
   
    //---updating "user_creation" & 'user_creation_resp' col of "rebuild_orders_details" table---//
    public function update_field($user_creation,$resp,$email){
       
       $sql = "UPDATE rebuild_orders_details SET user_creation = ? , user_creation_resp = ? WHERE email = ?";
       $stmt = $this->db->prepare($sql);
       $stmt->bind_param('iss', $user_creation,$resp, $email);
       
       // Execute the query
       if ($stmt->execute()) {
          return "user_creation column set to 1." ;
       } else {
          return $stmt->error;
       }
       
    }
   
   
}
  
    $user = new userCreation();
    $details = $user->create_customer();
    echo $details;
  
?>