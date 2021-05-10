<?php
require_once './php/annonce_func.inc.php';
require_once './php/user_func.inc.php';
require_once './php/pageAccess.inc.php';
if(!isset($_SESSION))
{
session_start();
}
  $annonce =  GetAnnonceInfo($_GET['idA']);
  $image = $annonce[6].$annonce[7].".".$annonce[8];
    if(DeleteAnnonce($_GET['idA'],$_GET['idU']))
    {       
          unlink($image);
          header('location: annonces.php');
    }
    else
      header('location: annonces.php?idU='.$_GET['idU']."&error=10");

