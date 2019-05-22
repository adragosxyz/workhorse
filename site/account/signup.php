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
        <title>workhorse. - Sign up</title>
    </head>
    <body>
    <form method="POST" action="/account/signup.php">
        <input type="email" name="email" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>"/>
        <br>
        <input type="password" name="pass1" />
        <br>
        <input type="password" name="pass2" />
        <br>
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
        <button type="submit">Sign up!</button>
    </form>
    </body>
</html>