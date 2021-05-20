<?php
require_once './php/user_func.inc.php';
if (!isset($_SESSION)) {
    session_start();
}
/**
 * Permet de mettre en surbrillance le lien dépendant de la page sur laquelle l'utilisateur se trouve
 *
 * @param string $currentPage
 * @return void
 */
function SetCurrentPage($currentPage)
{
    $_SESSION['currentPage'] = $currentPage;
}
/**
 * Permet de mettre en surbrillance le lien dépendant de la page sur laquelle l'utilisateur se trouve
 *
 * @param string $page
 * @return string/void Retourne un string "active" ou non dépendant de la page actuelle
 */
function SetActivePage($page)
{
    if ($_SESSION['currentPage'] == $page) return "class=\"active\"";
}
/**
 * Affiche la barre de navigation
 *
 * @return void Echo le contenu HTML de la barre de navigation
 */
function ShowNavBar()
{
    $navBar = "";
    //Navigation Start
    $navBar .= "<nav class=\"navbar navbar-default navbar-sticky bootsnav\">";
    $navBar .="<div class=\"container\">";
    // Début Header Navigation
    $navBar .= "<div class=\"navbar-header\">";
    $navBar .="<button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-menu\">";
    $navBar .=  "<i class=\"fa fa-bars\"></i>";
    $navBar .=   "</button>";
    $navBar .=     "<a class=\"navbar-brand\" href=\"index.php\"><img src=\"./img/logo.png\" class=\"logo\" alt=\"Logo du site\"></a>";
    $navBar .="</div>";
    // Fin Header Navigation


    $navBar .= "<div class=\"collapse navbar-collapse\" id=\"navbar-menu\">";
    $navBar .=        "<ul class=\"nav navbar-nav navbar-left\" data-in=\"fadeInDown\" data-out=\"fadeOutUp\">";
    $navBar .=            "<li ".SetActivePage("index") . "><a href=\"index.php\">Accueil</a></li>";
    //Affiche les liens de connexion et d'inscription si l'utilisateur n'est pas connecté
    if (!IsUserLoggedIn()) {
        $navBar .= "<li " .SetActivePage("login") . ">";
        $navBar .="<a href=\"login.php\">Se connecter</a>";
        $navBar .="</li>";
        $navBar .=    "<li " .SetActivePage("signup"). ">";
        $navBar .=  "<a href=\"signup.php\">S'inscrire</a>";
        $navBar .=               "</li>";
    }
    //Sinon affiche les liens correspondant au type de l'utilisateur
     else 
    {
        switch (GetUserType()) 
        {
            //Affiche les liens correspondant au type Chercheur
            case "Chercheur":
                $navBar .= "<li " .SetActivePage("annonces") . ">";
                $navBar.=     "<a href=\"annonces.php\">Annonces</a>";
                $navBar.=  "</li>";
                $navBar.=  "<li " .SetActivePage("wishlist") . ">";
                $navBar.=       "<a href=\"wishlist.php?idU=".GetUserId()."\">Ma Wishlist</a>";
                $navBar.=  "</li>";
                break;
            //Affiche les liens correspondant au type Annonceur
            case "Annonceur":
                $navBar .= "<li ";
                if (isset($_GET['idU']))
                $navBar .= SetActivePage("annonces");
                $navBar .= ">";
                $navBar.="<a href=\"annonces.php?idU=" . GetUserId() . "\">Mes Annonces</a>";
                $navBar.="</li>";
                $navBar.="<li " .SetActivePage("creer-annonce"). ">";
                $navBar.="<a href=\"creer-annonce.php\">Créer une annonce</a>";
                $navBar.="</li>";
                break;
                //Affiche les liens correspondant au type Administrateur
            case "Admin":
                $navBar .= "<li ";
                if(isset($_GET['gestion']) && $_GET['gestion'] == "utilisateurs") 
                $navBar .=SetActivePage("administration");  
                $navBar .= ">";
                $navBar.="<a href=\"administration.php?gestion=utilisateurs\">Gérer les utilisateurs</a>";
                $navBar.="</li>";
                $navBar.="<li ";
                if(isset($_GET['gestion']) && $_GET['gestion'] == "motscles") $navBar .= SetActivePage("administration"); 
                $navBar .= ">";
                $navBar.="<a href=\"administration.php?gestion=motscles\">Gérer les mots-clés</a>";
                $navBar.=" </li>";
                break;
        }
       
        $navBar .= "</ul>";
        $navBar.="<ul class=\"nav navbar-nav navbar-right\" data-in=\"fadeInDown\" data-out=\"fadeOutUp\">";           	
        $navBar.="<li><a href=\"logout.php\">Se déconnecter</a></li>";
    }
    $navBar .= "<li ".SetActivePage("faq").">";
    $navBar .= "<a href=\"faq.php\">Aide</a>";
    $navBar.=  "</li>";
    $navBar .= "</ul>";
    $navBar.="</div>";
    $navBar.= "</div>" ;
    $navBar.="</nav>";

    //Echo le contenu HTML de la barre de navigation
    echo $navBar;
}
