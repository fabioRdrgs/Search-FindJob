<?php
require_once "user_func.inc.php";

$features = "";
$features.= "<section class=\"features\">";
//S'assure que l'utilisateur est connecté pour montrer les vues adéquates
if(IsUserLoggedIn())
{
    //Affiche la vue d'accueil pour l'annonceur
    if(GetUserType()=="Annonceur")
    {
        $features.="<a href=\"creer-annonce.php\">";
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"creer-annonce.php\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-pencil\"></span></span>";
        $features.= 				 "<h3>Créer une annonce</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>";

      
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"annonces.php?idU=".GetUserId()."\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-list\"></span></span>";
        $features.= 				 "<h3>Parcourir mes annonces</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>";

    }
    //Affiche la vue d'accueil pour le chercheur
    else if (GetUserType() =="Chercheur")
    {
       
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"annonces.php\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-list\"></span></span>";
        $features.= 				 "<h3>Parcourir les annonces</h3>";
        $features.=                 "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>";
        $features.="</a>";
       
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"wishlist.php?idU=".GetUserId()."\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-tasks\"></span></span>";
        $features.= 				 "<h3>Gérer ma Wishlist</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>";

    }
    //Affiche la vue d'accueil pour l'admin
    else if(GetUserType()=="Admin")
    {
        $features.="<a href=\"administration.php?gestion=utilisateurs\">";
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"administration.php?gestion=utilisateurs\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-user\"></span></span>";
        $features.= 				 "<h3>Gérer les utilisateurs</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>";

      
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"administration.php?gestion=motscles\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-wrench\"></span></span>";
        $features.= 				 "<h3>Gérer les Mots-Clés</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>"; 
       
    }
}
//Sinon affiche une vue pour tous les utilisateurs non-connectés
else
{
    $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"login.php\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-log-in\"></span></span>";
        $features.= 				 "<h3>Se connecter</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>"; 
        $features.=     "<div class=\"container\">";
        $features.=          "<div class=\"col-sd-4\">";
        $features.="<a href=\"signup.php\">";
        $features.= 				"<div class=\"features-content\">";
        $features.= 				 "<span class=\"box1\"><span aria-hidden=\"true\" class=\"glyphicon glyphicon-edit\"></span></span>";
        $features.= 				 "<h3>Créer un compte</h3>";
        $features.=             "</div>";
        $features.="</a>";
        $features.= 	    "</div>";							
        $features.=     "</div>"; 

}
$features.= "</section>";
//Echo le contenu HTML de l'accueil
echo $features;