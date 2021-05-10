<?php
require_once './php/nav.inc.php';
require_once './php/annonce_func.inc.php';
require_once './php/alert.inc.php';
require_once './php/pageAccess.inc.php';
require_once './php/admin_func.inc.php';
$idsUtilisateur = filter_input(INPUT_POST,'idUser',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
$typesUtilisateur = filter_input(INPUT_POST,'type',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
$motsDePasseUtilisateur = filter_input(INPUT_POST,'passwordUser',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
if(!isset($_SESSION))
session_start();
SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));
if(isset($_GET['error']))
SetAlert("error",$_GET['error']);

if(!isset($_GET['limit']))
$_GET['limit'] = 1;
if(isset($_POST['plusAnnonces']))
{
	$_GET['limit']++;
	$_POST['rechercher'] = true;
}

if(isset($_POST['updateChanges']))
{
    if(IsEveryGivenIndexInDB($idsUtilisateur))
    {
        if(IsEveryGivenTypeInDB($typesUtilisateur))
        {
            for ($i=0; $i < count($idsUtilisateur); $i++) { 
                if(!empty($motsDePasseUtilisateur[$i]))
                $motDePassHash = password_hash($motsDePasseUtilisateur[$i], PASSWORD_DEFAULT);

                if(!UpdateUser($idsUtilisateur[$i],$typesUtilisateur[$i], $motDePassHash))
                return SetAlert("error",15);
            }       
            SetAlert("success",2);
        }
        else
        SetAlert("error",14);
    }      
    else
    SetAlert("error",13);
}

?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Jober Desk | Responsive Job Portal Template</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		
        <!-- All Plugin Css --> 
		<link rel="stylesheet" href="css/plugins.css">
		
		<!-- Style & Common Css --> 
		<link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/main.css">

    </head>
	
    <body>
	
    <?php ShowNavBar();
    ShowAlert();?>
	
	<form method="POST" action="administration.php<?php if(isset($_GET['gestion']))echo "?gestion=".$_GET['gestion'];?>">
	<section class="jobs">
		<div class="container">
			<div class="row heading">
                <?php if(isset($_GET['gestion']))
                {
                    if($_GET['gestion'] == "utilisateurs")
                    echo "<h2>Gestion d'Utilisateurs</h2>
                    <p>Gérer les types et mots de passes des utilisateurs</p>";
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
										echo "";
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
    </body>
</html>