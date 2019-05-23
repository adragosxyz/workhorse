<?php 

include '../debug.php';
include '../config/dep.php';

session_start();
if (!isset($_SESSION['User']) || $_SESSION['User']===null) {
  header('Location: /account/login.php');
  exit();
}

$user = $_SESSION['User'];

if (isset($_POST['delete']))
{
    $id_ssh = (int)$_POST['delete'];
    $query = "DELETE FROM SSH";
}

?>

