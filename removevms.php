<?php 

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

$query = "SELECT Name,Path,PrivateIP,Subdomain FROM VirtualMachines WHERE Active=0";
$db = new Database();
$conn = $db->getConnection();
$data=mysqli_query($conn,$query);

$path = "";
while ($row=mysqli_fetch_array($data)){
    $path = $row["Path"];
    $private_ip = trim($row["PrivateIP"]);
    $subdomain = trim($row["Subdomain"]);
    $name = trim($row["Name"]);
    if ($path=="")
    {
        continue;
    }

    $proxypass = file_get_contents("/vps/proxypass.txt");
    $proxypass = str_replace("REPLACESUBDOMAIN", $subdomain, $proxypass);
    $proxypass = str_replace("REPLACEPRIVATEIP", $private_ip, $proxypass);
    $proxypass = str_replace("\r", "", $proxypass);

    $apc = file_get_contents("/etc/apache2/sites-available/000-default.conf");
    $apc = str_replace($proxypass, "", $apc);
    file_put_contents("/etc/apache2/sites-available/000-default.conf", $apc);

    $query = "DELETE FROM VirtualMachines WHERE Active=0 AND Path='".$path."'";
    $data2=mysqli_query($conn,$query);

    exec("cd /tmp; cd ".$path."; sudo vagrant destroy -f; cd /tmp;");
    exec("sudo docker container rm -f ".$name);
    exec("sudo rm -rf ".$path);

    exec("sudo service apache2 reload");
}

?>