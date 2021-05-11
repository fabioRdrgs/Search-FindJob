<?php
require_once 'db.inc.php';
include_once 'wishlist_func.inc.php';
// SQL
// ==========================================================================================================

/**
 * Permet de créer une annonce ainsi qu'ajouter les mots clés fournis à cette dernière
 *
 * @param string $nomAnnonce
 * @param string $description
 * @param date $dateDebut
 * @param date $dateFin
 * @param array $keywords
 * @param string $dir
 * @param string $file
 * @param string $type
 * @param int $idUtilisateur
 * @return bool Retourne True si la transaction a bien été effectuée, false dans le cas contraire
 */
function CreerAnnonce($nomAnnonce,$description,$dateDebut,$dateFin,$keywords,$dir,$file,$type,$idUtilisateur)
{
  try {
    db()->beginTransaction();

    static $psAnnonce = null;
    if ($psAnnonce == null)
      $psAnnonce = db()->prepare("INSERT INTO `annonces` (`titre`,`description`,`date_debut`,`date_fin`,`media_path`,`media_nom`,`media_type`,`utilisateurs_id`)
                    VALUES (:TITRE,:DESCRIPTION,:DATEDEBUT,:DATEFIN,:MEDIAPATH,:MEDIANOM,:MEDIATYPE,:UTILISATEURSID)");
    $psAnnonce->bindParam(':TITRE', $nomAnnonce, PDO::PARAM_STR);
    $psAnnonce->bindParam(':DESCRIPTION', $description, PDO::PARAM_STR);
    $psAnnonce->bindParam(':DATEDEBUT', $dateDebut, PDO::PARAM_STR);
    $psAnnonce->bindParam(':DATEFIN', $dateFin, PDO::PARAM_STR);
    $psAnnonce->bindParam(':MEDIAPATH', $dir, PDO::PARAM_STR);
    $psAnnonce->bindParam(':MEDIANOM', $file, PDO::PARAM_STR);
    $psAnnonce->bindParam(':MEDIATYPE', $type, PDO::PARAM_STR);
    $psAnnonce->bindParam(':UTILISATEURSID', $idUtilisateur, PDO::PARAM_INT);
    $psAnnonce->execute();

    $annonceId = db()->lastInsertId();

    static $psKeywords = null;
    if($psKeywords == null)
     $psKeywords = db()->prepare("INSERT INTO `annonces_has_keywords` (`annonces_id`,`keywords_id`) 
     VALUES (:ANNONCESID,:KEYWORDSID)");

     foreach($keywords as $keyword)
     {
       $psKeywords->bindParam(':ANNONCESID',$annonceId,PDO::PARAM_INT);
       $psKeywords->bindParam(':KEYWORDSID',$keyword,PDO::PARAM_INT);
       $psKeywords->execute();
     }   
    db()->commit();

    return true;
  } catch (PDOException $e) {
    db()->rollBack();
    return false;
  }
}
/**
 * Permet de supprimer une annonce
 *
 * @param int $idAnnonce
 * @param int $idUser
 * @return bool Retourne True si la requête a bien été effectuée, false dans le cas contraire
 */
function DeleteAnnonce($idAnnonce, $idUser)
{
  static $ps = null;
  $sql = "DELETE FROM `annonces` WHERE (`id` = :IDANNONCE) AND (`utilisateurs_id`=:IDUSER);";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
    $ps->bindParam(':IDUSER', $idUser, PDO::PARAM_INT);
    $ps->execute();
    if($ps->rowCount() > 0)
    $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

/**
 * Permet de mettre à jour une annonce dans de multiples aspects: la mise à jour des données, remplacer le média de l'annonce ou l'enlever
 *
 * @param int $idAnnonce
 * @param string $nomAnnonce
 * @param string $description
 * @param date $dateDebut
 * @param date $dateFin
 * @param array $keywords
 * @param string $dir
 * @param string $filename
 * @param string $type
 * @param bool/null $supprimerMediaActuel
 * @return bool Retourne True si la transaction a bien été effectuée, false dans le cas contraire
 */
function UpdateAnnonce($idAnnonce,$nomAnnonce,$description,$dateDebut,$dateFin,$keywords,$dir,$filename,$type, $supprimerMediaActuel)
{
  try {
    db()->beginTransaction();

    static $psAnnonce = null;
    if ($psAnnonce == null)
      $psAnnonce = db()->prepare("UPDATE `annonces` SET 
      date_debut = :DATEDEBUT, 
      date_fin = :DATEFIN, 
      titre = :TITRE, 
      description = :DESCRIPTION WHERE (id = :IDANNONCE)");
      $psAnnonce->bindParam(':TITRE', $nomAnnonce, PDO::PARAM_STR);
      $psAnnonce->bindParam(':DESCRIPTION', $description, PDO::PARAM_STR);
      $psAnnonce->bindParam(':DATEDEBUT', $dateDebut, PDO::PARAM_STR);
      $psAnnonce->bindParam(':DATEFIN', $dateFin, PDO::PARAM_STR);
      $psAnnonce->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
      $psAnnonce->execute();

    if(!empty($supprimerMediaActuel))
    {
      static $psDeleteCurrentMedia = null;
      if($psDeleteCurrentMedia == null)
       $psDeleteCurrentMedia = db()->prepare("UPDATE `annonces` SET media_path = NULL, media_nom = NULL, media_type = NULL WHERE id = :IDANNONCE");
       $psDeleteCurrentMedia->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
       $psDeleteCurrentMedia->execute();

    }

    if(!empty($dir) && !empty($filename) && !empty($type))
    {
        static $psMedia = null;
        if($psMedia == null)
        $psMedia = db()->prepare("UPDATE `annonces` SET media_path = :MEDIAPATH, media_nom = :MEDIANOM, media_type = :MEDIATYPE  WHERE (id = :IDANNONCE)");
        $psMedia->bindParam(':MEDIAPATH', $dir, PDO::PARAM_STR);
        $psMedia->bindParam(':MEDIANOM', $filename, PDO::PARAM_STR);
        $psMedia->bindParam(':MEDIATYPE', $type, PDO::PARAM_STR);
        $psMedia->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
        $psMedia->execute();
    }
    if(!empty($keywords))
    {
      static $psKeywordsDelete = null;
      if($psKeywordsDelete == null)
       $psKeywordsDelete = db()->prepare("DELETE FROM `annonces_has_keywords` WHERE annonces_id = :IDANNONCE");
       $psKeywordsDelete->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
       $psKeywordsDelete->execute();

       static $psKeywordsAdd = null;
       if($psKeywordsAdd == null)
        $psKeywordsAdd = db()->prepare("INSERT INTO `annonces_has_keywords` (`annonces_id`,`keywords_id`) 
        VALUES (:ANNONCESID,:KEYWORDSID)");
   
        foreach($keywords as $keyword)
        {
          $psKeywordsAdd->bindParam(':ANNONCESID',$idAnnonce,PDO::PARAM_INT);
          $psKeywordsAdd->bindParam(':KEYWORDSID',$keyword,PDO::PARAM_INT);
          $psKeywordsAdd->execute();
        }   
          
    }
   
    db()->commit();

    return true;
  } 
  catch (PDOException $e) 
  {
    db()->rollBack();
    return false;
  }
}
/**
 * Permet de récupérer les Mots-Clés
 *
 * @return array Retourne un array contenant l'ensemble des mots-clés
 */
function GetKeywords()
{
  static $ps = null;
  $sql = 'SELECT * FROM `keywords`';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
/**
 * Permet de récupérer les mots-clés d'une annonce
 *
 * @param int $idAnnonce
 * @return array Retourne un array contenant l'ensemble des mots-clés de l'annonce en question
 */
function GetKeywordsByIdAnnonce($idAnnonce)
{
  static $ps = null;
  $sql = 'SELECT keywords.id, keywords.label FROM `keywords` JOIN `annonces_has_keywords` ON (keywords.id = annonces_has_keywords.keywords_id) WHERE annonces_has_keywords.annonces_id = :IDANNONCE';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

/**
 * Récupère l'ensemble des informations d'une annonce
 *
 * @param int $idAnnonce
 * @return array Retourne un array contenant l'ensemble des informations de l'annonce en question
 */
function GetAnnonceInfo($idAnnonce)
{
  static $ps = null;
  $sql = 'SELECT * FROM `annonces` WHERE id = :IDANNONCE';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetch(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
/**
 * Retourne les personnes ayant ajouté une annonce à leur wishlist
 *
 * @param int $idAnnonce
 * @return array Retourne un array contenant tous les utilisateurs ayant l'annonce dans leur wishlist avec la date d'ajout et leur login
 */
function GetFollowersByIdAnnonce($idAnnonce)
{
  static $ps = null;
  $sql = 'SELECT utilisateurs.login, wishlists.date FROM `annonces` JOIN `wishlists` ON (annonces.id = wishlists.annonces_id) JOIN `utilisateurs` ON (wishlists.utilisateurs_id = utilisateurs.id) WHERE annonces.id = :IDANNONCE';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

/**
 * Permet d'effectuer une recherche d'annonces dans la BDD avec les filtres appliqués par un chercheur
 *
 * @param string $recherche
 * @param array $motsClesSelect
 * @param int $limit
 * @return array Retourne toutes les annonces résultant de la recherche
 */
function GetAnnoncesFromSearchChercheur($recherche,$motsClesSelect,$limit)
{
  if(empty($motsClesSelect))
  $countKeywords = 0;
  else
  $countKeywords = count($motsClesSelect);

  static $ps = null;

  //Pour chaque paramètres, s'il est fournit => le teste, sinon, ne le teste pas ({PARAMÈTRE} IS NULL OR [CONDITION] = {PARAMÈTRE})
  $sql = "SELECT DISTINCT annonces.*
  FROM annonces 
  JOIN annonces_has_keywords 
  ON (annonces.id = annonces_has_keywords.annonces_id) 
  WHERE (titre LIKE :RECHERCHE 
  OR description LIKE :RECHERCHE) 
  AND date_debut <= CURRENT_DATE AND date_fin >= CURRENT_DATE ";

for ($i=0; $i < $countKeywords; $i++) 
{ 
  if($i == 0)
  $sql.= "AND ((:KEYWORD".$i." IS NULL OR keywords_id = :KEYWORD".$i.")";
  else
  $sql.= " OR (:KEYWORD".$i." IS NULL OR keywords_id = :KEYWORD".$i.")";

  if($i == $countKeywords-1)
  $sql.=")";
}
$sql.="  ORDER BY date_publication DESC LIMIT :LIMIT";


  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':RECHERCHE', "%" . $recherche . "%", PDO::PARAM_STR);
    $ps->bindParam(':LIMIT',$limit,PDO::PARAM_INT);
    for ($i=0; $i <  $countKeywords; $i++) { 
      $ps->bindParam(':KEYWORD'.$i, $motsClesSelect[$i], PDO::PARAM_INT); 
    }

    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

/**
 * Permet d'effectuer une recherche d'annonces dans la BDD avec les filtres appliqués par un Annonceur
 *
 * @param string $recherche
 * @param array $motsClesSelect
 * @param int $limit
 * @param int $idUtilisateur
 * @return array Retourne toutes les annonces résultant de la recherche
 */
function GetAnnoncesFromSearchAnnonceur($recherche, $motsClesSelect,$limit,$idUtilisateur)
{
  if(is_null($motsClesSelect))
  $countKeywords = 0;
  else
  $countKeywords = count($motsClesSelect);

  static $ps = null;

  //Pour chaque paramètres, s'il est fournit => le teste, sinon, ne le teste pas ({PARAMÈTRE} IS NULL OR [CONDITION] = {PARAMÈTRE})
  $sql = "SELECT DISTINCT annonces.*
  FROM annonces 
  JOIN annonces_has_keywords 
  ON (annonces.id = annonces_has_keywords.annonces_id) 
  WHERE (titre LIKE :RECHERCHE 
  OR description LIKE :RECHERCHE)  
  AND (:IDUTILISATEUR IS NULL OR utilisateurs_id = :IDUTILISATEUR)";

for ($i=0; $i < $countKeywords; $i++) 
{ 
  if($i == 0)
  $sql.= "AND ((:KEYWORD".$i." IS NULL OR keywords_id = :KEYWORD".$i.")";
  else
  $sql.= " OR (:KEYWORD".$i." IS NULL OR keywords_id = :KEYWORD".$i.")";

  if($i == $countKeywords-1)
  $sql.=")";
}
$sql.="  ORDER BY date_publication DESC LIMIT :LIMIT";


  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':RECHERCHE', "%" . $recherche . "%", PDO::PARAM_STR);
    $ps->bindParam(':IDUTILISATEUR', $idUtilisateur, PDO::PARAM_INT);
    $ps->bindParam(':LIMIT',$limit,PDO::PARAM_INT);
    for ($i=0; $i <  $countKeywords; $i++) { 
      $ps->bindParam(':KEYWORD'.$i, $motsClesSelect[$i], PDO::PARAM_INT); 
    }

    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}



// PHP
// ==========================================================================================================

/**
 * Permet d'afficher un select multiple avec recherche live
 *
 * @param array $motsClesSelectPost
 * @return void Echo le select multiple créé
 */
function ShowSelectKeywords($motsClesSelectPost)
{
  if(!isset($motsClesSelectPost))
  $motsClesSelectPost = [];

  $keywords= GetKeywords();
  $select="";
  $select.= "<div class=\"row-fluid\">";
  $select.=	"<select id=\"motsClesSelect\" name=\"motsClesSelect[]\" multiple class=\"selectpicker\" data-show-subtext=\"true\" data-live-search=\"true\">";
  foreach($keywords as $keyword)
{
  if(in_array($keyword[0],$motsClesSelectPost))
  $select.="<option selected value=\"".$keyword[0]."\">".$keyword[1]."</option>";
  else
  $select.="<option value=\"".$keyword[0]."\">".$keyword[1]."</option>";
}
  $select.=			"</select>";
  $select.=			"</div>";
  echo $select;
}
/**
 * Permet d'afficher les annonces étant le résultat d'une recherche d'un Chercheur
 *
 * @param string $recherche
 * @param array $motsClesSelectPost
 * @param int $limit
 * @return void Echo les annonces pour le chercheur résultant de la recherche 
 */
function ShowAnnoncesChercheur($recherche,$motsClesSelectPost,$limit)
{
  $annonces = GetAnnoncesFromSearchChercheur($recherche,$motsClesSelectPost,$limit);
	if($annonces != false)
	foreach($annonces as $annonce)
	{
		$affichageAnnonce = "";
		$affichageAnnonce .="<a href=\"annonce.php?idA=".$annonce[0]."\"><div class=\"company-list\">";
		$affichageAnnonce .= "	<div class=\"row\">";
		$affichageAnnonce .= "		<div class=\"col-md-10 col-sm-10\">";
		$affichageAnnonce .= "			<div class=\"company-content\">";
		$affichageAnnonce .= "				<h3>".$annonce[4]."</h3>";
		$affichageAnnonce .= "				<p><span class=\"package\"><i class=\"fa fa-clock-o\"></i>".$annonce[3]."</span></p>";
		$affichageAnnonce .= "			</div>";
		$affichageAnnonce .= "		</div>";
		$affichageAnnonce .= "	</div>";
		$affichageAnnonce .= "</div></a>";
		echo $affichageAnnonce; 
	}
}


/**
 * Permet d'afficher les annonces étant le résultat d'une recherche d'un Annonceur
 *
 * @param string $recherche
 * @param array $motsClesSelectPost
 * @param int $limit
 * @param int $idUtilisateur
 * @return void Echo les annonces pour le chercheur résultant de la recherche 
 */
function ShowAnnoncesAnnonceur($recherche,$motsClesSelectPost,$limit,$idUtilisateur)
{
  $annonces = GetAnnoncesFromSearchAnnonceur($recherche,$motsClesSelectPost,$limit,$idUtilisateur);
  $affichageAnnonce = "";
	if(!empty($annonces))
  {
    foreach($annonces as $annonce)
    {
      $keywords = GetKeywordsByIdAnnonce($annonce[0]);
      $followers = GetFollowersByIdAnnonce($annonce[0]);
      
      $affichageAnnonce .="<a href=\"annonce.php?idA=".$annonce[0]."\"><div class=\"company-list\">";
      $affichageAnnonce .= "	<div class=\"row\">";
      $affichageAnnonce .= "		<div class=\"col-md-10 col-sm-10\">";
      $affichageAnnonce .= "			<div class=\"company-content\">";
      $affichageAnnonce .= "				<h3>".$annonce[4]."</h3></a>";
      $affichageAnnonce .= "				<p><span class=\"company-name\">
                        <i class=\"fa fa-calendar-check-o\"></i>".$annonce[1]."</span><span class=\"company-location\">
                        <i class=\"fa fa-calendar-times-o\"></i>".$annonce[2]."</span>
                        <span class=\"package\"><i class=\"fa fa-clock-o\"></i>".$annonce[3]."</span>";
      $affichageAnnonce.= "<a style=\"color:red\"href=\"modifier-annonce.php?idA=".$annonce[0]."&idU=".$_GET['idU']."\"> Modifier </a>";
      $affichageAnnonce.= "<a style=\"color:red\" id=\"supprimerAnnonce\" href=\"supprimer-annonce.php?idA=".$annonce[0]."&idU=".$idUtilisateur."\"> Supprimer </a>";                
      $affichageAnnonce.="</p>";
      $affichageAnnonce.= "<p><span>Mots-clés : ";
      if(!empty($keywords))
      {
        for ($i=0; $i < count($keywords); $i++) {
          if($i==0)
          $affichageAnnonce.= $keywords[$i][1];
          else
          $affichageAnnonce.= ", ".$keywords[$i][1];
        }
      }
      $affichageAnnonce .="</span>";
      $affichageAnnonce .= "<span>Nombre de followers : ";
      if(!empty($followers))
      $affichageAnnonce .= count($followers);
      else
      $affichageAnnonce .="0";
      $affichageAnnonce .="</span></p>";
      $affichageAnnonce .= "			</div>";
      $affichageAnnonce .= "		</div>";
      $affichageAnnonce .= "	</div>";
      $affichageAnnonce .= "</div>";
      
    }
  }
  else
  $affichageAnnonce.="<p style=\"text-align:center;\">Vous n'avez pas d'annonces, créez-en !</p>";
	echo $affichageAnnonce; 
}
/**
 * Permet d'afficher les informations d'une annonce 
 *
 * @param string $typeUser
 * @param id $idAnnonce
 * @return void Echo les informations de l'annonce pour les afficher sur la page
 */
function ShowAnnonceInfo($typeUser,$idAnnonce)
{
  $annonceInfo = GetAnnonceInfo($idAnnonce);

  $annonce = "";
  if($typeUser =="Annonceur")
  {    
    $followers = GetFollowersByIdAnnonce($annonceInfo[0]);
    $annonce.= "<section class=\"profile-detail\">";
    $annonce.=    "<div class=\"container\">";
    $annonce.=      "<div class=\"col-md-12\">";
    $annonce.=        "<div class=\"row\">";
    $annonce.=          "<div class=\"basic-information\">";
    $annonce.=            "<div style=\"width:100%;\"class=\"col-md-9 col-sm-9\">";
    $annonce.=              "<div class=\"profile-content\">";
    $annonce.=                "<h2>".$annonceInfo[4]."</h2>";
    $annonce.=                "<p>Nombre de followers : ";
    if(!empty($followers))
    $annonce.= count($followers);
    else
    $annonce.= "0";
    $annonce.= "</p>";
    $annonce.=                "<ul class=\"information\">";
    $annonce.=                "<div class=\"panel panel-default\"><h4>Followers</h4></div>";
    if(!empty($followers))
    {
      foreach($followers as $follower)
      {
        $annonce.= "<li><span>".$follower[0]."</span> le ".$follower[1]."</li>";
      }
    }
    else
    $annonce.=                "<li><b>Vous n'avez pas de followers sur cette annonce</b></li>";
    $annonce.=                "</ul>";
    $annonce.=              "</div>";
    $annonce.=              "</br>";
    $annonce.=              "<div class=\"panel panel-default\">";
    $annonce.=                "<div class=\"panel-heading\">";
    $annonce.=                  "<i class=\"fa fa-user fa-fw\"></i> Description";
    $annonce.=                "</div>";                       
    $annonce.=                "<div  class=\"panel-body\">";
    $annonce.=                  "<p>".$annonceInfo[5]."</p>	";
    $annonce.=                "</div>";
    $annonce.=              "</div>";
    $annonce.=              "<div class=\"panel panel-default\">";
  
    if(!empty($annonceInfo[8]))
    {  
      $annonce.= "<table style=\"text-align: center;margin: auto\">";
      if($annonceInfo[8] == "pdf")
      {
        $annonce.= "<tr><td><embed src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" width=\"500px\" height=\"600px\" type=\"application/pdf\"></td></tr>
        <tr><td><a id=\"download\" href=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" download>
        <img style=\"width:15rem;height:15rem;\" src=\"./img/downloadArrow.png\" alt=\"download arrow image\">
        </a></td></tr><tr><td><label for=\"download\">Télécharger le PDF</label></td></tr>";
      }
      else
      {
        $annonce.= "<tr><td><img width=\"500px\" height=\"600px\"  src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" alt=\"Image annonce\"></td></tr>
        <tr><td><a id=\"download\" href=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" download>
        <img style=\"width:15rem;height:15rem;\" src=\"./img/downloadArrow.png\" alt=\"download arrow image\">
        </a></td></tr><tr><td><label for=\"download\">Télécharger l'image</label></td></tr>";
      }
      $annonce.= "</table>";
    }
    else
    $annonce.= "<p>Pas de média disponible</p>";
    $annonce.=              "</div>";
    $annonce.=            "</div>";
    $annonce.=          "</div>";
    $annonce.=        "</div>";
    $annonce.=      "</div>";
    $annonce.= "</section>";
  }
  else if($typeUser == "Chercheur")
  {
    $annonce.= "<section class=\"profile-detail\">";
    $annonce.=    "<div class=\"container\">";
    $annonce.=      "<div class=\"col-md-12\">";
    $annonce.=        "<div class=\"row\">";
    $annonce.=          "<div class=\"basic-information\">";
    $annonce.=            "<div style=\"width:100%;\"class=\"col-md-9 col-sm-9\">";
    $annonce.=              "<div class=\"profile-content\">";
    $annonce.=                "<h2>".$annonceInfo[4]."</h2>";
    $annonce.=              "</div>";
    $annonce.=              "</br>";
    $annonce.=              "<div class=\"panel panel-default\">";
    $annonce.=                "<div class=\"panel-heading\">";
    $annonce.=                  "<i class=\"fa fa-user fa-fw\"></i> Description";
    $annonce.=                "</div>";                       
    $annonce.=                "<div class=\"panel-body\">";
    $annonce.=                  "<p>".$annonceInfo[5]."</p>	";
    if(!HasUserAddedAnnonceToWishlist($annonceInfo[0],GetUserId()))
    {
      $annonce.=                   "<form action=\"annonce.php?idA=".$annonceInfo[0]."\" method=\"POST\" >";
      $annonce.=                   "<input name=\"addToWishlist\" type=\"submit\" class=\"form-control input-lg\" value=\"Ajouter annonce à wishlist\">";
      $annonce.=                   "</form>";
    }
    $annonce.=                "</div>";
    $annonce.=              "</div>";
    $annonce.=              "<div class=\"panel panel-default\">";
    if(!empty($annonceInfo[8]))
    {  
      $annonce.= "<table style=\"text-align: center;margin: auto\">";
      if($annonceInfo[8] == "pdf")
      {
        $annonce.= "<tr><td><embed src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" width=\"500px\" height=\"600px\" type=\"application/pdf\"></td></tr>
        <tr><td><a id=\"download\" href=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" download>
        <img style=\"width:15rem;height:15rem;\" src=\"./img/downloadArrow.png\" alt=\"download arrow image\">
        </a></td></tr><tr><td><label for=\"download\">Télécharger le PDF</label></td></tr>";
      }
      else
      {
        $annonce.= "<tr><td><img width=\"500px\" height=\"600px\"  src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" alt=\"Image annonce\"></td></tr>
        <tr><td><a id=\"download\" href=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" download>
        <img style=\"width:15rem;height:15rem;\" src=\"./img/downloadArrow.png\" alt=\"download arrow image\">
        </a></td></tr><tr><td><label for=\"download\">Télécharger l'image</label></td></tr>";
      }
      $annonce.= "</table>";
    }
    else
    $annonce.= "<p>Pas de média disponible</p>";
    $annonce.=              "</div>";
    $annonce.=            "</div>";
    $annonce.=          "</div>";
    $annonce.=        "</div>";
    $annonce.=      "</div>";
    $annonce.= "</section>";
  }
  echo $annonce;
}