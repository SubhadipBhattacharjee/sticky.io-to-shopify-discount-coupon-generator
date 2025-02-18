<?php 
require_once('dbConnection.php');

//--1st subscription details from Sticky CRM by postback URL--//
class firstSubscription{
    
    private $db;
    private $usernameSticky;
    private $passwordSticky;
    private $accessURLSticky;
    
    public function __construct(){
        
        $database = new MySQL();
        $this->db = $database->connection();
        
        //--Setting Sticky CRM credentials--//
        $this->usernameSticky = '--USERNAME--';
        $this->passwordSticky = '--PASSWORD--';
        $this->accessURLSticky = '--access URL sticky--';
    }
    
    
    //----fetching 1st rebuild details----//
    public function firstRebuildDetails(){
        
        date_default_timezone_set('America/New_York'); 
        $created_at = date('Y-m-d H:i:s');
        
        //----Data to insert----//
        $fname = isset($_REQUEST['fname']) ? $_REQUEST['fname'] : NULL ;
        $lname = isset($_REQUEST['lname']) ? $_REQUEST['lname'] : NULL ;
        $email = $_REQUEST['email'] ;
        $phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : NULL;
        $cust_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : NULL;
        $camp_id = isset($_REQUEST['campaign_id']) ? $_REQUEST['campaign_id'] : NULL;
        $anst_id = isset($_REQUEST['ancestor_id']) ? $_REQUEST['ancestor_id'] : NULL;
        $order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : NULL;
        $addr1 = isset($_REQUEST['addr1']) ? $_REQUEST['addr1'] : NULL;
        $addr2 = isset($_REQUEST['addr2']) ? $_REQUEST['addr2'] : NULL;
        $city = isset($_REQUEST['city']) ? $_REQUEST['city'] : NULL;
        $zip = isset($_REQUEST['zip']) ? $_REQUEST['zip'] : NULL;
        $cntry = isset($_REQUEST['country']) ? $_REQUEST['country'] : NULL;
          
          
        //---CHECKING FOR SAME RECORD using "ancestor_id" & "email" ---//
        if(!empty($anst_id) && !empty($email)){
            $sql = "SELECT * FROM rebuild_orders_details WHERE ancestor_id ='"     .$anst_id."' && email = '".$email."' ";
            $result = $this->db->query($sql);
        }
        
        //---chceking "billing_cycle" from order_view in Sticy---//
        $order = $this->order_view_sticky($order_id);
        $billCycle = 0;
        if(isset($order["billing_cycle"])){
           $billCycle = $order["billing_cycle"];
        }
        
          
        if(($result->num_rows == 0) && ($billCycle == 1))
        {
            //Prepare the SQL statement
            $stmt = $this->db->prepare("INSERT INTO rebuild_orders_details (f_name, l_name,email,phone,order_id,ancestor_id,campaign_id, customer_id,add1 ,add2 ,city ,zip ,country,created_at ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
            //Bind parameters to the prepared statement
            $stmt->bind_param("ssssiiiissssss", $fname, $lname,$email,$phone,$order_id,$anst_id,$camp_id, $cust_id,$addr1 ,$addr2 ,$city ,$zip, $cntry,$created_at);
        
            // Execute the query
            if ($stmt->execute()) {
                echo "New record inserted successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
                 
        }  
        else
        {
            echo "No record found to insert";
        }
        
    }
    
    //---checking for "billing_cycle" of the order---//
    public function order_view_sticky($order_id){
        
        $orderId = [ "order_id" => [$order_id] ];
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
         CURLOPT_URL => $this->accessURLSticky,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_USERPWD => $this->usernameSticky . ":" . $this->passwordSticky,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS =>json_encode($orderId),
         CURLOPT_HTTPHEADER => array(
           'Content-Type: application/json'
         ),
        
      ));
      
      $response = curl_exec($curl);
      curl_close($curl);
      
      $value = json_decode($response,true);
      return $value;
      
      //if ($response === false) {
      //   die('Failed to execute cURL request.'); // Handle failed request
      // }
      // if (curl_errno($curl)) {
      //    $error_msg = curl_error($curl);
      //    curl_close($curl);
      //    die('cURL error: ' . $error_msg);;
      // }
        
        
    }
    
    
}

  $cls = new firstSubscription();
  $details =  $cls->firstRebuildDetails();
  
  echo $details;
  
  //if(isset($order["billing_cycle"])){
  //   echo $order["billing_cycle"];
  //}
  // print_r($result);
  
?>