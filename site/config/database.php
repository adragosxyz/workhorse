<?php
error_reporting( E_ALL );

class Database{
    private $host = "localhost";
    private $port = "3306";
    private $db_name = "workhorse";
    private $username = "workhorse";
    private $password = "workhorsepassword";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = mysqli_connect($this->host.":".$this->port, $this->username, $this->password, $this->db_name);
        }
        catch(Exception $e) {
            die("Conexiunea cu serverul sql a esuat");
        }

        return $this->conn;
    }
}