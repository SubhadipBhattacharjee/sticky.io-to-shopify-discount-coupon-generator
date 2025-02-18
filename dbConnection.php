<?php
//namespace App\Database;

class MySQL
{
   
    private $host ;
    private $username ;
    private $password ;
    private $dbName ;
    private $connection ;
    
    public function __construct() {
      
      $this->host = "---HOST/IP---" ;
      $this->username = "---USERNAME---" ;
      $this->password = "---PASSWORD---" ;
      $this->dbName =  "---DATABASE---" ;
    }
    
    public function connection(){
       
      if ($this->connection === null) {
            //---Create a new connection using Singletone pattern---//
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->dbName);

            // Check for connection errors
            if ($this->connection->connect_error) {
                die("Connection failed: " . $this->connection->connect_error);
            }
      }
      return $this->connection; 
    
    }
    
    //Optional: Method to close the connection
    public function close() {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
}

?>