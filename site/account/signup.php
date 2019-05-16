<html>
    <head>
        <title>workhorse. - Sign up</title>
    </head>
    <body>
    <form method="POST" action="/api/account/signup.php">
        <input type="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>"/>
        <br>
        <input type="password" name="pass1" />
        <br>
        <input type="password" name="pass2" />
        <br>
        <button type="submit">Sign up!</button>
    </form>
    </body>
</html>