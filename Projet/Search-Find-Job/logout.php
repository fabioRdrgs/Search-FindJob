<?php
require_once './php/user_func.inc.php';
if(!isset($_SESSION))
session_start();

if(IsUserLoggedIn())
{
session_destroy();
header("Location:login.php");
}
else
header("Location:login.php");

?>