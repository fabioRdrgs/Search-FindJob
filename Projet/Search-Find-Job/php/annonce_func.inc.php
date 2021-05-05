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

function GetAnnoncesFromSearch($titreAnnonce, $descAnnonce,$motsClesSelect,$limit,$idUtilisateur)
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
  AND (:IDUTILISATEUR IS NULL OR utilisateurs_id = :IDUTILISATEUR) 
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


function ShowAnnonces($idUtilisateur)
{
  //Si l'on souhaite afficher la vue Annonces "Mes Annonces" ou celle de "Annonces"
  if(!is_null($idUtilisateur))
  {

  }
  else
  {

  }
}
