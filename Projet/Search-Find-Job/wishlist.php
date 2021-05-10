<?php 
require_once './php/nav.inc.php';
require_once './php/annonce_func.inc.php';
require_once './php/alert.inc.php';
require_once './php/pageAccess.inc.php';

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

	<?php ShowNavBar();?>
		
		<form method="POST" action="wishlist.php<?php 
		if(isset($_GET['idU'])) echo"?idU=".$_GET['idU']."&limit=".$_GET['limit'];else echo "?limit=".$_GET['limit']?>">
		<section class="jobs">
			<div class="container">
			<?php ShowAlert();?>
				<div class="row heading">
					<h2>Votre Wishlist</h2>
					<p>Toutes les annonces que vous suivez</p>
				</div>

				<div class="companies">
				<?php ShowWishlist($_GET['idU'],4*$_GET['limit'])?>
				</div>
				<div class="row">			
					<input name="plusAnnonces" type="submit" class="btn browse-btn" value="Plus d'annonces" />		
				</div>
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