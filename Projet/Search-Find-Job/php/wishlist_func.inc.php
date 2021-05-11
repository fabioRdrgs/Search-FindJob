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
  static $ps = null;
  $sql = 'SELECT * FROM `wishlists` WHERE annonces_id = :IDANNONCE AND utilisateurs_id = :IDUTILISATEUR';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
    if ($ps->execute() && $ps->rowCount() > 0)
      $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
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
  static $ps = null;
  $sql = 'INSERT INTO `wishlists` (`annonces_id`,`utilisateurs_id`) VALUES (:IDANNONCE,:IDUTILISATEUR)';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
    if ($ps->execute())
      $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
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
    static $ps = null;
    $sql = 'SELECT * FROM `wishlists` JOIN `annonces` ON (wishlists.annonces_id = annonces.id)WHERE wishlists.utilisateurs_id = :IDUTILISATEUR LIMIT :LIMIT';
  
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    $answer = false;
    try {
      $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
      $ps->bindParam(":LIMIT",$limit,PDO::PARAM_INT);
      if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
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
    static $ps = null;
    $sql = 'DELETE FROM `wishlists` where utilisateurs_id = :IDUTILISATEUR AND annonces_id = :IDANNONCE';
  
    if ($ps == null) {
      $ps = db()->prepare($sql);
    }
    $answer = false;
    try {
      $ps->bindParam(":IDUTILISATEUR",$idUtilisateur,PDO::PARAM_INT);
      $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
      if ($ps->execute() && $ps->rowCount() > 0)
      $answer = true;
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
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
  $wishes = GetWishlistForUser($idUtilisateur,$limit);
	if($wishes != false)
    {
        foreach($wishes as $wish)
        {
            $affichageAnnonce = "";
            $affichageAnnonce .="<a href=\"annonce.php?idA=".$wish[3]."\">
            <div class=\"company-list\">";
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
            echo $affichageAnnonce; 
        }
    }
    else
    echo "<p style=\"text-align:center;\">Vous n'avez pas d'annonces dans votre Wishlist, ajoutez-en !</p>";

}