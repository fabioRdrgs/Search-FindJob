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
$supprimerMediaActuel = filter_input(INPUT_POST,'enleverMedia');

//Récupère les informations de l'annonce à l'aide de son ID
if(isset($_GET['idA']))
{
	$annonceInfoOld = GetAnnonceInfo($_GET['idA']);
	$motsClesOld = GetKeywordsByIdAnnonce($_GET['idA']);
}

if(isset($_POST['update']))
{
	//Teste si tous les champs sont remplis, sinon affiche une erreur
	if(!empty($nomAnnonce) && !empty($description) && !empty($dateDebut) && !empty($dateFin) && isset($motsClesSelectPost))
	{
			$media = CheckMedia();
			
			//Définit comme false le fait qu'aucun mot-clé n'a été modifié
			$motsClesChange = false;
			$motsClesIdArrayOld= [];
			//Récupère les id de tous les mots-clés actuels
			foreach ($motsClesOld as $motsCleOld) 
			{
				array_push($motsClesIdArrayOld,$motsCleOld[0]);
			}
			//Array contenant tous les ID qui ne sont pas pareils dans les array contenant les nouveaux et anciens mots-clés	
			$differenceMotsCles = array_merge(array_diff($motsClesSelectPost,$motsClesIdArrayOld),array_diff($motsClesIdArrayOld,$motsClesSelectPost));

			//S'il n'y a pas d'erreur, procède à la mise à jour de l'annonce
			if(GetAlert()[1] != "error")
			{
				$dir = $media[0];
				$filename = $media[1];
				$type = $media[2];
				//Si quelconque changement est opéré, procède à la mise à jour
				if($supprimerMediaActuel ||$nomAnnonce != $annonceInfoOld[4] || $description != $annonceInfoOld[5] || $dateDebut != $annonceInfoOld[1] || $dateFin != $annonceInfoOld[2] || !empty($differenceMotsCles) || !empty($dir) || !empty($filename) || !empty($type))
				{
					//Si aucun changement de mot-clé a été effectué, défini l'array de mots-clés à update à null afin de ne pas mettre à jour les mots-clés
					if(empty($differenceMotsCles))
					$motsClesSelectPost = null;

					//Si la checkbox de "Supprimer média actuel" n'est pas cochée, définit la variable à null afin de ne pas supprimer le média actuel
					if(!isset($supprimerMediaActuel))					
						$supprimerMediaActuel = null;					
					
					//Mets à jour l'annonce et récupère le résultat de la requête
					$updateAnnonceResult = UpdateAnnonce($_GET['idA'],$nomAnnonce,$description,$dateDebut,$dateFin,$motsClesSelectPost,$dir,$filename,$type,$supprimerMediaActuel);					
					//Si la checkbox a été cochée pour supprimer le média actuel, supprime le média du serveur
					if(!is_null($supprimerMediaActuel) || GetAlert()[1] != "error")		
					unlink($annonceInfoOld[6].$annonceInfoOld[7].".".$annonceInfoOld[8]);
					//Si la mise à jour a bien été effectuée et qu'aucun nouveau média n'a été fourni, procède à l'upload sur le serveur de l'image
					if($updateAnnonceResult && !empty($dir) && !empty($filename) && !empty($type))
					{
						if(move_uploaded_file($_FILES["media"]["tmp_name"],$dir.$filename.".".$type))
						header('location: annonces.php?idU='.GetUserId());
						//Si l'upload n'a pas réussi, affiche une erreur
						else
						SetAlert("error",5);
					}
					//Si la mise à jour a été faite mais qu'aucun nouveau média n'a été fourni, redirige simplement vers annonces
					else if($updateAnnonceResult)
						header('location: annonces.php?idU='.GetUserId());
					//Si la mise à jour n'a pas été effectuée, affiche une erreur
					else
					SetAlert("error",11);
				}	
			}							
	}
	else
	SetAlert("error",6);
}
else
{
	//Permet de récupérer les informations de l'annonce en question pour modification
	if(isset($_GET['idA']))
	{		
		$nomAnnonce = $annonceInfoOld[4];
		$description = $annonceInfoOld[5];
		$dateDebut = $annonceInfoOld[1];
		$dateFin = $annonceInfoOld[2];	
		$motsClesSelectPost= [];
		foreach($motsClesOld as $motCle)
		{
			array_push($motsClesSelectPost,$motCle[0]);
		}
		$_POST['motsClesSelect'] = $motsClesSelectPost;
	}
}

?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Modifier une annonce | Search & Find Job</title>
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
	
	<?php ShowNavBar(); ?>
		
		<!-- Début section création d'annonce -->
		<section class="jobs">
			<div class="container">
				<div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
				<?php ShowAlert()?>
					<form method="POST" action="modifier-annonce.php?idA=<?= $_GET['idA']."&idU=".$_GET['idU']?>" enctype="multipart/form-data">		
						<label for="nomAnnonce" >Nom de l'annonce</label>									
                        <input required id="nomAnnonce" type="text" name="nomAnnonce" class="form-control input-lg" placeholder="Nom de l'annonce" value="<?=$nomAnnonce?>">
						<label for="description" >Description de votre annonce</label>									
						<textarea required id="description" name="description" placeholder="Description de votre annonce" class="form-control input-lg"><?=$description?></textarea>
						<label for="dateDebut" >Date de début de votre annonce</label>									
						<input required name="dateDebut" id="dateDebut" type="date" class="form-control input-lg" value="<?=$dateDebut?>">
						<label for="dateFin">Date de fin de votre annonce</label>									
						<input required name="dateFin" id="dateFin"  type="date" class="form-control input-lg" value="<?=$dateFin?>">	
						<label for="keywords" >Les tags de votre annonce (Veuillez en sélectionner 1 à plusieurs)</label>									
						<?php
						ShowSelectKeywords($_POST['motsClesSelect']);
						?>
                        <label for="media" >Média souhaitant être inclu à votre annonce (Image ou un fichier PDF) (Optionnel)</label>
                        <input id="media" name="media" type="file" accept=".png,.jpg,.jpeg,.pdf" class="form-control input-lg" >
						<?php
						if(!empty($annonceInfoOld[6])&&!empty($annonceInfoOld[7])&& !empty($annonceInfoOld[8]))
						{
							echo "<label for=\"enleverMedia\">Supprimer le média actuel (À cocher si oui)</label>
							<input id=\"enleverMedia\" name=\"enleverMedia\" class=\"form-control input-lg\" type=\"checkbox\"";if($supprimerMediaActuel)echo"checked"; echo "/>";
						}
						?>  
						<fieldset>
						<div class="row">										
							<div class='col'> 
							<input type="submit" name="update" id="update" class="form-control btn btn-primary" value="Mettre à Jour">
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