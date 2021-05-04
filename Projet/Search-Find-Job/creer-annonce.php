<?php
require './php/job_func.inc.php';
require_once "./php/pageAccess.inc.php";
$listeCompetences = Array();
$nomEntreprise = filter_input(INPUT_POST,'nomEntreprise',FILTER_SANITIZE_STRING);
$nomPoste = filter_input(INPUT_POST,'nomPoste',FILTER_SANITIZE_STRING);
$nombrePlace = filter_input(INPUT_POST,'nombrePlace',FILTER_SANITIZE_NUMBER_INT);
$adresse = filter_input(INPUT_POST,'adresse',FILTER_SANITIZE_STRING);
$siteweb = filter_input(INPUT_POST,'siteWeb',FILTER_SANITIZE_URL);
$mail = filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL);
$apropos = filter_input(INPUT_POST,'aPropos',FILTER_SANITIZE_STRING);
$salaireMin = filter_input(INPUT_POST,'salaireMin',FILTER_SANITIZE_NUMBER_INT);
$salaireMax = filter_input(INPUT_POST,'salaireMax',FILTER_SANITIZE_NUMBER_INT);

$competence= filter_input(INPUT_POST,'competence',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);
$competenceDesc = filter_input(INPUT_POST,'competenceDesc',FILTER_SANITIZE_STRING,FILTER_REQUIRE_ARRAY);


if(!isset($_SESSION))
{
session_start();
}

if(isset($_POST['supprimerJob']))
{
	header('location: /php/deleteJob.php');
}

$_SESSION['currentPage'] = pathinfo(__FILE__,PATHINFO_FILENAME);
if(!isset($_SESSION['loggedIn']))
$_SESSION['loggedIn'] = false;


if(isset($_POST['register']))
{
	if($_FILES["logo"]['error'] == 0)
	{
		for($i = 0; $i < count($competence);$i++)
		{
			array_push($listeCompetences, ["competence"=>$competence[$i] ,"competenceDesc" =>$competenceDesc[$i]]);
		}
		
		$Orgfilename = $_FILES["logo"]["name"];
		$filename = uniqid();
		$dir = "./tmp/";
		$ext = explode("/",$_FILES["logo"]["type"])[1];
		$file = $filename.'.'.$ext;

		if(in_array($ext,["png","bmp","jpg","jpeg","pdf"]))
		{		
				$createJoResult = CreateNewJob($nomEntreprise,$nomPoste,$nombrePlace,$salaireMin,$salaireMax,$adresse,$siteweb != null ? $siteweb : "",$mail,$apropos,$_SESSION['user']['idUser'],$listeCompetences,($filename.".".$ext));
				if($createJoResult)
				{  
						echo "Job Crée";
					if(move_uploaded_file($_FILES["logo"]["tmp_name"],$dir.$file))
					{  
						header('location: browse-job.php?idU='.$_SESSION['user']['idUser']);
					}  
					else
						echo "Error lors de l'upload de l'image";
				}		
				else
				{
					echo $createJoResult;
					unlink($dir.$file);
				}     
		}
		else
		{				
			echo "Veuillez sélectionner des fichiers valides!";
		}
	}
	else
	{
		
		if($competence != null)
		{
			for($i = 0; $i < count($competence);$i++)
			{
				array_push($listeCompetences, ["competence"=>$competence[$i] ,"competenceDesc" =>$competenceDesc[$i]]);
			}
		}
		else
		$listeCompetences = null;
		error_log($mail);
		$createJoResult = CreateNewJob($nomEntreprise,$nomPoste,$nombrePlace,$salaireMin,$salaireMax,$adresse,$siteweb != null ? $siteweb : "",$mail,$apropos,$_SESSION['user']['idUser'],$listeCompetences,"");
				if($createJoResult)
				{  
						header('location: browse-job.php?idU='.$_SESSION['user']['idUser']);				
				}	
				else
				echo "Erreur lors de la création du job";	
	}
	
}

?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Créer une offre d'emploi</title>
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
	
	<?php include "./php/nav.inc.php"; ?>
		
		<!-- Début section création d'annonce -->
		<section class="jobs">
			<div class="container">
				<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
					<form method="POST" action="job-create.php" enctype="multipart/form-data">		
						<label for="nomAnnonce" >Nom de l'annonce</label>									
                        <input required id="nomAnnonce" type="text" name="nomAnnonce" class="form-control input-lg" placeholder="Nom de l'annonce">
						<label for="description" >Description de votre annonce</label>									
						<textarea required id="description" name="description" placeholder="Description de votre annonce" class="form-control input-lg"></textarea>
						<label for="dateDebut" >Date de début de votre annonce</label>									
						<input required name="dateDebut" id="dateDebut" type="date" class="form-control input-lg">
						<label for="dateFin">Date de fin de votre annonce</label>									
						<input required name="dateFin" id="dateFin"  type="date" class="form-control input-lg" >	
						<label for="tags" >Les tags de votre annonce (Veuillez séparer vos tags par une virgule <i>tag1,tag2,tag3, . . . </i>)</label>									
                        <textarea required id="tags" name="tags" placeholder="Les tags de votre annonce" class="form-control input-lg"></textarea>
                        <label for="media" >Média souhaitant être inclu à votre annonce (Image ou un fichier PDF) (Optionnel)</label>
                        <input id="media" name="media" type="file" accept=".png,.jpg,.jpeg,.pdf" class="form-control input-lg" >
						<fieldset>
						<div class="row">	
							<div class='col'>  
							<input type="reset" name="reset" id="reset" class="form-control btn btn-primary" value="reset"/>
							</div>											
							<div class='col'> 
							<input type="submit" name="register" id="register" class="form-control btn btn-primary">
							</div>
						</div>
						</fieldset>	
						
					</form>
				</div>
			</div>
        </section>
		<!-- Create Job section End -->	
		
		<!-- footer start -->
		<footer>
			<div class="container">
				<div class="col-md-3 col-sm-6">
					<h4>Featured Job</h4>
					<ul>
						<li><a href="#">Browse Jobs</a></li>
						<li><a href="#">Premium MBA Jobs</a></li>
						<li><a href="#">Access Database</a></li>
						<li><a href="#">Manage Responses</a></li>
						<li><a href="#">Report a Problem</a></li>
						<li><a href="#">Mobile Site</a></li>
						<li><a href="#">Jobs by Skill</a></li>
					</ul>
				</div>
				
				<div class="col-md-3 col-sm-6">
					<h4>Latest Job</h4>
					<ul>
						<li><a href="#">Browse Jobs</a></li>
						<li><a href="#">Premium MBA Jobs</a></li>
						<li><a href="#">Access Database</a></li>
						<li><a href="#">Manage Responses</a></li>
						<li><a href="#">Report a Problem</a></li>
						<li><a href="#">Mobile Site</a></li>
						<li><a href="#">Jobs by Skill</a></li>
					</ul>
				</div>
				
				<div class="col-md-3 col-sm-6">
					<h4>Reach Us</h4>
					<address>
					<ul>
					<li>1201, Murakeu Market, QUCH07<br>
					United Kingdon</li>
					<li>Email: Support@job.com</li>
					<li>Call: 044 123 458 65879</li>
					<li>Skype: job@skype</li>
					<li>FAX: 123 456 85</li>
					</ul>
					</address>
				</div>
				
				<div class="col-md-3 col-sm-6">
					<h4>Drop A Mail</h4>
					<form>
						<input type="text" class="form-control input-lg" placeholder="Your Name">
						<input type="text" class="form-control input-lg" placeholder="Email...">
						<textarea class="form-control" placeholder="Message"></textarea>
						<button type="submit" class="btn btn-primary">Login</button>
					</form>
				</div>
				
				
			</div>
			<div class="copy-right">
			 <p>&copy;Copyright 2018 Jober Desk | Design By <a href="https://themezhub.com/">ThemezHub</a></p>
			</div>
		</footer>
		 
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
		<script src="./js/create-job.js"></script>
    </body>
</html>