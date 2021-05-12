<?php 
// SQL
// ==========================================================================================================

/**
 * Permet de tester si l'utilisateur a déjà ajouté une annonce dans sa wishlist
 *
 * @param int $idAnnonce
 * @param int $idUtilisateur
 * @return bool Retourne Vrai s'il possède déjà l'annonce dans sa Wishlist, False dans le cas contraire
 */
function HasUserAddedAnnonceToWishlist($idAnnonce,$idUtilisateur)
{
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT * FROM `wishlists` WHERE annonces_id = :IDANNONCE AND utilisateurs_id = :IDUTILISATEUR';
   
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
    //Si la requête réussi sans soucis et qu'il y a plus de 0 lignes retournées, assigne true à la variable $answer
    if ($ps->execute() && $ps->rowCount() > 0)
      $answer = true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
  return $answer;
}
/**
 * Permet d'ajouter une annonce à une wishlist d'un utilisateur
 *
 * @param int $idAnnonce
 * @param int $idUtilisateur
 * @return bool Retourne True si la requête a bien été effectuée, false dans le cas contraire
 */
function AddToUserWishlist($idAnnonce,$idUtilisateur)
{
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'INSERT INTO `wishlists` (`annonces_id`,`utilisateurs_id`) VALUES (:IDANNONCE,:IDUTILISATEUR)';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
    //Si la requête réussi sans soucis et qu'il y a plus de 0 lignes retournées, assigne true à la variable $answer
    if ($ps->execute())
      $answer = true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
  return $answer;
}
/**
 * Permet de récupérer la Wishlist d'un utilisateur
 *
 * @param int $idUtilisateur
 * @param int $limit
 * @return array
 */
function GetWishlistForUser($idUtilisateur,$limit)
{
    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $ps = null;
    $sql = 'SELECT * FROM `wishlists` JOIN `annonces` ON (wishlists.annonces_id = annonces.id)WHERE wishlists.utilisateurs_id = :IDUTILISATEUR ORDER BY `date` ASC LIMIT :LIMIT';
  
    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    $answer = false;
    try {
      //Affecte tous les paramètres avec la variable correspondante
      $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
      $ps->bindParam(":LIMIT",$limit,PDO::PARAM_INT);
      //Si la requête réussi sans soucis et qu'il y a plus de 0 lignes retournées, assigne true à la variable $answer
      if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
    } 
    //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
    catch (PDOException $e) 
    {
      echo $e->getMessage();
    }
    //Renvoie le résultat de la requête une fois terminé
    return $answer;
}
/**
 * Permet de retirer une annonce de la wishlist d'un utilisateur
 *
 * @param int $idUtilisateur
 * @param int $idAnnonce
 * @return bool Retourne true si la requête s'est bien effectuée, false dans le cas contraire
 */
function RemoveWish($idUtilisateur,$idAnnonce)
{
    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $ps = null;
    $sql = 'DELETE FROM `wishlists` where utilisateurs_id = :IDUTILISATEUR AND annonces_id = :IDANNONCE';
  
    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    $answer = false;
    try {
      //Affecte tous les paramètres avec la variable correspondante
      $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
      $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
      //Si la requête réussi sans soucis et qu'il y a plus de 0 lignes retournées, assigne true à la variable $answer
      if ($ps->execute() && $ps->rowCount() > 0)
      $answer = true;
    } 
    //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false 
    catch (PDOException $e) 
    {
      echo $e->getMessage();
    }
    //Renvoie le résultat de la requête une fois terminé
    return $answer;
}

// PHP
// ==========================================================================================================

/**
 * Permet d'afficher la wishlist d'un utilisateur
 *
 * @param int $idUtilisateur
 * @param int $limit
 * @return void Echo la wishlist de l'utilisateur
 */
function ShowWishlist($idUtilisateur,$limit)
{
  //Récupère toutes les annonces suivies par l'utilisateur en question
  $wishes = GetWishlistForUser($idUtilisateur,$limit);
  $affichageAnnonce = "";

  //Si l'utilisateur suit des annonces, les affiche
	if($wishes != false)
    {
      
        foreach($wishes as $wish)
        {
         
            $affichageAnnonce .="<a href=\"annonce.php?idA=".$wish[3]."\">";
            $affichageAnnonce .="<div class=\"company-list\">";
            $affichageAnnonce .= "	<div class=\"row\">";
            $affichageAnnonce .= "		<div class=\"col-md-10 col-sm-10\">";
            $affichageAnnonce .= "			<div class=\"company-content\">";
            $affichageAnnonce .= "				<h3>".$wish[7]."</h3></a>";
            $affichageAnnonce .= "				<p><span class=\"package\"><i class=\"fa fa-clock-o\"></i>Annonce suivie le ".$wish[2]."</span></p>";
            $affichageAnnonce .=  "<a style=\"color:red\" id=\"supprimerWish\" href=\"supprimer-wish.php?idU=".$idUtilisateur."&idA=".$wish[3]."\"> Retirer de la wishlist</a>";  ;
            $affichageAnnonce .= "			</div>";
            $affichageAnnonce .= "		</div>";
            $affichageAnnonce .= "	</div>";
            $affichageAnnonce .= "</div>";
        }
    }
    //Sinon affiche un message
    else
    $affichageAnnonce .="<p style=\"text-align:center;\">Vous n'avez pas d'annonces dans votre Wishlist, ajoutez-en !</p>";
    echo $affichageAnnonce;
}