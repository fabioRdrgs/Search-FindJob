<?php
require_once './php/user_func.inc.php';
if(!isset($_SESSION))
session_start();

if(IsUserLoggedIn())
{
session_destroy();
header("Location:index.php");
}
else
header("Location:index.php");

?>