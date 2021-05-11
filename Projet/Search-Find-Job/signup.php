<?php
require_once "./php/user_func.inc.php";
require_once "./php/alert.inc.php";
require_once './php/nav.inc.php';

SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));
if(!isset($_SESSION))
{
session_start();
}

if(!isset($_SESSION['user']['loggedIn']))
ChangeLoginState(false);

if(isset($_GET['error']))
SetAlert("error",$_GET['error']);

if(isset($_POST['reset']))
{
	unset($_POST['name']);
	unset($_POST['surname']);
	unset($_POST['email']);
	unset($email);
}
//Si l'utilisateur appuie sur s'inscrire
if (isset($_POST['register'])) 
{
	//Teste que tous les champs sont remplis, sinon affiche une erreur
	if(empty($email) || empty($password) || empty($passwordVer))
	SetAlert("error",6);
	else
	//Teste si l'email existe déjà dans la base de donnée, si oui, affiche une erreur
	if (!VerifyIfMailExists($email)) 
	{
		//REGEX testant l'email pour voir si c'est un email valide
		preg_match("/^([\w\d._\-#])+@([\w\d._\-#]+[.][\w\d._\-#]+)+$/", $email, $matches);
		//Teste que l'email fournit correspond bien aux normes d'un email typique
		if (strlen($email) > 0 && strlen($email) < 45 && $email && $matches != null) 
		{
			//Teste que les 2 mots de passe rentrés sont les mêmes, sinon affiche une erreur
			if ($password == $passwordVer) 
			{
				//Teste si l'inscription est bien effectuée, sinon affiche une erreur
				if (RegisterUser($email, $password, $type) == false)
				SetAlert("error",5);
				else{;
					header('location: index.php');
				}
			} else
			SetAlert("error",4);
		}
		else
		SetAlert("error",9);
	}
	else
	SetAlert("error",3);
	unset($password);
	unset($passwordVer);
} 

if(isset($_POST['password']) && isset($_POST['passwordVerify']))
{
	unset($_POST['password']);
	unset($_POST['passwordVerify']);
}

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>EmployMe | S'inscrire</title>
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

	<!-- Début de section d'inscription -->
	<section class="login-wrapper">
		<div class="container">
			<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
				<form method="POST" action="signup.php">
					<img class="img-responsive" alt="logo" src="img/logo.png">
					<?php 
					 //Affiche une div contenant un message d'erreur
					ShowAlert();
					?>
					<input required type="email" id="email" name="email" class="form-control input-lg" placeholder="Adresse E-mail" value="<?=$email?>">
					<input required type="password" id="pswd" name="password" class="form-control input-lg" placeholder="Mot de Passe">
					<input required type="password" id="pswd2" name="passwordVerify" class="form-control input-lg" placeholder="Confirmez le Mot de Passe">
					<?php 
					//Affiche un select avec tous les types d'utilisateur actuel
					CreateTypeSelect();
					?>
					<fieldset>
						<div class="row">	
							<div class='col'>  
							<input type="reset" name="reset" id="reset" class="form-control btn btn-primary" value="reset"/>
							</div>												
							<div class='col'> 
							<input type="submit" name="register" id="register" class="form-control btn btn-primary" value="s'inscrire" disabled>
							</div>
						</div>
					</fieldset>	

				</form>
			</div>
		</div>
	</section>
	<!-- Fin de section de d'inscription -->
	<?php include_once './php/footer.inc.html'?>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/owl.carousel.min.js"></script>
	<script src="js/bootsnav.js"></script>
	<script src="js/signup.js"></script>
</body>

</html>