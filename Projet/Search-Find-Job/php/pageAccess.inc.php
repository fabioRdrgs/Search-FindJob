<?php
require_once './php/user_func.inc.php';
require_once './php/alert.inc.php';
// Nom de la page chargée (sans l'extension)
$script = basename($_SERVER['SCRIPT_NAME'], '.php');
$gestionDisponibles = ["motscles","utilisateurs"];
//S'assure que la session soit démarrée à chaque fois
if(!isset($_SESSION))
{
session_start();
}

if(!isset($_SESSION['user']['loggedIn']))
ChangeLoginState(false);


//Si on est connecté, accède les tests de d'accès de page correspondant à l'état connecté de l'utilisateur
 if(IsUserLoggedIn())
{
    //Si l'utilisateur est connecté, il n'aura pas accès aux pages de connexion ou d'inscription
    if ($script == 'login' || $script == "signup")
    {
        header('location: index.php');
        die("Vous n'avez pas accès à cette page");
    }
    else
    //Si l'utilisateur est un annonceur
    if(GetUserType() == "Annonceur")
    {
        //Il ne pourra aller que sur les pages "annonces","creer-annonce","index","annonce","modifier-annonce" et supprimer-annonce
        if($script != "annonces" && $script != "creer-annonce" && $script != "index" && $script != "annonce" && $script != "modifier-annonce" && $script != "supprimer-annonce" && $script != 'logout')
        {
            header('location: index.php?alert=error&num=7');
            die("Vous n'avez pas accès à cette page");
        }
        //Il ne pourra pas aller sur la page annonces si l'id utilisateur n'est pas fournit en GET ou
        //que l'id ne correspond pas à son id
        if($script =="annonces")
        {
            if(!isset($_GET['idU'])|| $_GET['idU'] != GetUserId())
            {
                header('location: annonces.php?idU='.GetUserId());
                die("Vous n'avez pas accès à cette page");
            }
            
        }
        //Il ne pourra pas aller sur la page supprimer annonce si l'id utilisateur n'est pas fournit en GET, 
        //qu'il ne correspond pas à l'id de l'utilisateur ou que l'id de l'annonce ne soit pas définie en GET
        if($script == "supprimer-annonce")
        {
            if(!isset($_GET['idU']) || $_GET['idU'] != GetUserId() || !isset($_GET['idA']))
            {
                header('location: index.php');
                die("Vous n'avez pas accès à cette page");
            }
        }
        if($script == "modifier-annonce")
        {
            if(!isset($_GET['idA']))
            {
                header('location: annonces.php?idU='.GetUserId().'&alert=error&num=20');
                die("Vous n'avez pas accès à cette page");
            }
            else
            {
                $GetAnnonceInfo  = GetAnnonceInfo($_GET['idA']);
                if(!isset($_GET['idA']) || !isset($_GET['idU']) || $_GET['idU'] != GetUserId() || $GetAnnonceInfo[9] != GetUserId())
                {
                    header('location: annonces.php?idU='.GetUserId().'&alert=error&num=7');
                    die("Vous n'avez pas accès à cette page");
                }
            }
        }
    }
    else
    //Si l'utilisateur est un chercheur
    if(GetUserType() == "Chercheur")
    {
        //Il ne pourra aller que sur les pages "annonces","annonce","index" et "wishlist"
        if($script != "annonces" && $script != "annonce" && $script != "index" && $script != "wishlist" && $script != "logout" && $script != "supprimer-wish")
        {
            header('location: index.php?alert=error&num=7');
            die("Vous n'avez pas accès à cette page");
        }
        //Il ne pourra pas aller sur la page wishlist si l'id utilisateur n'est pas fournit en GET
        if($script == "wishlist" )
        {
            if(!isset($_GET['idU']) || $_GET['idU'] != GetUserId())
            {
                header('location: wishlist.php?idU='.GetUserId());
                die("Vous n'avez pas accès à cette page");
            }
        }
    }
    else
    //Si l'utilisateur est un admin
    if(GetUserType() == "Admin")
    {
        //Il ne pourra pas aller que sur les pages "administration" et "index"
        if($script != "administration" && $script != "index" && $script != 'logout')
        {
            header('location: index.php');
            die("Vous n'avez pas accès à cette page");
        }
        //Il ne pourra aller sur la page administration si le type de gestion n'est pas fournit en GET
        if($script == "administration" && !isset($_GET['gestion']))
        {
            header('location: index.php');
            die("Vous n'avez pas accès à cette page");
        }
        else if(!in_array($_GET['gestion'],$gestionDisponibles))
        {
            header('location: index.php');
            die("Vous n'avez pas accès à cette page");
        }
    }
    //Aucun utilisateur ne pourra aller sur la page annonce si l'id de l'annonce n'est pas fournit en GET
    if($script == "annonce" && !isset($_GET['idA']))
    {
        header('location: index.php');
        die("Vous n'avez pas accès à cette page");
    }
}
//Si on n'est pas connecté, accède les tests de d'accès de page correspondant à l'état non connecté de l'utilisateur
else
{
    //Si l'utilisateur non connecté tente d'accéder à tout autre page qu'index, signup ou login, il est renvoyé à l'index
    if($script != "index" && $script != "login" && $script != "signup")
    {
        header('location: login.php?alert=error&num=2');
        die("Vous n'avez pas accès à cette page");
    }
}