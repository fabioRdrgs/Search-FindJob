<?php
// Nom de la page chargée (sans l'extension)

//REGROUPER PAR IFs DE TYPE USER!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
require_once './php/user_func.inc.php';
require_once './php/alert.inc.php';
$script = basename($_SERVER['SCRIPT_NAME'], '.php');
if(!isset($_SESSION))
{
session_start();
}

if(!isset($_SESSION['user']['loggedIn']))
ChangeLoginState(false);



if($script == "annonce" && !isset($_GET['idA']))
{
    header('location: index.php');
        die("Vous n'avez pas accès à cette page");
}
//Si on est connecté, accède les tests de d'accès de page correspondant à l'état connecté de l'utilisateur
 if(IsUserLoggedIn())
{
    // Vérifier si elle est dans la liste des droits.
    // Toujours permettre l'accès à index
    if ($script == 'login'|| $script == "signup")
    {
        header('location: index.php');
        die("Vous n'avez pas accès à cette page");
    }
    if($script == "administration" && GetUserType() != "Admin" )
    {
        header('location: index.php?error=7');
        die("Vous n'avez pas accès à cette page");
    }else if($script =="administration" && !isset($_GET['gestion']))
    {
        header('location: index.php?error=7');
        die("Vous n'avez pas accès à cette page");
    }
    if($script == "creer-annonce" && GetUserType() != "Annonceur")
    {
        header('location: index.php');
    die("Vous n'avez pas accès à cette page");
    }
    if($script == "supprimer-annonce" &&  GetUserType() != "Annonceur")
    {
        header('location: index.php');
        die("Vous n'avez pas accès à cette page");
    }
    else if($script == "supprimer-annonce")
    {
        if(!isset($_GET['idU']) || $_GET['idU'] != GetUserId() || !isset($_GET['idA']))
        {
            header('location: index.php');
            die("Vous n'avez pas accès à cette page");
        }
    }
    if($script=="wishlist" && !isset($_GET['idU']))
    {
        header('location: index.php');
        die("Vous n'avez pas accès à cette page");
    }
    else if($script=="wishlist" && GetUserType() != "Chercheur")
    {
        header('location: index.php?error=7');
        die("Vous n'avez pas accès à cette page");
    }
    if($script=="annonces" && isset($_GET['idU'])&&GetUserType()!="Annonceur")
    {
        header('location: annonces.php');
        die("Vous n'avez pas accès à cette page");
    }
}
//Si on n'est pas connecté, accède les tests de d'accès de page correspondant à l'état non connecté de l'utilisateur
else
{
    //Si l'utilisateur non connecté tente d'accéder à tout autre page qu'index, signup ou login, il est renvoyé à l'index
    if($script != "index" || $script != "login" || $script != "signup")
    {
        header('location: login.php?error=2');
        die("Vous n'avez pas accès à cette page");
    }
    
}


