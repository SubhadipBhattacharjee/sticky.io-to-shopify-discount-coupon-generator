<?php 
require_once('dbConnection.php');
require_once('commonModule.php');

class discountCode{
    
    public function __construct(){
        
        $database = new MySQL();
        $this->db = $database->connection();
        //--Shopify Credentials--//
        $this->accessToken = '--access-token of shopify---' ;
        //$this->accessURL = '';
    }
    
    //---Creating discount code in Shopify---//
    public function create_discount_code(){
        
        $lists = $this->fetch_price_rules_list();
        if(empty($lists)){
           return "No user found to create discount code.";
       }
       
       foreach($lists as $list){
           
            $priceRuleId = $list['price_rule_id'];
           
            $data =  [
               "discount_code" => [
                "code" => $list[discount_code]
                ] 
            ];
            
            $accessURL = 'https://406f58-37.myshopify.com/admin/api/2024-07/price_rules/'.$list[price_rule_id].'/discount_codes.json';    
            
            
            //----creating discount_code in Shopify----//
            $response= $this->POST($accessURL,$this->accessToken,$data);
            if($response['errors']){
               $resp = json_encode($response);
               $this->failedAPIResponse($resp,$priceRuleId);
               continue;
            }
            //---inserting discount_code records in DB ---//
            $output = $this->insert_discount_code($response);
            echo $output;
       }
        
        
    }
    
    //---Hitting cURL to create discount code in Shopify---//
    public function POST($url,$token,$data){
       $cURL = new cURLWebhook($url,$token,$data); // commonModule.php file
       return $cURL->cURL_POST();
    }
    
    //---Saving Failed API response to 'shopify_price_rules'---//
    public function failedAPIResponse($resp,$priceRuleId){
        
        $priceRule_status = 2; // set for invalid API response.
        
        $sql = "UPDATE shopify_price_rules SET disc_code_status = ?, disc_code_api_resp = ? WHERE price_rule_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iss', $priceRule_status, $resp, $priceRuleId);
       
       // Execute the query
       if ($stmt->execute()) {
          echo "Falied API response saved." ;
       } else {
          echo $stmt->error;
       }
        
        
    }
    
    
    //---getting price_rules list created in Shopify---//
    public function fetch_price_rules_list(){
       $sql = "SELECT * FROM shopify_price_rules WHERE disc_code_status = 0";
       $result = $this->db->query($sql);
       return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    
    //---inserting records into 'shopify_discount_code' TABLE---//
    public function insert_discount_code($data){
        
       $code = $data['discount_code']['code'];
       $price_rule_id = $data['discount_code']['price_rule_id'];
       $status = 1;
       $resp = json_encode($data);
       
       $sql = "INSERT INTO shopify_discount_code (discount_code, discount_code_id, price_rule_id) VALUES (?, ?, ?)";
       $stmt = $this->db->prepare($sql);
       $stmt->bind_param('sss',$code ,$data['discount_code']['id'], $data['discount_code']['price_rule_id']);
       
       
       // Execute the query
       if ($stmt->execute()) {
          $this->update_discount_code_status($status,$price_rule_id,$resp);
          return $stmt->insert_id;
       } else {
          return $stmt->error;
       }
        
    }
    
    //---updating 'discount_code_status' of 'shopify_price_rules' table---//
    function update_discount_code_status($status,$price_rule_id,$resp){
        
       $sql = "UPDATE shopify_price_rules SET disc_code_status = ?,disc_code_api_resp = ? WHERE price_rule_id = ?";
       $stmt = $this->db->prepare($sql);
       $stmt->bind_param('iss', $status,$resp,$price_rule_id);
       
       // Execute the query
       if ($stmt->execute()) {
          return "Record updated successfully" ;
       } else {
          return $stmt->error;
       }
    }
    
    
}

   $priceList = new discountCode();
   $data = $priceList->create_discount_code();
   
   echo $data;
   
   
?>