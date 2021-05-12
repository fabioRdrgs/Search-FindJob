<?php
require_once './php/annonce_func.inc.php';
require_once "./php/pageAccess.inc.php";
require_once './php/alert.inc.php';
require_once './php/nav.inc.php';

//Définit la page actuelle pour la barre de navigation 
SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));

$nomAnnonce = filter_input(INPUT_POST,'nomAnnonce',FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST,'description',FILTER_SANITIZE_STRING);
$dateDebut = filter_input(INPUT_POST,'dateDebut',FILTER_SANITIZE_STRING);
$dateFin = filter_input(INPUT_POST,'dateFin',FILTER_SANITIZE_STRING);
$motsClesSelectPost = filter_input(INPUT_POST,'motsClesSelect',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);

if(isset($_POST['creer']))
{
	//Teste si tous les champs sont remplis, sinon affiche une erreur
	if(!empty($nomAnnonce) && !empty($description) && !empty($dateDebut) && !empty($dateFin) && isset($motsClesSelectPost))
	{
		//Prépare le média fournit à l'upload
		$media = CheckMedia();
		//Si aucune erreur n'est survenue, procède à la création de l'annonce
		if(GetAlert()[1]!= "error")
		{
			$dir = $media[0];
			$filename = $media[1];
			$type = $media[2];
			$createAnnonceResult = CreerAnnonce($nomAnnonce,$description,$dateDebut,$dateFin,$motsClesSelectPost,$dir,$filename,$type,GetUserId());
			//Si la création de l'annonce a été effectuée avec succès, procède vers l'upload du média si fournit
			if(isset($createAnnonceResult) && $createAnnonceResult)
			{
				//Va effectuer l'upload du média sur le serveur si un média a été fournit
				if($_FILES["media"]['error'] == 0)
				{
					//Si l'upload a été effectué avec succès, renvoie à la page d'annonces, sinon, affiche une erreur
					if(UploadMedia($dir,$filename,$type))
					{
						header('location: annonces.php?idU='.GetUserId().'&alert=success&num=3');
					}
					else
					SetAlert("error",5);
				}
				//Sinon, effectue une simple redirection
				else
				header('location: annonces.php?idU='.GetUserId().'&alert=success&num=3');
			}	
			else
			SetAlert("error",19);	
		}
		
	}
	else
	SetAlert("error",6);
}
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Créer une annonce | Search & Find Job</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <!-- All Plugin Css --> 
		<link rel="stylesheet" href="css/plugins.css">
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
		<!-- Style & Common Css --> 
		<link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/main.css">

    </head>
	
    <body>
	

	<?php ShowNavBar();?>
		
		<!-- Début section création d'annonce -->
		<section class="jobs">
			<div class="container">
				<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
					
				<?php ShowAlert()?>
				<div class="row heading">
					<h2>Créez votre annonce</h2>
				</div>
					<form method="POST" action="creer-annonce.php" enctype="multipart/form-data">		
						<label for="nomAnnonce" >Nom de l'annonce</label>									
                        <input required id="nomAnnonce" type="text" name="nomAnnonce" class="form-control input-lg" placeholder="Nom de l'annonce" value="<?=$nomAnnonce?>">
						<label for="description" >Description de votre annonce</label>									
						<textarea required id="description" name="description" placeholder="Description de votre annonce" class="form-control input-lg"><?=$description?></textarea>
						<label for="dateDebut" >Date de début de votre annonce</label>									
						<input required name="dateDebut" id="dateDebut" type="date" class="form-control input-lg" value="<?=$dateDebut?>">
						<label for="dateFin">Date de fin de votre annonce</label>									
						<input required name="dateFin" id="dateFin"  type="date" class="form-control input-lg" value="<?=$dateFin?>">	
						<label for="motsClesSelect" >Les tags de votre annonce (Veuillez en sélectionner 1 à plusieurs)</label>									
						<?php
						ShowSelectKeywords($motsClesSelectPost);
						?>
                        <label for="media" >Média souhaitant être inclu à votre annonce (Image ou un fichier PDF) (Optionnel)</label>
                        <input id="media" name="media" type="file" accept=".png,.jpg,.jpeg,.pdf" class="form-control input-lg" >
						<fieldset>
						<div class="row">	
							<div class='col'>  
							<input type="reset" name="reset" id="reset" class="form-control btn btn-primary" value="reset"/>
							</div>											
							<div class='col'> 
							<input type="submit" name="creer" id="creer" class="form-control btn btn-primary" value="Créer annonce">
							</div>
						</div>
						</fieldset>	
						
					</form>
				</div>
			</div>
        </section>
		<!-- Create Job section End -->	
		
		<?php include_once './php/footer.inc.html'?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  		<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
		<script src="./js/create-job.js"></script>
    </body>
</html>