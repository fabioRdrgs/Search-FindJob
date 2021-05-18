<?php
require_once 'db.inc.php';
include_once 'wishlist_func.inc.php';
// SQL
// ==========================================================================================================

/**
 * Permet de créer une annonce ainsi qu'ajouter les mots-clés fournis à cette dernière
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
    //Début de la transaction
    db()->beginTransaction();

    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $psAnnonce = null;
    $sqlAnnonce = "INSERT INTO `annonces` (`titre`,`description`,`date_debut`,`date_fin`,`media_path`,`media_nom`,`media_type`,`utilisateurs_id`)
    VALUES (:TITRE,:DESCRIPTION,:DATEDEBUT,:DATEFIN,:MEDIAPATH,:MEDIANOM,:MEDIATYPE,:UTILISATEURSID)";

    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($psAnnonce == null)
      $psAnnonce = db()->prepare($sqlAnnonce);

    //Affecte tous les paramètres avec la variable correspondante
    $psAnnonce->bindParam(':TITRE', $nomAnnonce, PDO::PARAM_STR);
    $psAnnonce->bindParam(':DESCRIPTION', $description, PDO::PARAM_STR);
    $psAnnonce->bindParam(':DATEDEBUT', $dateDebut, PDO::PARAM_STR);
    $psAnnonce->bindParam(':DATEFIN', $dateFin, PDO::PARAM_STR);
    $psAnnonce->bindParam(':MEDIAPATH', $dir, PDO::PARAM_STR);
    $psAnnonce->bindParam(':MEDIANOM', $file, PDO::PARAM_STR);
    $psAnnonce->bindParam(':MEDIATYPE', $type, PDO::PARAM_STR);
    $psAnnonce->bindParam(':UTILISATEURSID', $idUtilisateur, PDO::PARAM_INT);
    $psAnnonce->execute();

    //Récupère l'id de l'annonce créée
    $annonceId = db()->lastInsertId();

    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $psKeywords = null;
    $sqlKeywords = "INSERT INTO `annonces_has_keywords` (`annonces_id`,`keywords_id`) 
    VALUES (:ANNONCESID,:KEYWORDSID)";

    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if($psKeywords == null)
     $psKeywords = db()->prepare($sqlKeywords);

    //Exécute et assigne l'id du mot-clé ainsi que son l'id de l'annonce pour chaque IDs dans l'array array de mots-clés
     foreach($keywords as $keyword)
     {
       $psKeywords->bindParam(':ANNONCESID',$annonceId,PDO::PARAM_INT);
       $psKeywords->bindParam(':KEYWORDSID',$keyword,PDO::PARAM_INT);
       $psKeywords->execute();
     }  
    //Termine la transaction en la committant 
    db()->commit();

    return true;
  } 
  //Si une erreur survient, rollback le tout, echo le message d'erreur et retourne false   
  catch (PDOException $e) 
  {
    echo $e;
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
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = "DELETE FROM `annonces` WHERE (`id` = :IDANNONCE) AND (`utilisateurs_id`=:IDUSER);";
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;

  try {
    //Assigne chaque paremètres et leur variable correspondante
    $ps->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
    $ps->bindParam(':IDUSER', $idUser, PDO::PARAM_INT);
    $ps->execute();
    //Si plus de 0 annonces ont été supprimées, affecte true au résultat
    if($ps->rowCount() > 0)
    $answer = true;
  } 
  //Si une erreur survient,  echo le message d'erreur
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
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
    //Début de la transaction
    db()->beginTransaction();
    //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
    static $psAnnonce = null;
    $sqlAnnonce = "UPDATE `annonces` SET 
    date_debut = :DATEDEBUT, 
    date_fin = :DATEFIN, 
    titre = :TITRE, 
    description = :DESCRIPTION WHERE (id = :IDANNONCE)";

    //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
    if ($psAnnonce == null)
      $psAnnonce = db()->prepare($sqlAnnonce);

      //Affecte tous les paramètres avec la variable correspondante
      $psAnnonce->bindParam(':TITRE', $nomAnnonce, PDO::PARAM_STR);
      $psAnnonce->bindParam(':DESCRIPTION', $description, PDO::PARAM_STR);
      $psAnnonce->bindParam(':DATEDEBUT', $dateDebut, PDO::PARAM_STR);
      $psAnnonce->bindParam(':DATEFIN', $dateFin, PDO::PARAM_STR);
      $psAnnonce->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
      $psAnnonce->execute();

    //S'assure que la variable supprimer média n'est pas vide, si elle n'est pas vide cela indique qu'il faut supprimer le média présent dans l'annonce actuelle
    if(!empty($supprimerMediaActuel))
    {
      static $psDeleteCurrentMedia = null;
      if($psDeleteCurrentMedia == null)
       $psDeleteCurrentMedia = db()->prepare("UPDATE `annonces` SET media_path = NULL, media_nom = NULL, media_type = NULL WHERE id = :IDANNONCE");
       $psDeleteCurrentMedia->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
       $psDeleteCurrentMedia->execute();

    }
    //S'assure que les variables chemin, nom de fichier et type ne sont pas vide, si elle n'est pas vide cela indique qu'il faut affecter ces données à l'annonce actuelle
    if(!empty($dir) && !empty($filename) && !empty($type))
    {
        //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
        static $psMedia = null;
        $sqlMedia="UPDATE `annonces` SET media_path = :MEDIAPATH, media_nom = :MEDIANOM, media_type = :MEDIATYPE  WHERE (id = :IDANNONCE)";
        //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
        if($psMedia == null)
        $psMedia = db()->prepare($sqlMedia);
        //Affecte tous les paramètres avec la variable correspondante
        $psMedia->bindParam(':MEDIAPATH', $dir, PDO::PARAM_STR);
        $psMedia->bindParam(':MEDIANOM', $filename, PDO::PARAM_STR);
        $psMedia->bindParam(':MEDIATYPE', $type, PDO::PARAM_STR);
        $psMedia->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
        //Exécute la requête
        $psMedia->execute();
    }
    //S'assure que l'array de mots-clés n'est pas vide supprimer média n'est pas vide, si elle n'est pas vide cela indique qu'il faut supprimer les mots-clés de l'annonce et insérer les nouveaux
    if(!empty($keywords))
    {
      //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
      static $psKeywordsDelete = null;
      $sqlKeywordsDelete="DELETE FROM `annonces_has_keywords` WHERE annonces_id = :IDANNONCE";
      //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
      if($psKeywordsDelete == null)
       $psKeywordsDelete = db()->prepare($sqlKeywordsDelete);
       //Affecte tous les paramètres avec la variable correspondante
       $psKeywordsDelete->bindParam(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
       //Exécute la requête
       $psKeywordsDelete->execute();
       //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
       static $psKeywordsAdd = null;
       $sqlKeywordsAdd = "INSERT INTO `annonces_has_keywords` (`annonces_id`,`keywords_id`) 
       VALUES (:ANNONCESID,:KEYWORDSID)";

       //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
       if($psKeywordsAdd == null)
        $psKeywordsAdd = db()->prepare($sqlKeywordsAdd);

        //Exécute et assigne l'id du mot-clé ainsi que l'id de l'annonce pour chaque Mot-Clé dans l'array array de mots-clés
        foreach($keywords as $keyword)
        {
          $psKeywordsAdd->bindParam(':ANNONCESID',$idAnnonce,PDO::PARAM_INT);
          $psKeywordsAdd->bindParam(':KEYWORDSID',$keyword,PDO::PARAM_INT);
          $psKeywordsAdd->execute();
        }   
          
    }
    //Termine la transaction en la committant 
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
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT * FROM `keywords`';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } 
  //Si une exception survient, echo le message d'erreur
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
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
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT keywords.id, keywords.label FROM `keywords` JOIN `annonces_has_keywords` ON (keywords.id = annonces_has_keywords.keywords_id) WHERE annonces_has_keywords.annonces_id = :IDANNONCE';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindValue(':IDANNONCE', $idAnnonce, PDO::PARAM_INT);
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } 
  //Si une exception survient, echo le message d'erreur
  catch (PDOException $e) 
  {
    //Si une erreur survient,  echo le message d'erreur
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
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
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT * FROM `annonces` WHERE id = :IDANNONCE';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetch(PDO::FETCH_NUM);
  } 
  //Si une exception survient, echo le message d'erreur
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
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
  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  $sql = 'SELECT utilisateurs.login, wishlists.date FROM `annonces` JOIN `wishlists` ON (annonces.id = wishlists.annonces_id) JOIN `utilisateurs` ON (wishlists.utilisateurs_id = utilisateurs.id) WHERE annonces.id = :IDANNONCE';
  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindParam(":IDANNONCE",$idAnnonce,PDO::PARAM_INT);
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } 
  //Si une exception survient, echo le message d'erreur
  catch (PDOException $e)
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
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
  //Teste si des mots-clés ont été fournis, si oui, affecte le compte de mots-clés à la variable sinon affecte 0
  if(empty($motsClesSelect))
  $countKeywords = 0;
  else
  $countKeywords = count($motsClesSelect);

  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  //Pour chaque paramètres, s'il est fournit => le teste, sinon, ne le teste pas ({PARAMÈTRE} IS NULL OR [CONDITION] = {PARAMÈTRE})
  $sql = "SELECT DISTINCT annonces.*
  FROM annonces 
  JOIN annonces_has_keywords 
  ON (annonces.id = annonces_has_keywords.annonces_id) 
  WHERE (titre LIKE :RECHERCHE 
  OR description LIKE :RECHERCHE) 
  AND date_debut <= CURRENT_DATE AND date_fin >= CURRENT_DATE ";

  //Va ajouter autant de test qu'il y a de mots-clés à la requête
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

  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
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
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } 
  //Si une exception survient, echo le message d'erreur
  catch (PDOException $e) 
  {
    echo $e->getMessage();
  }
  //Renvoie le résultat de la requête une fois terminé
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
  //Teste si des mots-clés ont été fournis, si oui, affecte le compte de mots-clés à la variable sinon affecte 0
  if(is_null($motsClesSelect))
  $countKeywords = 0;
  else
  $countKeywords = count($motsClesSelect);

  //Déclaration du prepare statement en null s'il n'a pas déjà été instancié avant
  static $ps = null;
  //Pour chaque paramètres, s'il est fournit => le teste, sinon, ne le teste pas ({PARAMÈTRE} IS NULL OR [CONDITION] = {PARAMÈTRE})
  $sql = "SELECT DISTINCT annonces.*
  FROM annonces 
  JOIN annonces_has_keywords 
  ON (annonces.id = annonces_has_keywords.annonces_id) 
  WHERE (titre LIKE :RECHERCHE 
  OR description LIKE :RECHERCHE)  
  AND (:IDUTILISATEUR IS NULL OR utilisateurs_id = :IDUTILISATEUR)";

  //Va ajouter autant de test qu'il y a de mots-clés à la requête
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

  //Si le prepare statement n'a pas été instancié avant, il sera null et donc aura besoin d'être préparé à nouveau
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    //Affecte tous les paramètres avec la variable correspondante
    $ps->bindValue(':RECHERCHE', "%" . $recherche . "%", PDO::PARAM_STR);
    $ps->bindParam(':IDUTILISATEUR', $idUtilisateur, PDO::PARAM_INT);
    $ps->bindParam(':LIMIT',$limit,PDO::PARAM_INT);
    //Affecte les paramètres de mots-clés autant fois qu'il y a de mots-clés de fournit
    for ($i=0; $i <  $countKeywords; $i++) { 
      $ps->bindParam(':KEYWORD'.$i, $motsClesSelect[$i], PDO::PARAM_INT); 
    }
    //Si la requête réussi sans soucis, fetch tous les résultats dans $answer
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_NUM);
  } 
  //Si une exception survient, echo le message d'erreur
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
 * Permet d'afficher un select multiple avec recherche live
 *
 * @param array $motsClesSelectPost
 * @return void Echo le select multiple créé
 */
function ShowSelectKeywords($motsClesSelectPost)
{
  //Si aucun mot-clé n'est fournit, définit la variable comme un array vide
  if(is_null($motsClesSelectPost))
  $motsClesSelectPost = [];
  //Récupère tous les mots-clés
  $keywords= GetKeywords();
  //Affecte à la variable tout le contenu HTML voulu
  $select="";
  $select.= "<div class=\"row-fluid\">";
  $select.=	"<select id=\"motsClesSelect\" name=\"motsClesSelect[]\" multiple class=\"selectpicker\" data-show-subtext=\"true\" data-live-search=\"true\">";
  //Pour chaque mots-clés, l'affiche dans le select
  foreach($keywords as $keyword)
  {
    if(in_array($keyword[0],$motsClesSelectPost))
    $select.="<option selected value=\"".$keyword[0]."\">".$keyword[1]."</option>";
    else
    $select.="<option value=\"".$keyword[0]."\">".$keyword[1]."</option>";
  }
  $select.=			"</select>";
  $select.=			"</div>";
  //Echo le contenu HTML du select multiple
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
  //Récupère toutes les annonces résultant de la recherche du chercheur
  $annonces = GetAnnoncesFromSearchChercheur($recherche,$motsClesSelectPost,$limit);
  $affichageAnnonce = "";
  //S'assure que des annonces ont bien été trouvées
	if(!empty($annonces))
  {
    //Affiche chaque annonces à l'aide d'un echo
    foreach($annonces as $annonce)
    {
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
    }
  }
  //Sinon affiche un message
  else
    $affichageAnnonce.="<p style=\"text-align:center;\">Aucune annonce ne correspond aux paramètres de recherche</p>";
    echo $affichageAnnonce; 
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
  //Récupère toutes les annonces résultant de la recherche de l'annonceur
  $annonces = GetAnnoncesFromSearchAnnonceur($recherche,$motsClesSelectPost,$limit,$idUtilisateur);
  $affichageAnnonce = "";
  //S'assure que des annonces ont bien été trouvées
	if(!empty($annonces))
  {
    //Affiche chaque annonces à l'aide d'un echo
    foreach($annonces as $annonce)
    {
      //Récupère tous les mots-clés et les followers de l'annonce
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
      //Affiche les mots-clés s'il y en a
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
      //Affiche les followers s'il y en a
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
  //Sinon affiche un message
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
  //Récupère toutes les infos de l'annonce en question
  $annonceInfo = GetAnnonceInfo($idAnnonce);
  //S'assure que l'annonce que l'utilisateur souhaite consulter existe, sinon le redirige vers les annonces
  if(empty($annonceInfo))
  {
    header('location: annonces.php?alert=error&num=20');
    die('Annonce inexistante');
  }
  else
  {
    $annonce = "";
    //Affiche la vue d'infos d'annonce pour l'annonceur
    if($typeUser =="Annonceur")
    {    
      //Récupère tous les followers de l'annonce
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
      //Affiche les followers de l'annonce s'il y en a
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
    
      //Affiche le média de l'annonce s'il y en a un
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
    //Affiche la vue d'infos d'annonce pour le chercheur
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
      //Affihe un bouton pour ajouter l'annonce à la wishlist de l'utilisateur s'il ne l'a pas déjà fait
      if(!HasUserAddedAnnonceToWishlist($annonceInfo[0],GetUserId()))
      {
        $annonce.=                   "<form action=\"annonce.php?idA=".$annonceInfo[0]."\" method=\"POST\" >";
        $annonce.=                   "<input name=\"addToWishlist\" type=\"submit\" class=\"form-control input-lg\" value=\"Ajouter annonce à wishlist\">";
        $annonce.=                   "</form>";
      }
      $annonce.=                "</div>";
      $annonce.=              "</div>";
      $annonce.=              "<div class=\"panel panel-default\">";
      //Affiche le média de l'annonce s'il y en a un
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
    //Echo le contenu HTML de l'annonce
    echo $annonce;
  }
  
}

function CheckMedia()
{
  if(isset($_FILES["media"]))
  {
    //Si un fichier est fournit (Image ou PDF)
    if($_FILES["media"]['error'] == 0)
    {
      //Si le fichier fourni est plus petit que 20Mo
      if($_FILES["media"]["size"]<=20000000)
      {
        $filename = uniqid();
        $dir = "./tmp/";
        $type = explode("/",$_FILES["media"]["type"])[1];

        if(!in_array($type,["png","bmp","jpg","jpeg","pdf"]))
        {		
          SetAlert("error",8); 
        }
      }
    }
    else
    {
      $type = null;
      $dir = null;
      $filename = null;
    }
  }

  return ([$dir,$filename,$type]);
}

function UploadMedia($dir,$filename,$type)
{
  //Si l'upload de l'image réussi, redirige vers la page mes annonces, sinon affiche une erreur
  if(move_uploaded_file($_FILES["media"]["tmp_name"],$dir.$filename.".".$type)) 
    return true; 
  else
    return false;
  
}