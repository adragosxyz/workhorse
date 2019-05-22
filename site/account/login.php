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
        <title>workhorse. - Login</title>
    </head>
    <body>
    <form method="POST" action="/account/login.php">
        <input type="email" name="email"/>
        <br>
        <input type="password" name="pass" />
        <br>
        <?php 
        if (isset($_GET['signup'])) {
        ?>
        <div class="alert alert-success" role="alert">
            Your account was registered! Please log in now.
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
        <button type="submit">Login!</button>
    </form>

    </body>
</html>