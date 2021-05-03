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
	
<!-- Navigation Start  -->
<nav class="navbar navbar-default navbar-sticky bootsnav">

<div class="container">      
	<!-- Start Header Navigation -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
			<i class="fa fa-bars"></i>
		</button>
		<a class="navbar-brand" href="index.html"><img src="img/logo.png" class="logo" alt=""></a>
	</div>
	<!-- End Header Navigation -->

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="navbar-menu">
		<ul class="nav navbar-nav navbar-right" data-in="fadeInDown" data-out="fadeOutUp">
				<li><a href="index.html">Home</a></li> 
				<li><a href="login.html">Login</a></li>
				<li><a href="companies.html">Companies</a></li> 
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Browse</a>
					<ul class="dropdown-menu animated fadeOutUp" style="display: none; opacity: 1;">
						<li class="active"><a href="browse-job.html">Browse Jobs</a></li>
						<li><a href="company-detail.html">Job Detail</a></li>
						<li><a href="resume.html">Resume Detail</a></li>
					</ul>
				</li>
			</ul>
	</div><!-- /.navbar-collapse -->
</div>   
</nav>
<!-- Navigation End  -->
		
		<!-- login section start -->
		<section class="login-wrapper">
			<div class="container">
				<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
					<form method="POST" action="login.php">
						<img class="img-responsive" alt="logo" src="img/logo.png">
						<input required type="email" name="email"class="form-control input-lg" oninput="" placeholder="Adresse E-mail">
						<input required type="password" name="password" class="form-control input-lg" placeholder="Mot de Passe">
						<fieldset>
						<div class="row">							
							<div class='col'> 
							<input type="submit" name="login" id="login" class="form-control btn btn-primary" >
							</div>
						</div>
						</fieldset>	
						<p>Vous n'avez pas de compte ? <a href="./signup.php">Créez un compte</a></p>
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

    </body>
</html>