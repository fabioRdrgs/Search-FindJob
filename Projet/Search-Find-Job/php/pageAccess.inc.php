<?php
// Nom de la page chargée (sans l'extension)
require_once './php/user_func.inc.php';
require_once './php/error.inc.php';
$script = basename($_SERVER['SCRIPT_NAME'], '.php');
if(!isset($_SESSION))
{
session_start();
}

if(!isset($_SESSION['user']['loggedIn']))
ChangeLoginState(false);

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
    if($script == "administration" && GetUserType() != "Admin")
    {
        header('location: index.php?error=7');
        die("Vous n'avez pas accès à cette page");
    }
}
//Si on n'est pas connecté, accède les tests de d'accès de page correspondant à l'état non connecté de l'utilisateur
else
{
    //Si on essaie d'accéder à la page parcourir job avec un id fournit pour voir les jobs crées par une personne
    //Et que l'id de l'utilisateur actuel ne correspond pas à celui fournit, renvoie à l'index
    if($script == "creer-annonce")
    {
        header('location: login.php?error=2');
        die("Vous n'avez pas accès à cette page");
    }
    if($script == "wishlist")
    {
        header('location: login.php?error=2');
        die("Vous n'avez pas accès à cette page");
    }
    if($script == "administration")
    {
        header('location: login.php?error=2');
        die("Vous n'avez pas accès à cette page");
    }

}


