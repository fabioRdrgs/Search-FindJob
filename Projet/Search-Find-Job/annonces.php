<?php
require_once './php/nav.inc.php';
require_once './php/annonce_func.inc.php';
$titre = filter_input(INPUT_POST,'nomAnnonce',FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST,'descAnnonce',FILTER_SANITIZE_STRING);
$motsClesSelectPost = filter_input(INPUT_POST,'motsClesSelect',FILTER_SANITIZE_NUMBER_INT,FILTER_REQUIRE_ARRAY);
if(!isset($_SESSION))
session_start();

SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));

if(!isset($_GET['limit']))
$_GET['limit'] = 1;
if(isset($_POST['plusAnnonces']))
{
	$_GET['limit']++;
	$_POST['rechercher'] = true;
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
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
		<!-- Style & Common Css --> 
		<link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/main.css">


    </head>
	
    <body>

		<?php ShowNavBar();?>
		
		<!-- Inner Banner -->
		<section class="inner-banner" style="background-color:#242c36 url(https://via.placeholder.com/1920x600)no-repeat;">
			<div class="container">
				<div class="caption">
					<?php
					//Affiche un message différent dépendant de si on est sur nos jobs ou simplement en train de parcourir les jobs
						if(isset($_GET['idU']))
						echo "<h2>Vos offres d'emploi</h2>";
						else
						echo "<h2>Trouvez votre job rêvé!</h2>"
					?>					
				</div>
			</div>
		</section>
		
		<form method="POST" action="annonces.php<?php 
		if(isset($_GET['idU'])) echo"?idU=".$_GET['idU']."&limit=".$_GET['limit'];else echo "?limit=".$_GET['limit']?>">
		<section class="jobs">
			<div class="container">
			
			<!-- Début Header recherche annonces -->
				<div class="row heading">
					<h2>Cherchez votre annonce</h2>
				</div>
			<!-- Fin Header recherche annonces -->
				<!-- Début div recherche -->
				<div class="row top-pad">
					<div class="filter">
						<div class="col-md-2 col-sm-3">
							<p>Rechercher par:</p>
						</div>							
					
						<div class="col-md-10 col-sm-9 pull-right">
							<ul class="filter-list">
								<li>
									<label for="nomAnnonce">Nom Annonce</label>
									<input class="form-control input-lg" id="nomAnnonce" style="width:24rem;"  name="nomAnnonce" type="text" placeholder="Rechercher une annonce" value="<?= $titre?>"/>
								</li>
								<li>
									<label for="descAnnonce">Description Annonce</label>
									<input class="form-control input-lg"id="descAnnonce" style="width:40rem;" name="descAnnonce" type="text" placeholder="Rechercher par description" value="<?= $description?>"/>
								</li>
								<li>
								<label for="motsClesSelect">Rechercher par Mots Clés</label>
									<?php ShowSelectKeywords($motsClesSelectPost);?>
								</li>
								<li>
									<input class="form-control input-lg" type="submit" name="rechercher" value="Rechercher"/>
								</li>
							</ul>	
						</div>
					</div>							
				</div>	

				<!-- Fin div recherche -->
				<!-- Début affichage annonces -->

				<div class="companies">
                                          
            </div>
<?php
if(isset($_POST['rechercher']))
{
	$annonces = GetAnnoncesFromSearch($titre,$description,$motsClesSelectPost,4*$_GET['limit'],null);
	if($annonces != false)
	foreach($annonces as $annonce)
	{
		$affichageAnnonce = "";
		$affichageAnnonce .="<a href=\"annonce.php?idA=".$annonce[0]."\"><div class=\"company-list\">";
		$affichageAnnonce .= "	<div class=\"row\">";
		$affichageAnnonce .= "		<div class=\"col-md-10 col-sm-10\">";
		$affichageAnnonce .= "			<div class=\"company-content\">";
		$affichageAnnonce .= "				<h3>".$annonce[4]."</h3>";
		$affichageAnnonce .= "				<p><span class=\"company-name\">
											<i class=\"fa fa-calendar-check-o\"></i>".$annonce[1]."</span><span class=\"company-location\">
											<i class=\"fa fa-calendar-times-o\"></i>".$annonce[2]."</span>
											<span class=\"package\"><i class=\"fa fa-clock-o\"></i>".$annonce[3]."</span></p>";
		$affichageAnnonce .= "			</div>";
		$affichageAnnonce .= "		</div>";
		$affichageAnnonce .= "	</div>";
		$affichageAnnonce .= "</div></a>";
		echo $affichageAnnonce; 
	}
}
?>
				<!-- Fin affichage annonces-->

				<div class="row">			
					<input name="plusAnnonces" type="submit" class="btn browse-btn" value="Plus d'annonces" />		
				</div>
			</div>
		</section>
		</form>

		<?php include_once './php/footer.inc.html'?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
		<script src="js/main.js"></script>
	
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
		
    </body>
</html>