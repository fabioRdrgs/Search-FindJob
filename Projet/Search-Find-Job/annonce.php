<?php
require_once './php/nav.inc.php';
require_once './php/annonce_func.inc.php';
require_once './php/pageAccess.inc.php';
if(!isset($_SESSION))
session_start();

SetCurrentPage(pathinfo(__FILE__,PATHINFO_FILENAME));

if(isset($_POST['addToWishlist']))
{
    if(!HasUserAddedAnnonceToWishlist($_GET['idA'],GetUserId()))
    if(AddToUserWishlist($_GET['idA'],GetUserId()))
    SetAlert("success",1);
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
<?php ShowAlert();?>
		<?php if(isset($_GET['idA']))ShowAnnonceInfo(GetUserType(),$_GET['idA'])?>

	<?php include_once './php/footer.inc.html'?>
		 
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/owl.carousel.min.js"></script>
		<script src="js/bootsnav.js"></script>
		<script src="js/main.js"></script>
    </body>
</html>
