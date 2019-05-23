<?php 


include '../debug.php';
include '../config/dep.php';
session_start();

if (isset($_SESSION['User']) && $_SESSION['User']!==null)
{
    header('Location: /');
    exit();
}

$error = '';

if (isset($_POST['email']) && isset($_POST['pass']))
{
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM Users WHERE Email LIKE '".mysqli_real_escape_string($conn, $_POST['email'])."' AND Password LIKE '".md5('workhorse'.$_POST['pass'])."'";
    $data=mysqli_query($conn,$query);
    if ($row=mysqli_fetch_array($data))
    {
        $query = "SELECT * FROM AccountBalance WHERE IdUser=".$row['Id'];
        $data2 = mysqli_query($conn,$query);
        if (!mysqli_fetch_array($data2))
        {
          $query = "INSERT INTO AccountBalance(IdUser,Balance) VALUES(".$row['Id'].",0);";
          $data2 = mysqli_query($conn,$query);
        }
        $_SESSION['User'] = new User($row['Id'], $row['Email']);

        header('Location: /dashboard');
        exit();
    }
    else {
        $error = 'The login credentials are incorrect!.';
    }

}
?>

<html>
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Online VPS Service">
<meta name="author" content="workhorse.">

<title>workhorse.</title>

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
    
        <div class="overlay"></div>
            <div class="container">
                <div class="formular ">
    
    <form method="POST" action="/account/login.php">
    <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
        <div class="label-custom text-primary">
        <label for="InputEmail">E-mail address:</label>
        </div>
        <input type="email" class="form-control" id="InputEmail" name="email" placeholder="Enter your e-mail address"/>

        <div class="label-custom text-primary">
        <label for="InputPassword">Password:</label>
        </div>      
        <input type="password" class="form-control" id="InputPassword" name="pass" placeholder="Enter your password" />
        <br>
    </div>
        <?php 
        if (isset($_GET['signup'])) {
        ?>
        <div class="alert alert-success" role="alert">
            Your account was registered! Please log in now.
        </div>
        </div>

        <?php }?>
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
        <div class="col-md-4 col-lg-8 col-xl-1 mx-auto" >
        <button type="submit" class="btn btn-primary " >Login</button>
    </div>
    </div>
    </form>
    </div>
    </div>
    

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

    </body>
</html>