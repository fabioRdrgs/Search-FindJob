<?php
if(!isset($_SESSION))
{
session_start();
}
require_once './php/alert.inc.php';
require_once './php/user_func.inc.php';
require_once './php/nav.inc.php';
require_once './php/pageAccess.inc.php';
SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));

//Permet d'afficher l'erreur adéquate si une erreur a été envoyée en GET par une autre page
if(isset($_GET['error']))
SetAlert("error",$_GET['error']);

$_SESSION['currentPage'] = pathinfo(__FILE__,PATHINFO_FILENAME);
//Lorsque l'utilisateur appuie sur se connecter
if(isset($_POST['login']))
//Teste si tous les champs sont remplis, sinon affiche une erreur
if(empty($email) || empty($password))
SetAlert("error",6);
else
//Teste si la connexion avec les données fournies réussie et connecte l'utilisateur avant de le renvoyer à l'accueil
//Sinon, affiche une erreur
if(!ConnectUser($email,$password))
	SetAlert("error",1);
	else
	header('location: index.php');
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>EmployMe | Se connecter</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <!-- All Plugin Css --> 
		<link rel="stylesheet" href="css/plugins.css">
		
		<!-- Style & Common Css --> 
		<link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/main.css">

    </head>
	
    <body>
	<?php ShowNavBar();?>
		
		<!-- Début de section de login -->
		<section class="login-wrapper">
			<div class="container">
				<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
					<form method="POST" action="login.php">
						<img class="img-responsive" alt="logo" src="img/logo.png">
						<?php
						 //Affiche une div contenant un message d'erreur
						 ShowAlert();
						  ?>
						<input required type="email" name="email"class="form-control input-lg" oninput="" placeholder="Adresse E-mail" value="<?=$email?>">
						<input required type="password" name="password" class="form-control input-lg" placeholder="Mot de Passe">
						<fieldset>
						<div class="row">	
							<div class='col'>  
							<input type="reset" name="reset" id="reset" class="form-control btn btn-primary" value="reset"/>
							</div>											
							<div class='col'> 
							<input type="submit" name="login" id="login" class="form-control btn btn-primary" value="se connecter" >
							</div>
						</div>
						</fieldset>	
						<p>Vous n'avez pas de compte ? <a href="./signup.php">Créez un compte</a></p>
					</form>
				</div>
			</div>
		</section>
		<!-- Fin de section de login -->	
		
		<?php include_once './php/footer.inc.html'?>
		 
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
    </body>
</html>