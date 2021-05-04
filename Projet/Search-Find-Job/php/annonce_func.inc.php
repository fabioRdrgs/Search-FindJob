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

/**
 * Permet de mettre à jour le Job
 *
 * @param string $nomEntreprise
 * @param string $nomPoste
 * @param int $nombrePlace
 * @param string $adresse
 * @param string $siteweb
 * @param string $mail
 * @param string $apropos
 * @param <string, string> $listeCompetences
 * @param int $idJob
 * @param string $logo
 * @return bool Si true = Updated, sinon erreur SQL
 */
function UpdateJob($nomEntreprise, $nomPoste, $nombrePlace, $adresse, $siteweb, $mail, $apropos, $listeCompetences, $idJob, $logo)
{
  try {
    $sqlJob = "UPDATE `Job` SET ";
    $sqlJob .= "`nomEntreprise` = :NOMENTREPRISE, ";
    $sqlJob .= "`nomPoste` = :NOMPOSTE, ";
    $sqlJob .= "`nombrePlace` = :NOMBREPLACE, ";
    $sqlJob .= "`adresse` = :ADRESSE, ";
    $sqlJob .= "`siteWeb` = :SITEWEB, ";
    $sqlJob .= "`mail` = :MAIL, ";
    $sqlJob .= "`aPropos` = :APROPOS, ";
    $sqlJob .= "`logo` = :LOGO ";
    $sqlJob .= "WHERE (`id` = :ID)";

    db()->beginTransaction();
    static $psCreateApp = null;

    if ($psCreateApp == null)
      $psCreateApp = db()->prepare($sqlJob);

    $psCreateApp->bindParam(':NOMENTREPRISE', $nomEntreprise, PDO::PARAM_STR);
    $psCreateApp->bindParam(':NOMPOSTE', $nomPoste, PDO::PARAM_STR);
    $psCreateApp->bindParam(':NOMBREPLACE', $nombrePlace, PDO::PARAM_INT);
    $psCreateApp->bindParam(':ADRESSE', $adresse, PDO::PARAM_STR);
    $psCreateApp->bindParam(':SITEWEB', $siteweb, PDO::PARAM_STR);
    $psCreateApp->bindParam(':MAIL', $mail, PDO::PARAM_STR);
    $psCreateApp->bindParam(':APROPOS', $apropos, PDO::PARAM_STR);
    $psCreateApp->bindParam(':ID', $idJob, PDO::PARAM_INT);
    $psCreateApp->bindParam(':LOGO', $logo, PDO::PARAM_STR);
    $psCreateApp->execute();

    $sqlCompetence = "UPDATE `Job_Competence` SET";
    $sqlCompetence .= "`nomCompetence = :NOMCOMPETENCE, ";
    $sqlCompetence .= "`descriptionCompetence = :DESCRIPTIONCOMPETENCE";
    $sqlCompetence .= "WHERE (`idJob` = :IDJOB)";

    static $psCompetenceUpdate = null;
    if ($psCompetenceUpdate == null)
      $psCompetenceUpdate = db()->prepare($sqlCompetence);

    for ($i = 0; $i < count($listeCompetences); $i++) {
      $psCompetenceUpdate->bindParam(':NOMCOMPETENCE', $listeCompetences[$i][0], PDO::PARAM_STR);
      $psCompetenceUpdate->bindParam(':DESCRIPTIONCOMPETENCE', $listeCompetences[$i][1], PDO::PARAM_STR);
      $psCompetenceUpdate->bindParam(':IDJOB', $idJob, PDO::PARAM_INT);
      $psCompetenceUpdate->execute();
    }
    db()->commit();
    return true;
  } catch (PDOException $e) {
    db()->rollBack();
    return false;
  }
}
/**
 * Permet de récupérer les informations d'un job
 *
 * @param int $id
 * @return mixed Retourne false si erreur, null si pas de résultat ou un array contenant les informations
 */
function ReadJob($id)
{
  static $ps = null;
  $sql = 'SELECT * FROM `Job` WHERE id = :ID';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':ID', $id, PDO::PARAM_INT);

    if ($ps->execute())
      $answer = $ps->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
/**
 * Permet de récupérer les compétences d'un job
 *
 * @param int $idJob 
 * @return mixed Retourne false si erreur, null si pas de résultat ou un array contenant les informations
 */
function ReadCompetenceByJobId($idJob)
{
  static $ps = null;
  $sql = 'SELECT * FROM `Job_Competence` WHERE idJob = :IDJOB';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDJOB', $idJob, PDO::PARAM_INT);

    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
/**
 * Undocumented function
 *
 * @param [type] $id
 * @param [type] $limit
 * @return Array()
 */
function GetJobsByUserId($id, $limit)
{
  static $ps = null;
  $sql = "SELECT * FROM `Job` WHERE `idUtilisateur` = :IDUSER LIMIT :LIMIT";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDUSER', $id, PDO::PARAM_INT);
    $ps->bindParam(':LIMIT', $limit, PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
/**
 * Undocumented function
 *
 * @param [type] $limit
 * @return Array()
 */
function GetJobs($limit)
{
  static $ps = null;
  $sql = "SELECT * FROM `Job` LIMIT :LIMIT";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':LIMIT', $limit, PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}

/**
 * Permet de supprimer un job ainsi que les compétences liées à ce dernier
 *
 * @param int $idJob
 * @return void
 */
function DeleteJob($idJob, $idUser)
{
  static $ps = null;
  $sql = "DELETE FROM `Job` WHERE (`id` = :IDJOB) AND (`idUtilisateur`=:IDUSER);";
  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindParam(':IDJOB', $idJob, PDO::PARAM_INT);
    $ps->bindParam(':IDUSER', $idUser, PDO::PARAM_INT);
    $ps->execute();
    $answer = true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}
/**
 * Récupère les jobs correspondant à la recherche de l'utilisateur
 *
 * @param string $search Recherche du job en question
 * @param int $limit Nombre de jobs affichés à la fois
 * @param int $idUser (Optionnel) Id de l'utilisateur
 * @return Array(Array()) Array contenant les infos correspondant à chaque jobs
 */
function GetJobBySimpleSearch($search, $limit, $idUser)
{
  // Si un ID utilisateur est fournit => le teste, sinon, ne le teste pas({PARAMÈTRE} IS NULL OR [CONDITION] = {PARAMÈTRE})
  $sql = 'SELECT * FROM `Job` WHERE nomPoste LIKE :SEARCH AND (:IDUSER IS NULL OR idUtilisateur = :IDUSER) LIMIT :LIMIT';

  static $ps = null;

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':SEARCH', '%' . $search . '%', PDO::PARAM_STR);
    $ps->bindParam(':IDUSER', $idUser, PDO::PARAM_INT);
    $ps->bindParam(':LIMIT', $limit, PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}


/**
 * Permet d'afficher les jobs correspondant à une recherche simple ou avancée
 *
 * @param string $search
 * @param int $minSalary
 * @param int $maxSalary
 * @param int $skillCount
 * @param int $limit
 * @return Array(Array()) Array contenant les infos correspondant à chaque jobs
 */
function GetJobByAdvancedSearch($search, $minSalary, $maxSalary, $skillCount, $limit,$idUser)
{
  if($minSalary == "")
  $minSalary = null;
  if($maxSalary =="")
  $maxSalary=null;
  if($skillCount == "")
  $skillCount = 0;

  static $ps = null;

  //Pour chaque paramètres, s'il est fournit => le teste, sinon, ne le teste pas ({PARAMÈTRE} IS NULL OR [CONDITION] = {PARAMÈTRE})
  $sql = 
   'SELECT Job.id as \'idJob\',Job.*, Job_Competence.* FROM `Job` JOIN `Job_Competence` ON (`Job_Competence`.`idJob` = `Job`.`id`)
   WHERE nomPoste LIKE :SEARCH 
   AND (:MINSALARY IS NULL OR salaireMin >= :MINSALARY) 
   AND (:MAXSALARY IS NULL OR salaireMax <= :MAXSALARY) 
   AND (:IDUSER IS NULL OR idUtilisateur = :IDUSER)
    GROUP BY `Job`.`nomPoste` HAVING Count(nomCompetence) >= :SKILLCOUNT LIMIT :LIMIT';

  if ($ps == null) {
    $ps = db()->prepare($sql);
  }
  $answer = false;
  try {
    $ps->bindValue(':SEARCH', "%" . $search . "%", PDO::PARAM_STR);
    $ps->bindValue(':MINSALARY', $minSalary, PDO::PARAM_INT);
    $ps->bindValue(':MAXSALARY', $maxSalary, PDO::PARAM_INT);
    $ps->bindValue(':SKILLCOUNT', $skillCount, PDO::PARAM_INT);
    $ps->bindValue(':IDUSER', $idUser, PDO::PARAM_INT);
    $ps->bindParam(':LIMIT', $limit, PDO::PARAM_INT);
    if ($ps->execute())
      $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $answer;
}



function TestFiles($filesName)
{ $file= [];
  //Teste si des médias ont été fournis
  if (isset($filesName) && $filesName['error'] == 0)
  {
      $filename = uniqid();
      $ext = explode("/", $filesName["type"])[1];

        //Teste si format du média est valide
        if(in_array($ext, ["png", "jpg", "jpeg", "pdf"]) && $filesName['size'] < 15145728)
        {
          $file = [$filename, $ext];
        } 
        else 
        {
          echo "Veuillez sélectionner des fichiers valides!";
          return;
        }   
  } 
   else
    $file = null;

  return $file = $file[0].".".$file[1];
}



/**
 * Permet de soumettre sa candidature
 *
 * @param string $prenomCandidat
 * @param string $nomCandidat
 * @param string $adresseCandidat
 * @param string $dateNaissanceCandidat
 * @param string $motivationCandidat
 * @param string $photoCandidat {nom.extension} de la photo du candidat
 * @param string $cvCandidat {nom.extension} du CV du candidat
 * @param int $idUser
 * @param int $idJob
 * @param array $competencesJob Contient l'id de la compétence ainsi que le niveau 
 * @return void
 */
function SendApplication($prenomCandidat, $nomCandidat, $adresseCandidat, $dateNaissanceCandidat, $motivationCandidat, $photoCandidat, $cvCandidat, $idUser, $idJob, $competencesJob)
{
  try {
    $sqlSendApp = "INSERT INTO `Candidature` 
    (`prenom`, `nom`, `adresse`,`dateNaissance`, `motivation`, `photo`, `cv`, `idUtilisateur`, `idJob`) 
    VALUES (:PRENOM, :NOM, :ADRESSE, :DATENAISSANCE, :MOTIVATION, :PHOTO, :CV, :IDUSER, :IDJOB)";

    $sqlCompetenceApp = "INSERT INTO `Candidature_Competence`
    (`idCandidature`,`idJob_Competence`,`niveau`)
    VALUES (:IDCANDIDATURE, :IDJOBCOMPETENCE, :NIVEAU)";

    db()->beginTransaction();
    static $psCreateApp = null;

    if ($psCreateApp == null)
      $psCreateApp = db()->prepare($sqlSendApp);

    $psCreateApp->bindParam(':PRENOM', $prenomCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':NOM', $nomCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':ADRESSE', $adresseCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':DATENAISSANCE', $dateNaissanceCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':MOTIVATION', $motivationCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':PHOTO', $photoCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':CV', $cvCandidat, PDO::PARAM_STR);
    $psCreateApp->bindParam(':IDUSER', $idUser, PDO::PARAM_INT);
    $psCreateApp->bindParam(':IDJOB', $idJob, PDO::PARAM_INT);
    $psCreateApp->execute();

    $idCandidature = db()->lastInsertId();

    static $psCompetenceApp = null;
    if ($psCompetenceApp == null)
      $psCompetenceUpdate = db()->prepare($sqlCompetenceApp);

    for ($i = 0; $i < count($competencesJob); $i++) {
      $psCompetenceUpdate->bindParam(':IDJOBCOMPETENCE', $competencesJob[$i]["idJob"], PDO::PARAM_INT);
      $psCompetenceUpdate->bindParam(':NIVEAU', $competencesJob[$i]["niveau"], PDO::PARAM_INT);
      $psCompetenceUpdate->bindParam(':IDCANDIDATURE', $idCandidature, PDO::PARAM_INT);
      $psCompetenceUpdate->execute();
    }

    db()->commit();
    return true;
  } catch (PDOException $e) {
    db()->rollBack();
    return false;
  }
}

// ==========================================================================================================

/**
 * Fonction s'occupant d'afficher le composant jobs de browse-job
 *
 * @param int $limit Limite de jobs affiché à l'écran
 * @return void
 */
function Jobs($search, $minSalary, $maxSalary, $skillCount, $limit, $idUser)
{
    echo "<form id=\"jobForm\" method=\"POST\" action=\"browse-job.php";
    if (isset($_GET['idU'])) echo "?idU=" . $_GET['idU'];
    echo "\">
    <section class=\"jobs\">
        <div class=\"container\">
          <div class=\"row heading\">";
    if (isset($_GET['idU']))
      echo "<h2>Parcourez vos Jobs</h2><p>Vous avez crée plus de<span style=\"color:red;\"> 1 offre d'emploi</span></p>";
    else
      echo "<h2>Trouvez votre Job</h2><p>Plus de xxx jobs disponibles!</p> 
            <!-- Default switch -->";
    echo "<div class=\"custom-control custom-switch\">";
    echo "<input ";
    if (isset($_POST['searchMode']))
      echo "checked";
    echo " type=\"checkbox\" class=\"custom-control-input\" name=\"searchMode\"id=\"searchMode\">
              <label class=\"custom-control-label\" for=\"searchMode\">Mode de recherche avancé</label>";

    echo "</div>
            <div class=\"row top-pad\">
                <div class=\"filter\">
                  <div class=\"col-md-2 col-sm-3\">
                  <p>Chercher par:</p>
                  </div>
                  <!--Filtres Jobs-->
                  <div class=\"col-md-10 col-sm-9 pull-right\">
                  <ul class=\"filter-list\">
                    <li id=\"search\">             
                    </li>	               
                  </ul>	
                </div>
              </div>
            </div></div>
        ";
    if (isset($_POST['submit']))
      ShowJobsBySearch($search, $minSalary, $maxSalary, $skillCount, $limit, $idUser,$_POST['searchMode']);
    else if (isset($_GET['idU']))
      ShowJobsByIdUser($_GET['idU'], $limit);
    else {
      ShowJobs($limit);
    }
    echo "
          <!--Afficher plus de jobs-->
          <div class=\"row\">
            <input type=\"submit\" id=\"moreJobs\" name=\"moreJobs\" class=\"btn browse-btn\" value=\"Afficher plus de Jobs\" />
          </div>   
  </section>
      </form>";
}

/**
 * Retourne un array contenant les informations du job et ses compétences
 *
 * @param int $id
 * @return <array,array> Contient un array d'informatons du job et un array de compétences
 */
function GetJobInfo($id)
{
  return ["job" => ReadJob($id), "competences" => ReadCompetenceByJobId($id)];
}
/**
 * Affiche tous les jobs
 * @param int $limit Limite de jobs affiché à l'écran
 * @return void
 */
function ShowJobs($limit)
{
  $jobs = GetJobs($limit);
  foreach ($jobs as $job) {
    echo "<div class=\"companies\">
        <div class=\"company-list\">
        <a href=\"job-info.php?idJ=" . $job['id'] . "\" > 
         <div class=\"row\">
                <div class=\"col-md-2 col-sm-2\">
                    <div class=\"company-logo\">
                        <img src=\"";
    if ($job['logo'] != "")
      echo "tmp/" . $job['logo'];
    else
      echo "img/NoImage.png";
    echo "\" class=\"img-responsive\" alt=\"Logo de l'entreprise\" />
                    </div>
                </div>
                <div class=\"col-md-8 col-sm-8\">
                    <div class=\"company-content\">
                        <h3>" . $job['nomPoste'] . "</h3>
                        <p><span class=\"company-name\"><i class=\"fa fa-briefcase\"></i>" . $job['nomEntreprise'] . "</span><span class=\"company-location\"><i class=\"fa fa-map-marker\"></i> " . $job['adresse'] . "</span><span class=\"package\"><i class=\"fa fa-money\"></i>" . $job['salaireMin'] . ".- à " . $job['salaireMax'] . ".-"  . "</span></p>
                    </div>
                </div>
                <div class=\"col-md-2 col-sm-2\">
                "; //<a href=\"job-info.php?idJ=".$job['id']."\">Afficher Job</a>
    echo "</div>
            </div></a>
        </div>										
    </div>";
  }
}
/**
 * Affiche tous les jobs disponibles par id Utilisateur (Créateur du job)
 *
 * @param int $id
 * @param int $limit Limite de jobs affiché à l'écran
 * @return void
 */
function ShowJobsByIdUser($id, $limit)
{
  $jobs = GetJobsByUserId($id, $limit);
  foreach ($jobs as $job) {
    echo "<div class=\"companies\">
        <a href=\"job-info.php?idJ=" . $job['id'] . "\" >  <div class=\"company-list\">
         
          <div class=\"row mask\">
                <div class=\"col-md-2 col-sm-2\">
                    <div class=\"company-logo\">
                        <img src=\"";
    if ($job['logo'] != "")
      echo "tmp/" . $job['logo'];
    else
      echo "img/NoImage.png";
    echo "\" class=\"img-responsive\" alt=\"Logo de l'entreprise\" />
                    </div>
                </div>
                <div class=\"col-md-8 col-sm-8\">
                    <div class=\"company-content\">
                        <h3>" . $job['nomPoste'] . "</h3>
                        <p><span class=\"company-name\"><i class=\"fa fa-briefcase\"></i>" . $job['nomEntreprise'] . "</span><span class=\"company-location\"><i class=\"fa fa-map-marker\"></i> " . $job['adresse'] . "</span><span class=\"package\"><i class=\"fa fa-money\"></i>" . $job['salaireMin'] . ".- à " . $job['salaireMax'] . ".-"  . "</span></p>
                    </div>
                </div>
                <div class=\"col-md-2 col-sm-2\">
                  "; //<a href=\"job-info.php?idJ=".$job['id']."\">Afficher Job</a>
    echo "<a href=\"deleteJob.php?idJ=" . $job['id'] . "&idU=" . $_SESSION['user']['idUser'] . "\">Supprimer le Job</a>
                </div>
            </div>           
        </div>	</a>									
    </div>";
  }
}

/**
 * Affiche tous les jobs par rapport à la recherche avancée ou simple de l'utilisateur
 *
 * @param string $search Recherche de l'utilisateur
 * @param int $minSalary Salaire Minimum désiré
 * @param int $maxSalary Salaire Maximum désiré
 * @param int $skillCount Nombre de compétences désiré
 * @param int $limit Limite de jobs affiché à l'écran
 * @return void
 */
function ShowJobsBySearch($search, $minSalary, $maxSalary, $skillCount, $limit, $idUser,$searchmode)
{

  //Si en mode recherche simple ou qu'aucun autre champ n'est rempli même en recherche avancée, effectue une recherche simple
  if (is_null($searchmode) || $minSalary == "" && $maxSalary == "" && $skillCount == "") {
    $jobs = GetJobBySimpleSearch($search, $limit, $idUser);
  } 
  else 
  {
    $jobs = GetJobByAdvancedSearch($search, $minSalary, $maxSalary, $skillCount, $limit, $idUser);
  }
  //Affiche les jobs
  foreach ($jobs as $job) {
    echo "<div class=\"companies\">
      <a href=\"job-info.php?idJ=" . $job['idJob'] . "\" >  <div class=\"company-list\">
       
        <div class=\"row mask\">
              <div class=\"col-md-2 col-sm-2\">
                  <div class=\"company-logo\">
                      <img src=\"";
    if ($job['logo'] != "")
      echo "tmp/" . $job['logo'];
    else
      echo "img/NoImage.png";
    echo "\" class=\"img-responsive\" alt=\"Logo de l'entreprise\" />
                  </div>
              </div>
              <div class=\"col-md-8 col-sm-8\">
                  <div class=\"company-content\">
                      <h3>" . $job['nomPoste'] . "</h3>
                      <p><span class=\"company-name\"><i class=\"fa fa-briefcase\"></i>" . $job['nomEntreprise'] . "</span><span class=\"company-location\"><i class=\"fa fa-map-marker\"></i> " . $job['adresse'] . "</span><span class=\"package\"><i class=\"fa fa-money\"></i>" . $job['salaireMin'] . ".- à " . $job['salaireMax'] . ".-"  . "</span></p>
                  </div>
              </div>
          </div>
          
      </div>	</a>									
  </div>";
  }
}


function ShowCompetencesByJob($idJob)
{
  $competences = ReadCompetenceByJobId($idJob);

  echo "<div class=\"panel panel-default\">
  <div class=\"panel-heading\">
    <i class=\"fa fa-leaf fa-fw\"></i> Compétences :
  </div>
  <!-- /.panel-heading -->
  <div class=\"panel-body\">
    <p>Compétences requises pour ce job</p>
    <ul>";
  foreach ($competences as $competence) {
    echo "<li><b>" . $competence['nomCompetence'] . " : </b> " . $competence['descriptionCompetence'] . "</li>";
  }

  echo " </ul>
  </div>
</div>";
}
