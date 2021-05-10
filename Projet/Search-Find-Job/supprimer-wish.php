<?php
require_once './php/annonce_func.inc.php';
require_once './php/user_func.inc.php';
require_once './php/pageAccess.inc.php';
if(!isset($_SESSION))
{
session_start();
}
    if(RemoveWish($_GET['idU'],$_GET['idA']))    
      header('location: wishlist.php?idU='.$_GET['idU']);   
    else
      header('location: wishlist.php?idU='.$_GET['idU']."&error=12");

