<?php 
class User {
    private $conn;
    public $username;

    public function __construct($conn, $username) {
        $this->conn = $conn;
        $this->username = $username;
    }

    
}

?>