<html>
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Online VPS Service">
<meta name="author" content="workhorse.">

<title>ssh-keys</title>

<!-- Bootstrap core CSS -->
<link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="/vendor/custom-made/stilizare.css" rel="stylesheet">
<!-- Custom fonts for this template -->
<link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="/vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
<link href="/https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

<!-- Custom styles for this template -->
<link href="/css/landing-page.min.css" rel="stylesheet">
<link href="/css/site.css" rel="stylesheet">

</head>

<body>
<nav class="navbar navbar-light bg-light static-top">
    <div class="container">
      <a class="navbar-brand" href="/">
        <img src="/img/logo1.png" id="logo-wh"/>
      </a>
    </div>
</nav>
<main>
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

$error = "";

if (isset($_POST['delete']))
{
    $id_ssh = (int)$_POST['delete'];
    $query = "DELETE FROM SSHKeys WHERE IdUser=".$user->id." AND Id=".$id_ssh;
    $data=mysqli_query($conn,$query);
    header('Location: /account/sshkeys.php');
    exit();
}
if (isset($_POST['ssh_key']))
{
    $ssh_key = $_POST['ssh_key'];
    $ssh_key = trim($ssh_key);
    $ssh_key = mysqli_real_escape_string($conn, $ssh_key);
    $ssh_key = str_replace("\n","", $ssh_key);

    if (substr($ssh_key, 0, 8) === "ssh-rsa ") {
        $query = "INSERT INTO SSHKeys(IdUser, SSHKey) VALUES(".$user->id.",'".$ssh_key."')";
        $data=mysqli_query($conn,$query);
        header('Location: /account/sshkeys.php');
        exit();
    }
    else {
        $error = "Not a valid ssh-rsa key";
    }
}

$query = "SELECT Id, SSHKey FROM SSHKeys WHERE IdUser=".$user->id;
$data=mysqli_query($conn,$query);
while($row=mysqli_fetch_array($data)){
?>







    <div class="container" style="margin-top: 6rem; margin-bottom: 6rem;">
    <form action="/account/sshkeys.php" method="POST">
    <div class="col-md-4 col-lg-4 col-xl-4 mx-auto">
    <input type="hidden" name="delete" value="<?php echo $row['Id']; ?>" />

    <div class="textarea" style="margin-left: 4rem">
    <textarea><?php echo htmlspecialchars($row['SSHKey']); ?></textarea>
    </div>


    <div class="col-md-3 col-lg-8 col-xl-6 mx-auto" style="margin-top: 1.5rem; margin-bottom: 1.5rem; padding-left: 0.2rem">
    <button type="submit" class="btn btn-danger ">Delete ssh key</button>
    </div>

    </div>
    </div>
    </form>
    <br>
<?php
}
?>


<div class="overlay"></div>
            <div class="container" style="margin-bottom: 6rem;">
            
<form action="/account/sshkeys.php" method="POST" id="addForm">
<div class="col-md-4 col-lg-4 col-xl-4 mx-auto">
<div class="label-custom text-primary" style="margin-top:3rem; margin-left: 5.5rem">
<label for="ssh_key" >Add a new ssh key!</label>
</div>

<br>
<div class="textarea" style="margin-left: 4rem">
<textarea form="addForm" name="ssh_key"></textarea>
</div>

<div class="col-md-3 col-lg-8 col-xl-6 mx-auto" style="margin-top: 1.5rem; margin-bottom: 1.5rem">
<button type="submit" class="btn btn-primary ">Add ssh key</button>
</div>

</div>
</form>
<?php
if ($error !== '')
{
    ?>
    <div class="alert alert-danger" role="alert">
    <?php echo htmlspecialchars($error); ?>
    </div>
    <?php
}
?>

</div>
</div>

<script>
let stateObj = {
    foo: "dashboard",
};

history.replaceState(stateObj, "", "/dashboard/");
</script>


</main>

<footer class="footer bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center text-lg-center my-auto">
          <p class="text-muted small mb-4 mb-lg-0">&copy; workhorse. 2019. All Rights Reserved.</p>
        </div>
      </div>
    </div>
  </footer>

<body>
</html>
