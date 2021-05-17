<?php
require_once './php/nav.inc.php';
require_once './php/annonce_func.inc.php';
require_once './php/alert.inc.php';
require_once './php/pageAccess.inc.php';
require_once './php/admin_func.inc.php';
//Variables 
$idsUtilisateur = filter_input(INPUT_POST,'idUser',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
$typesUtilisateur = filter_input(INPUT_POST,'type',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
$motsDePasseUtilisateur = filter_input(INPUT_POST,'passwordUser',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
$motsClesIdPost = filter_input(INPUT_POST,'idKeyword',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
$motsClesLabelsPost = filter_input(INPUT_POST,'labelsKeywords',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
$newMotsClesPost = filter_input(INPUT_POST,'labelNewKeywords',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
$deleteKeywordCheckboxPost = filter_input(INPUT_POST,'deleteCheckbox',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
//Initialisation
if(!isset($_SESSION))
session_start();
//Définit la page actuelle pour la barre de navigation 
SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));

if(!isset($_GET['limit']))
$_GET['limit'] = 1;

//Si l'utilisateur appuie sur Plus D'annonces, incrémente la limite tout en lançant la même recherche actuelle
if(isset($_POST['plusAnnonces']))
{
	$_GET['limit']++;
	$_POST['rechercher'] = true;
}

//Lorsque le bouton de mise à jour est pressés
if(isset($_POST['updateChanges']))
{
    //Teste le mode de gestion actuel
    if($_GET['gestion'] == "utilisateurs")
    {
        //Teste si tous les ID existent dans la base de donnée, dans le cas échéant affiche une erreur
        if(IsEveryGivenIndexInDB($idsUtilisateur))
        {
            //Teste si tous les types existent dans la BDD, dans le cas échéant affiche une erreur
            if(IsEveryGivenTypeInDB($typesUtilisateur))
            {
                //Va hasher tous les mots de passes qui ont été modifiés
                for ($i=0; $i < count($idsUtilisateur); $i++) 
                { 
                   if(!empty($motsDePasseUtilisateur[$i]))
                   $motsDePasseUtilisateur[$i] = password_hash($motsDePasseUtilisateur[$i], PASSWORD_DEFAULT);           
                }       
                //Va mettre à jour les utilisateurs, dans le cas échéant affiche une erreur
                if(!UpdateUsers($idsUtilisateur,$typesUtilisateur, $motsDePasseUtilisateur))
                SetAlert("error",15);    
            }
            else
            SetAlert("error",14);
        }      
        else
        SetAlert("error",13);
    }
    else if($_GET['gestion'] == "motscles")
    {
        //S'assure que les labels et IDs ont été définis
        if(isset($motsClesLabelsPost) && isset($motsClesIdPost))
        {
            //Teste si tous les labels possèdent quelque chose de rentré afin de ne pas avoir de labels vides
            $emptyLabel = false;
            foreach($motsClesLabelsPost as $label)
            {
                if(empty($label))
                $emptyLabel = true;
            }
            if($emptyLabel)
            SetAlert("error",6);
            else 
            //Mets à jour les Mots-Clés, dans le cas échéant affiche une erreur
                if(!UpdateKeywords($motsClesIdPost,$motsClesLabelsPost))
                SetAlert("error",15);      
        }
        //S'assure que des nouveaux mots-clés ont été fournis
        if(isset($newMotsClesPost))
        {
            //Va ajouter les mots-clés à la base de donnée, dans le cas échéant affiche une erreur
            if(!AddKeywords($newMotsClesPost))
             SetAlert("error",17);
        }
        //S'assure que des mots clés ont été sélectionnés pour être supprimés
        if(isset($deleteKeywordCheckboxPost))
        {      
            //Va supprimer les mots clés fournis,, dans le cas échéant affiche une erreur
            if(!DeleteKeywords($deleteKeywordCheckboxPost))   
             SetAlert("error",16);     
        }
    }

    //Si aucune erreur n'est survenue, affiche un message de succès de la mise à jour
    if(GetAlert()[1] != "error")
    SetAlert("success",2);
}

?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Administration</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
        <!-- All Plugin Css --> 
		<link rel="stylesheet" href="css/plugins.css">
		
		<!-- Style & Common Css --> 
		<link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/main.css">

    </head>
	
    <body>
	
    <?php 
    //Affiche la barre de navigation
    ShowNavBar();
    //Affiche les alerts (S'il y en a)
    ShowAlert();
    ?>
	
	<form method="POST" action="administration.php<?php if(isset($_GET['gestion']))echo "?gestion=".$_GET['gestion'];?>">
	<section class="jobs">
		<div class="container">
			<div class="row heading">
                <?php 

                if(isset($_GET['gestion']))
                {
                    //Change le message dépendant du type de gestion
                    if($_GET['gestion'] == "utilisateurs")
                    echo "<h2>Gestion d'Utilisateurs</h2>
                    <p>Gérez les types et mots de passes des utilisateurs</p>";
                    else if($_GET['gestion'] == "motscles")
                    echo "<h2>Gestion des Mots-Clés</h2>
                    <p>Ajoutez, modifiez ou supprimez des mots clés</p>";
                }?>
			</div>
			<div class="companies">
            <?php if(isset($_GET['gestion']))
									{
										if($_GET['gestion'] == "utilisateurs")
										ShowUserManagement();
										else if($_GET['gestion'] =="motscles")
										ShowKeywordManagement();
									};?>
           
			</div>
            <input class="btn browse-btn" type="submit" name="updateChanges" value="Appliquer les changements"/>
		</div>
	</section>
    </form>

    <?php include_once './php/footer.inc.html'?>
		 
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
		<script src="js/main.js"></script>
        <script src="js/administration.js"></script>
    </body>
</html>