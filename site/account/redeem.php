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
        }
    }
    else {
        $error="The coupon code is invalid.";
    }
}

?>

<html>

<head>
    <title>workhorse. - Redeem code</title>
</head>
<body>
        <form action="/account/redeem.php" method="POST">
            <input type="text" name="code" id="code">
            <button type="submit">Send code!</button>
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
</body>

</html>