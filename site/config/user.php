<?php

class User
{
    public $id;
    public $email;
    public $balance;
    public $vms;

    function __construct($_id, $_email) {
        $this->id = (int)$_id;
        $this->email = $_email;
        $this->balance = $_balance;

        if (id<1) return;

        $db = new Database();
        $conn = $db->getConnection();

        $query = "SELECT Id,Name,Subdomain,StartDate,Active,Price FROM VirtualMachines WHERE UserId='.$id.'";
        $data=mysqli_query($con,$query);

        $this->vms = array();

        while($row=mysqli_fetch_array($data)){
            array_push($this->vms, new VM(row[0],row[1],row[2],row[3],row[4],row[5]));
        }

    }

}


?>