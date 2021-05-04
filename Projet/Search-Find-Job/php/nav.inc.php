<?php
require_once './php/user_func.inc.php';
if (!isset($_SESSION)) {
    session_start();
}
function SetCurrentPage($currentPage)
{
    $_SESSION['currentPage'] = $currentPage;
}
function SetActivePage($page)
{
    if ($_SESSION['currentPage'] == $page) return "class=\"active\"";
}
function ShowNavBar()
{
    $navBar = "";
    //Navigation Start
    $navBar .= "<nav class=\"navbar navbar-default navbar-sticky bootsnav\">
    <div class=\"container\">";
    // Début Header Navigation
    $navBar .= "
        <div class=\"navbar-header\">
            <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-menu\">
                <i class=\"fa fa-bars\"></i>
            </button>
            <a class=\"navbar-brand\" href=\"index.php\"><img src=\"./img/logo.png\" class=\"logo\" alt=\"Logo du site\"></a>
        </div>";
    // Fin Header Navigation


    $navBar .= "<div class=\"collapse navbar-collapse\" id=\"navbar-menu\">
            <ul class=\"nav navbar-nav navbar-left\" data-in=\"fadeInDown\" data-out=\"fadeOutUp\">
                <li ".SetActivePage("index") . "><a href=\"index.php\">Accueil</a></li>";
    if (!IsUserLoggedIn()) {
        $navBar .= "<li " .
            SetActivePage("login") . ">
                        <a href=\"login.php\">Se connecter</a>
                        </li>
                        <li " .
            SetActivePage("signup") . ">
                        <a href=\"signup.php\">S'inscrire</a>
                        </li>";
    }
     else 
    {
        switch (GetUserType()) {
            case "Chercheur":
                $navBar .= "<li " .
                    SetActivePage("annonces") . ">
                                <a href=\"annonces.php\">Annonces</a>
                                </li>
                                <li " .
                    SetActivePage("wishlist") . ">
                                <a href=\"wishlist.php\">Ma Wishlist</a>
                                </li>";
                break;
            case "Annonceur":
                $navBar .= "<li ";
                if (isset($_GET['idU']))
                    $navBar .= SetActivePage("annonces");
                $navBar .= ">
                                <a href=\"annonces.php?idU=" . GetUserId() . "\">Mes Annonces</a>
                                </li>
                                <li " .
                    SetActivePage("creer-annonce") . ">
                                <a href=\"creer-annonce.php\">Créer une annonce</a>
                                </li>";
                break;
                case "Admin":
                     $navBar .= "<li ";
                   if(isset($_GET['gestion']) && $_GET['gestion'] == "utilisateurs") $navBar .=SetActivePage("administration");  $navBar .= ">
                                <a href=\"administration.php?gestion=utilisateurs\">Gérer les utilisateurs</a>
                                </li>
                                <li ";
                    if(isset($_GET['gestion']) && $_GET['gestion'] == "motscles") $navBar .= SetActivePage("administration"); $navBar .= ">
                                <a href=\"administration.php?gestion=motscles\">Gérer les mots clés</a>
                                </li>";
                    break;
        }


        $navBar .= "</ul>
                    <ul class=\"nav navbar-nav navbar-right\" data-in=\"fadeInDown\" data-out=\"fadeOutUp\">           	
                        <li><a href=\"logout.php\">Se déconnecter</a></li>";
    }

    $navBar .= "</ul>
        </div><!-- /.navbar-collapse -->
        </div>   
        </nav>";

    //fin de la barre de navigation
    echo $navBar;
}
