<?php
require_once './php/alert.inc.php';
require_once './php/nav.inc.php';
if(!isset($_SESSION))
session_start();

SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));

if(isset($_GET['error']))
SetAlert("error",$_GET['error']);
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
	
		<?php ShowNavBar(); ?>
		<?php 
		ShowAlert()?>
	
				<!-- Inner Banner -->
				<section class="inner-banner" style="background-color:#242c36 url(https://via.placeholder.com/1920x600)no-repeat;">
			<div class="container">
				<div class="caption">
					<h2>Trouvez votre Job rêvé ! ! !</h2>				
				</div>
			</div>
		</section>
	<?php include_once "./php/index.inc.php";?>

			
		<?php include_once './php/footer.inc.html'?>
		 
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
		<script src="js/main.js"></script>
    </body>
</html>
