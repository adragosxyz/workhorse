<?php 

include '../debug.php';
include '../config/dep.php';

header('Location: /dashboard/');

session_start();
if (!isset($_SESSION['User']) || $_SESSION['User']===null) {
  header('Location: /account/login.php');
  exit();
}

$user = $_SESSION['User'];

if (isset($_POST['id']))
{
    $id = (int)$_POST['id'];

    $vm = "";
    for ($i=0;$i<sizeof($user->vms);$i++)
    {
        if ($id==$user->vms[$i]->id)
        {
            $vm = $user->vms[$i];
        }
    }
    if ($vm=="")
    {
        exit(0);
    }

    $query = "SELECT Path,PrivateIP,Name FROM VirtualMachines WHERE Id=".$id." AND IdUser=".$user->id;
    $db = new Database();
    $conn = $db->getConnection();
    $data=mysqli_query($conn,$query);

    $path = "";
    $private_ip="";
    $name = "";
    if ($row=mysqli_fetch_array($data)){
        $path = $row["Path"];
        $private_ip = trim($row["PrivateIP"]);
        $name = trim($row["Name"]);
    }

    if ($path=="")
    {
        exit(0);
    }

    $proxypass = file_get_contents("/vps/proxypass.txt");
    $proxypass = str_replace("REPLACESUBDOMAIN", $vm->subdomain, $proxypass);
    $proxypass = str_replace("REPLACEPRIVATEIP", $private_ip, $proxypass);
    $proxypass = str_replace("\r", "", $proxypass);

    $apc = file_get_contents("/etc/apache2/sites-available/000-default.conf");
    $apc = str_replace($proxypass, "", $apc);
    file_put_contents("/etc/apache2/sites-available/000-default.conf", $apc);

    $query = "DELETE FROM VirtualMachines WHERE Id=".$id." AND IdUser=".$user->id;
    $data=mysqli_query($conn,$query);

    exec("cd ".$path."; sudo vagrant destroy -f");
    exec("sudo docker container rm -f ".$name);

    exec("sudo service apache2 reload");
}

?>