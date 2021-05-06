<?php
require_once './php/annonce_func.inc.php';
require_once "./php/pageAccess.inc.php";
require_once './php/error.inc.php';
require_once './php/nav.inc.php';

SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));
$nomAnnonce = filter_input(INPUT_POST,'nomAnnonce',FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST,'description',FILTER_SANITIZE_STRING);
$dateDebut = filter_input(INPUT_POST,'dateDebut',FILTER_SANITIZE_STRING);
$dateFin = filter_input(INPUT_POST,'dateFin',FILTER_SANITIZE_STRING);
$motsClesSelectPost = filter_input(INPUT_POST,'motsClesSelect',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
$supprimerMediaActuel = filter_input(INPUT_POST,'enleverMedia');
if(!isset($_SESSION))
{
session_start();
}
if(!IsUserLoggedIn())
ChangeLoginState(false);

$_SESSION['currentPage'] = pathinfo(__FILE__,PATHINFO_FILENAME);
$annonceInfoOld = GetAnnonceInfo($_GET['idA']);
$motsClesOld = GetKeywordsByIdAnnonce($_GET['idA']);


if(isset($_POST['update']))
{
	
	
	//Teste si tous les champs sont remplis, sinon affiche une erreur
	if(!empty($nomAnnonce) && !empty($description) && !empty($dateDebut) && !empty($dateFin) && isset($motsClesSelectPost))
	{
		//Si un fichier est fournit (Image ou PDF)
		if($_FILES["media"]['error'] == 0)
		{
			//Si le fichier fourni est plus petit que 20Mo
			if($_FILES["media"]["size"]<=20000000)
			{
				$Orgfilename = $_FILES["media"]["name"];
				$filename = uniqid();
				$dir = "./tmp/";
				$type = explode("/",$_FILES["media"]["type"])[1];
				$file = $filename.'.'.$type;

				if(!in_array($type,["png","bmp","jpg","jpeg","pdf"]))
				{		
					SetError(8); 
				}				
			}
		}
		else
		{
			$dir = null;
			$filename = null;
			$type = null;
		}

			$motsClesChange = false;
			$motsClesIdArrayOld= [];
			foreach ($motsClesOld as $motsCleOld) 
			{
				array_push($motsClesIdArrayOld,$motsCleOld[0]);
			}
			for ($i=0; $i < count($motsClesIdArrayOld); $i++) 
			{ 
				if(!in_array($motsClesSelectPost[$i],$motsClesIdArrayOld))
				$motsClesChange = true;
			}

			if(empty(GetError()))
			{
				if($supprimerMediaActuel ||$nomAnnonce != $annonceInfoOld[4] || $description != $annonceInfoOld[5] || $dateDebut != $annonceInfoOld[1] || $dateFin != $annonceInfoOld[2] || $motsClesChange || !empty($dir) || !empty($filename) || !empty($type))
				{
					if(!$motsClesChange)
					$motsClesSelectPost = null;

					if(!isset($supprimerMediaActuel))					
						$supprimerMediaActuel = null;					
					
					$updateAnnonceResult = UpdateAnnonce($_GET['idA'],$nomAnnonce,$description,$dateDebut,$dateFin,$motsClesSelectPost,$dir,$filename,$type,$supprimerMediaActuel);					
					if($updateAnnonceResult && !empty($dir) && !empty($filename) && !empty($type))
					{
						if(!is_null($supprimerMediaActuel))		
						unlink($annonceInfoOld[6].$annonceInfoOld[7].".".$annonceInfoOld[8]);

						if(move_uploaded_file($_FILES["media"]["tmp_name"],$dir.$filename.".".$type))
						header('location: annonces.php?idU='.GetUserId());
						else
						SetError(5);
					}
					else if($updateAnnonceResult)
					{
						header('location: annonces.php?idU='.GetUserId());
						if(!is_null($supprimerMediaActuel))		
						unlink($annonceInfoOld[6].$annonceInfoOld[7].".".$annonceInfoOld[8]);
					}
					else
					SetError(11);
				}	
			}							
	}
}
else
{
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
        <title>Créer une offre d'emploi</title>
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
				<?php ShowError()?>
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
							<input type="reset" name="reset" id="reset" class="form-control btn btn-primary" value="reset"/>
							</div>											
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