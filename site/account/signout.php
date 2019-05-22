<?php

session_start();

unset($_SESSION['User']);
unset($_COOKIE['User']);

header('Location: /');

?>