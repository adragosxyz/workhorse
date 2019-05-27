<?php 

include '../debug.php';
include '../config/dep.php';

session_start();
if (!isset($_SESSION['User']) || $_SESSION['User']===null) {
  header('Location: /account/login.php');
  exit();
}

$user = $_SESSION['User'];
$error='';

if (isset($_POST['code']))
{
    $db = new Database();
    $conn = $db->getConnection();
    $code = $_POST['code'];
    $query = "SELECT Id, CouponValue FROM Coupons WHERE CouponCode LIKE '".$code."'";
    $data=mysqli_query($conn,$query);
    if ($row=mysqli_fetch_array($data)){
        $coupon_id = $row['Id'];
        $value = (int)$row['CouponValue'];
        $query = "SELECT * FROM CouponTransaction WHERE IdCoupon=".$coupon_id." AND IdUser=".$user->id;
        $data2=mysqli_query($conn,$query);
        if ($row2 = mysqli_fetch_array($data2))
        {
            $error="You have already redeemed this coupon!";
        }
        else {
            $query = "INSERT INTO CouponTransaction(IdCoupon, IdUser) VALUES (".$coupon_id.",".$user->id.")";
            mysqli_query($conn, $query);
            $query = "UPDATE AccountBalance SET Balance=Balance+".$value." WHERE IdUser=".$user->id;
            mysqli_query($conn, $query);
            $_SESSION['User'] = new User($user->id, $user->email);
            header('Location: /dashboard/');
        }
    }
    else {
        $error="The coupon code is invalid.";
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
        <form action="/account/redeem.php" method="POST">
        <div class="col-md-4 col-lg-4 col-xl-4 mx-auto">
        
        <div class="label-custom text-primary">
        <label for="code" style="margin-left: 5.2rem">Please enter your code:</label>
        </div>
        
            <input type="text" class="form-control" name="code" id="code">
            
            <div class="col-md-4 col-lg-8 col-xl-5 mx-auto" style="margin-top: 4rem; margin-bottom: 1.5rem">   
            <button type="submit" class="btn btn-primary">Send code!</button>
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