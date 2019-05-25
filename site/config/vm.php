<?php

class VM
{
    public $id;
    public $name;
    public $subdomain;
    public $startdate;
    public $active;
    public $price;
    public $sshport;
    public $ftpport;
    function __construct($_id, $_name, $_sshport, $_ftpport, $_subdomain, $_startdate, $_active, $_price) {
        $this->id = $_id;
        $this->name = $_name;
        $this->sshport = $_sshport;
        $this->ftpport = $_ftpport;
        $this->subdomain = $_subdomain;
        $this->startdate = $_startdate;
        $this->active = $_active;
        $this->price = $_price;
    }
}


?>