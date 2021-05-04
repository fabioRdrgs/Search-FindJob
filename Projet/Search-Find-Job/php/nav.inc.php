<?php
require_once './php/user_func.inc.php';
if(!isset($_SESSION))
{
session_start();
}
function SetCurrentPage($currentPage)
{
    $_SESSION['currentPage'] = $currentPage;
}
function SetActivePage($page)
{
    if($_SESSION['currentPage'] == $page)echo "class=\"active\"";
}
function ShowNavBar()
{
        //Navigation Start
        echo"
    <nav class=\"navbar navbar-default navbar-sticky bootsnav\">

    <div class=\"container\">";    
        // Début Header Navigation
        echo"
        <div class=\"navbar-header\">
            <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-menu\">
                <i class=\"fa fa-bars\"></i>
            </button>
            <a class=\"navbar-brand\" href=\"index.php\"><img src=\"./img/logo.png\" class=\"logo\" alt=\"Logo du site\"></a>
        </div>";

        // Fin Header Navigation


        echo"<div class=\"collapse navbar-collapse\" id=\"navbar-menu\">
            <ul class=\"nav navbar-nav navbar-left\" data-in=\"fadeInDown\" data-out=\"fadeOutUp\">

                    <li ";SetActivePage("index"); echo"><a href=\"index.php\">Accueil</a></li>";

                    
                    if(!IsUserLoggedIn())
                    {
                        echo "<li ";SetActivePage("login"); echo">
                        <a href=\"login.php\">Se connecter</a>
                        </li>
                        <li ";  SetActivePage("signup"); echo">
                        <a href=\"signup.php\">S'inscrire</a>
                        </li>";
                    }
                    else
                    {
                        switch(GetUserType())
                        {
                            case "Chercheur":
                                echo "<li "; SetActivePage("annonces");echo ">
                                <a href=\"annonces.php\">Annonces</a>
                                </li>
                                <li "; SetActivePage("wishlist");echo ">
                                <a href=\"wishlist.php\">Ma Wishlist</a>
                                </li>";
                                break;
                                case "Annonceur":
                                echo "<li "; if(isset($_GET['idU']))SetActivePage("annonces");echo ">
                                <a href=\"annonces.php?idU=".GetUserId()."\">Mes Annonces</a>
                                </li>
                                <li "; SetActivePage("creer-annonce");echo ">
                                <a href=\"creer-annonce.php\">Créer une annonce</a>
                                </li>";
                                    break;
                        }
                        
               
                    echo"</ul>
                    <ul class=\"nav navbar-nav navbar-right\" data-in=\"fadeInDown\" data-out=\"fadeOutUp\">           	
                        <li><a href=\"logout.php\">Se déconnecter</a></li>";
                    }
                    				
                echo "</ul>
        </div><!-- /.navbar-collapse -->
        </div>   
        </nav>";

        //fin de la barre de navigation
}