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

if (isset($_POST['email']) && isset($_POST['pass1']) && isset($_POST['pass2']))
{
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        
        $db = new Database();
        $conn = $db->getConnection();

        $query = "SELECT * FROM Users WHERE Email LIKE '".mysqli_real_escape_string($conn, $_POST['email'])."'";
        $data=mysqli_query($conn,$query);
        if (mysqli_fetch_array($data))
        {
            $error = 'This email is already in use';
        }
        else {
            if ($_POST['pass1'] !== $_POST['pass2'])
            {
                $error = "The two passwords are different";
            }
            else {
                if (strlen($_POST['pass1'])<6)
                {
                    $error = 'The password should be at least 6 characters long';
                }
                else {
                    
                    $query = "INSERT INTO Users(Email, Password) VALUES('".mysqli_real_escape_string($conn, $_POST['email'])."','".md5('workhorse'.$_POST['pass1'])."')";
                    $data=mysqli_query($conn,$query);
                    if ($data) {
                        header('Location: /account/login.php?signup=1');
                        exit();
                    }
                    else {
                        $error = 'There is a problem with the database, please try again later!';
                    }
                }
            }
        }


    } else {
        $error = 'The input is not a valid email address.';
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
                <div class="formular-sign-up ">

    <form method="POST" action="/account/signup.php">
    <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
        <div class="label-custom text-primary">
        <label for="InputEmail">E-mail address:</label>
        </div>
        <input type="email" name="email" class="form-control" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>"/>
        <br>
        <div class="label-custom text-primary">
        <label for="InputPassword">Password:</label>
        </div>
        <input type="password" id="InputPassword" class="form-control"name="pass1" />
        <br>
        <div class="label-custom text-primary">
        <label for="Re-enterPassword">Re-enter Password::</label>
        </div>
        <input type="password" id="Re-enterPassword" class="form-control" name="pass2" />
        <br>
    </div>
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
    <div class="row">  
     <button type="submit" class="btn btn-primary ">Sign up!</button>
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