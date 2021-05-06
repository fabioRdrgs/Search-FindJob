<?php
require_once 'db.inc.php';

// SQL
// ==========================================================================================================

/**
 * Permet de créer un nouveau Job
 *
 * @param string $nomEntreprise
 * @param string $nomPoste
 * @param int $nombrePlace
 * @param string $adresse
 * @param string $siteweb
 * @param string $mail
 * @param string $apropos
 * @param int $idUtilisateur
 * @param <string, string> $listeCompetences Peut être null -> Pas de compétences ajoutées
 * @param string $logo Peut être vide -> Pas de logo
 * @return bool Si true = Job Created, sinon erreur SQL
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

function GetFollowersByIdAnnonce()
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

function GetAnnoncesFromSearchChercheur($titreAnnonce, $descAnnonce,$motsClesSelect,$limit)
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
  WHERE titre LIKE :TITREANNONCE 
  AND description LIKE :DESCANNONCE 
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
$sql.="  ORDER BY date_publication ASC LIMIT :LIMIT";


  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':TITREANNONCE', "%" . $titreAnnonce . "%", PDO::PARAM_STR);
    $ps->bindValue(':DESCANNONCE', "%" .$descAnnonce. "%", PDO::PARAM_STR);
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
function GetAnnoncesFromSearchAnnonceur($titreAnnonce, $descAnnonce,$motsClesSelect,$limit,$idUtilisateur)
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
  WHERE titre LIKE :TITREANNONCE 
  AND description LIKE :DESCANNONCE 
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
$sql.="  ORDER BY date_publication ASC LIMIT :LIMIT";


  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':TITREANNONCE', "%" . $titreAnnonce . "%", PDO::PARAM_STR);
    $ps->bindValue(':DESCANNONCE', "%" .$descAnnonce. "%", PDO::PARAM_STR);
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

function ShowAnnoncesChercheur($titre,$description,$motsClesSelectPost,$limit)
{
  $annonces = GetAnnoncesFromSearchChercheur($titre,$description,$motsClesSelectPost,$limit);
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

function ShowAnnoncesAnnonceur($titre,$description,$motsClesSelectPost,$limit,$idUtilisateur)
{
  $annonces = GetAnnoncesFromSearchAnnonceur($titre,$description,$motsClesSelectPost,$limit,$idUtilisateur);
	if($annonces != false)
	foreach($annonces as $annonce)
	{
    $keywords = GetKeywordsByIdAnnonce($annonce[0]);
    $followers = GetFollowersByIdAnnonce($annonce[0]);
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
    $affichageAnnonce.= "<p><span>";
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
		$affichageAnnonce .= "</div></a>";
		echo $affichageAnnonce; 
	}
}

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
        $annonce.= "<li><span>".$follower[0]."</span>".$follower[1]."</li>";
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
    if($annonceInfo[8] == "pdf")
    {
      $annonce.= "<embed src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" width=\"500\" height=\"375\" type=\"application/pdf\">";
    }
    else
    {
      $annonce.= "<img src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" alt=\"Image annonce\">";
    }
    $annonce.=              "</div>";
    $annonce.=            "</div>";
    $annonce.=          "</div>";
    $annonce.=        "</div>";
    $annonce.=      "</div>";
    $annonce.= "</section>";
  }
  else
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
    $annonce.=                "</div>";
    $annonce.=              "</div>";
    $annonce.=              "<div class=\"panel panel-default\">";
    if($annonceInfo[8] == "pdf")
    {
      $annonce.= "<embed src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" width=\"500\" height=\"375\" type=\"application/pdf\">";
    }
    else
    {
      $annonce.= "<a href=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\"><img style=\" display: block;margin-left: auto;margin-right: auto;\"src=\"".$annonceInfo[6].$annonceInfo[7].".".$annonceInfo[8]."\" alt=\"Image annonce\"></a>";
    }
    $annonce.=              "</div>";
    $annonce.=            "</div>";
    $annonce.=          "</div>";
    $annonce.=        "</div>";
    $annonce.=      "</div>";
    $annonce.= "</section>";
  }
  echo $annonce;
}