<?php 

include '../debug.php';
include '../config/dep.php';

session_start();
if (!isset($_SESSION['User']) || $_SESSION['User']===null) {
  header('Location: /account/login.php');
  exit();
}

$user = $_SESSION['User'];

$db = new Database();
$conn = $db->getConnection();
$createvm = 0;

$error = '';

if ($user->balance - 21 < 0)
{
    $error = "You don't have enough money to start a VM";
}

if ($error === '') {
    $query = "SELECT Id, SSHKey FROM SSHKeys WHERE IdUser=".$user->id;
    $data=mysqli_query($conn,$query);
    if ($row=mysqli_fetch_array($data)){
        if (isset($_POST['subdomain']))
        {
            $subdomain = trim($_POST['subdomain']);
            if ($subdomain=="www" || $subdomain=="wwww" || $subdomain=="http" || $subdomain=="https") {
                $error = "Subdomain is blacklisted";
            }
            else
            if (strlen($subdomain) < 3)
            {
                $error = "Subdomain name must be at least 3 characters long";
            }
            else if (strlen($subdomain) > 15)
            {
                $error = "Subdomain can be at most 15 characters";
            }
            else {
                $alph = "qwertyuiopasdfghjklzxcvbnm";
                for ($i=0;$i<strlen($subdomain);$i++)
                {
                    if (strpos($alph, $subdomain[$i]) === false) {
                        $error = "Subdomain contains invalid characters";
                    }
                }
                if ($error === '')
                {
                    $query = "SELECT Subdomain FROM VirtualMachines WHERE Subdomain LIKE '".$subdomain."'";
                    $data=mysqli_query($conn,$query);
                    if ($row=mysqli_fetch_array($data)){
                        $error = "The subdomain name is already taken";
                    }
                    else {
                        $error = "Your VM was created successfully! It will be online in 3-5 minutes!";
                        $createvm = 1;
                        ignore_user_abort(true);
                        set_time_limit(0);

                        ob_start();
                    }
                }
            }
        }
    }
    else {
        $error = "You have no SSH Key, please go and add one before creating a VM";
    }
    }
?>

<?php 
if ($error === '') {
?>
    <h3>Create VPS</h3>
    <form action="/dashboard/addvm.php" method="POST">
        <label for="subdomain">Subdomain name (for HTTP access) minimum 3 characters, all lowercase alphabet</label>
        <input type="text" name="subdomain" id="subdomain">
        <br>
        <label for="">OS: Ubuntu 18.04.2</label>
        <br>
        <label for="">Price: $14/month ($0.021/hour)</label>
        <br>
        <label for="">Authentication: <a href="/account/sshkeys.php">SSH Key</a></label>
        <br>
        <button type="submit">Create</button>
    </form>
<?php } 
else {
?>
        <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($error); ?>
        </div>
<?php } ?>

<?php 
if ($createvm === 1) {
    header('Connection: close');
    header('Content-Length: '.ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();

    $query = "SELECT SSHKey FROM SSHKeys WHERE IdUser=".$user->id;
    $data=mysqli_query($conn,$query);
    $auth_keys = "";

    $path = "/vps/".$subdomain."/";
    mkdir($path);

    while ($row=mysqli_fetch_array($data)){
        $auth_keys = $auth_keys.$row['SSHKey']."\n";
    }

    file_put_contents($path."keys", $auth_keys);

    $vagrantfile = file_get_contents("/vps/VagrantfileModel");

    //luam toate porturile ocupate din baza de date
    $sshports = array();
    $ftpports = array();
    $query = "SELECT SSHPort, FTPPort FROM VirtualMachines";
    $data=mysqli_query($conn,$query);

    while ($row=mysqli_fetch_array($data)){
        array_push($sshports, (int)$row['SSHPort']);
        array_push($ftpports, (int)$row['FTPPort']);
    }

    //cautam un port SSH liber in intervalul 2223-...
    $startssh = 2223;
    $startftp = 21210;
    $sshport = 0;
    $ftpport1 = 0;
    $ftpport2 = 0;

    for ($i=$startssh; $i<=$startssh+3000; $i++)
    {
        if (!in_array($i, $sshports))
        {
            $sshport = $i;
            break;
        }
    }
    //cautam 2 porturi FTP libere in intervalul 21210-... 
    for ($i=$startftp; $i<=$startftp+6000; $i++)
    {
        if (!in_array($i, $ftpports) && !in_array($i+1, $ftpports) && !in_array($i-1, $ftpports))
        {
            $ftpport1 = $i;
            $ftpport2 = $i+1;
            break;
        }
    }

    $vagrantfile = str_replace("SSHPORTREPLACE",$sshport, $vagrantfile);
    $vagrantfile = str_replace("FTPPORTREPLACE1",$ftpport1, $vagrantfile);
    $vagrantfile = str_replace("FTPPORTREPLACE2",$ftpport2, $vagrantfile);

    file_put_contents($path."Vagrantfile", $vagrantfile);

    exec("cd ".$path."; sudo vagrant up");
    $hostname = file_get_contents($path."dockername");
    $hostname = trim($hostname);

    $private_ip = exec("sudo docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ".$hostname);
    $private_ip = trim($private_ip);

    $query = "INSERT INTO VirtualMachines(IdUser,Name,Path,PrivateIP,SSHPort,FTPPort,Subdomain,StartDate,LastPaidDate,Active,Price) VALUES ";
    $query = $query."(".$user->id.", '".$hostname."', '".$path."', '".$private_ip."', ".$sshport.", ".$ftpport1.", '".$subdomain."', NOW(), NOW(), 1, 21);";
    $data= mysqli_query($conn,$query);

    $query = "UPDATE AccountBalance SET Balance=Balance-21 WHERE IdUser=".$user->id;
    $data= mysqli_query($conn,$query);

    $proxypass = file_get_contents("/vps/proxypass.txt");
    $proxypass = str_replace("REPLACESUBDOMAIN", $subdomain, $proxypass);
    $proxypass = str_replace("REPLACEPRIVATEIP", $private_ip, $proxypass);
    $proxypass = str_replace("\r", "", $proxypass);

    file_put_contents("/etc/apache2/sites-available/000-default.conf", $proxypass, FILE_APPEND | LOCK_EX);

    exec("sudo service apache2 reload");
}

?>