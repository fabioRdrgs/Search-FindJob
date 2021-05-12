<?php
require_once './php/user_func.inc.php';
require_once './php/pageAccess.inc.php';

if(IsUserLoggedIn())
{
session_destroy();
header("Location:index.php");
}
else
header("Location:index.php");

?>