<?php
if(!isset($_SESSION))
{
session_start();
}

if(!isset($_SESSION['loggedIn']))
$_SESSION['loggedIn'] = false;
if(!is_null($_GET['error']))
SetError($_GET['error']);
require_once "./php/user_func.inc.php";
require_once "./php/error.inc.php";
if(isset($_POST['reset']))
{
	unset($_POST['name']);
	unset($_POST['surname']);
	unset($_POST['email']);
	unset($email);
}

if (isset($_POST['register'])) 
{
	if($email == "" || is_null($email) )
	SetError(6);
	else
	if (!VerifyIfMailExists($email)) 
	{	SetError(0);
		preg_match("/^([\w\d._\-#])+@([\w\d._\-#]+[.][\w\d._\-#]+)+$/", $email, $matches);
		if (strlen($email) > 0 && strlen($email) < 45 && strlen($password) > 6 && $email && $matches != null) 
		{
			SetError(0);
			if ($password == $passwordVer) 
			{
				SetError(0);
				if (RegisterUser($email, $password, $type) == false)
				SetError(5);
				else{
					echo "<p style=\"color:red\">Inscription réussie</p>";
					header('location: index.php');
				}
			} else
			SetError(4);
		}
	}
	else
	SetError(3);

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


	<!-- login section start -->
	<section class="login-wrapper">
		<div class="container">
			<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
				<form method="POST" action="signup.php">
					<img class="img-responsive" alt="logo" src="img/logo.png">
					<?php ShowError();?>
					<input required type="email" id="email" name="email" class="form-control input-lg" placeholder="Adresse E-mail" value="<?=$email?>">
					<input required type="password" id="pswd" name="password" class="form-control input-lg" placeholder="Mot de Passe">
					<input required type="password" id="pswd2" name="passwordVerify" class="form-control input-lg" placeholder="Confirmez le Mot de Passe">
					<?php CreateTypeSelect();?>
					<fieldset>
						<div class="row">	
							<div class='col'>  
							<input type="reset" name="reset" id="reset" class="form-control btn btn-primary" value="reset"/>
							</div>												
							<div class='col'> 
							<input type="submit" name="register" id="register" class="form-control btn btn-primary" disabled>
							</div>
						</div>
						</fieldset>	

				</form>
			</div>
		</div>
	</section>
	<!-- login section End -->

	<?php include_once './php/footer.inc.html'?>

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/owl.carousel.min.js"></script>
	<script src="js/bootsnav.js"></script>
	<script src="js/signup.js"></script>
</body>

</html>