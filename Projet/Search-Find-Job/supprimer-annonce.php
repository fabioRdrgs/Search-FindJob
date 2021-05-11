<?php
require_once './php/annonce_func.inc.php';
require_once './php/user_func.inc.php';
require_once './php/pageAccess.inc.php';
if(!isset($_SESSION))
{
session_start();
}
  $annonce =  GetAnnonceInfo($_GET['idA']);
  if(!empty($annonce[6])&&!empty($annonce[7])&&!empty($annonce[8]))
  $image = $annonce[6].$annonce[7].".".$annonce[8];
    if(DeleteAnnonce($_GET['idA'],$_GET['idU']))
    {       
      if(!empty($annonce[6])&&!empty($annonce[7])&&!empty($annonce[8]))
          unlink($image);
          
          header('location: annonces.php');
    }
    else
      header('location: annonces.php?idU='.$_GET['idU']."&error=10");

